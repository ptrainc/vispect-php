'use strict';
const $ = id => document.getElementById(id);
const input = $('gambar'), preview = $('preview'), button = $('analyze');
let model = null, selected = null, busy = false, imageReady = false, objectUrl = null, selectionVersion = 0;
function feedback(message, error = false) {
 $('feedback').hidden = !message;
 $('feedback').textContent = message;
 $('feedback').className = error ? 'error' : '';
}
function controls() {
 button.disabled = busy || !model || !imageReady;
 button.textContent = busy ? 'Menganalisis…' : (model ? 'Analisis gambar →' : 'Model belum siap');
 input.disabled = busy;
 $('reset-image').disabled = busy;
}
async function loadModel() {
 $('retry-model').hidden = true;
 $('model-state').textContent = 'Memuat model AI…';
 try {
  if (!window.tmImage) throw new Error('Library AI belum dimuat. Periksa internet, lalu muat ulang halaman.');
  const loaded = await tmImage.load('./ai/model.json', './ai/metadata.json');
  if (loaded.getTotalClasses() !== 2) throw new Error('Interface ini menggunakan model OK/NG dua kelas.');
  model = loaded;
  $('model-state').textContent = 'Model siap · OK / NG';
 } catch (error) {
  $('model-state').textContent = error.message || 'Model gagal dimuat.';
  $('retry-model').hidden = false;
 }
 controls();
}
async function choose(file) {
 if (busy) return;
 const version = ++selectionVersion;
 selected = null; imageReady = false; controls(); feedback('');
 preview.hidden = true; preview.removeAttribute('src'); $('upload-hint').hidden = false;
 $('file-name').textContent = 'Belum ada gambar dipilih'; $('reset-image').hidden = true;
 if (objectUrl) URL.revokeObjectURL(objectUrl);
 objectUrl = null;
 $('result-content').hidden = true;
 $('result-empty').hidden = false;
 $('result-label').textContent = 'MENUNGGU GAMBAR';
 $('result-status').textContent = '';
 $('result-status').className = 'badge neutral';
 $('confidence').textContent = '0.00';
 $('result-source').textContent = '';
 for (const name of ['ok', 'ng']) {
  $(name + '-value').textContent = '0.00%';
  $(name + '-bar').value = 0;
 }
 if (!file) { input.value = ''; return; }
 if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 2000000) {
  input.value = ''; feedback('Pilih JPG, PNG, atau WEBP berukuran maksimal 2 MB.', true); return;
 }
 objectUrl = URL.createObjectURL(file); preview.src = objectUrl;
 try {
  await preview.decode();
  if (version !== selectionVersion) return;
  selected = file; imageReady = true; preview.hidden = false; $('upload-hint').hidden = true;
  $('result-label').textContent = 'BELUM DIANALISIS';
  $('file-name').textContent = file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB';
  $('reset-image').hidden = false;
 } catch {
  if (version !== selectionVersion) return;
  input.value = ''; feedback('Gambar tidak dapat dibaca. Pilih file lain.', true);
 }
 controls();
}
input.addEventListener('change', () => choose(input.files[0]));
$('reset-image').addEventListener('click', () => input.click());
$('retry-model').addEventListener('click', () => window.tmImage ? loadModel() : location.reload());
const drop = $('dropzone');
['dragenter','dragover'].forEach(event => drop.addEventListener(event, e => { e.preventDefault(); if (!busy) drop.classList.add('dragging'); }));
['dragleave','drop'].forEach(event => drop.addEventListener(event, e => { e.preventDefault(); drop.classList.remove('dragging'); }));
drop.addEventListener('drop', e => {
 if (busy || !e.dataTransfer.files.length) return;
 input.files = e.dataTransfer.files;
 choose(e.dataTransfer.files[0]);
});
$('inspection-form').addEventListener('submit', async e => {
 e.preventDefault();
 if (busy || !model || !selected || !imageReady) return;
 busy = true; controls(); feedback('');
 const file = selected;
 let hasPrediction = false;
 try {
  const predictions = await model.predict(preview);
  const values = {};
  for (const p of predictions) {
   const name = p.className.toUpperCase().trim();
   if (['OK','NG'].includes(name)) values[name] = p.probability * 100;
  }
  if (!Number.isFinite(values.OK) || !Number.isFinite(values.NG)) throw new Error('Label model harus OK dan NG.');
  const status = values.OK > values.NG ? 'OK' : 'NG';
  const confidence = Math.max(values.OK, values.NG);
  $('result-empty').hidden = true; $('result-content').hidden = false;
  $('result-status').textContent = status; $('result-status').className = 'badge ' + status.toLowerCase();
  $('confidence').textContent = confidence.toFixed(2);
  for (const name of ['ok','ng']) {
   $(name + '-value').textContent = values[name.toUpperCase()].toFixed(2) + '%';
   $(name + '-bar').value = values[name.toUpperCase()];
  }
  $('result-source').textContent = file.name;
  $('result-label').textContent = 'BELUM TERSIMPAN';
  hasPrediction = true;
  feedback('Analisis selesai. Menyimpan hasil…');
  const body = new FormData();
  body.append('gambar', file);
  body.append('status', status);
  body.append('akurasi', confidence.toFixed(2));
  body.append('persentase_ok', values.OK.toFixed(2));
  body.append('persentase_ng', values.NG.toFixed(2));
  // Compatible with the existing PHP upload endpoint and database schema.
  const response = await fetch('proses/upload.php', { method:'POST', body });
  const text = await response.text();
  if (!response.ok || !text.trimStart().startsWith('Upload berhasil!')) throw new Error('Hasil belum berhasil disimpan. Periksa koneksi database dan folder uploads.');
  $('result-label').textContent = 'TERSIMPAN';
  feedback('Pemeriksaan berhasil disimpan.');
 } catch(error) {
  feedback((hasPrediction ? 'Prediksi tersedia, tetapi penyimpanan belum terkonfirmasi. ' : '') + (error.message || 'Terjadi kesalahan.'), true);
 } finally { busy = false; controls(); }
});
// Clear restored file inputs on a fresh load; persisted navigation also starts empty.
input.value = '';
choose(null);
window.addEventListener('pageshow', event => {
 if (event.persisted) { busy = false; input.value = ''; choose(null); }
});
loadModel();
