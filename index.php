<?php
require __DIR__ . '/includes/view.php';
start_page();
?>
<div class="inspection-grid">
<section class="panel"><div class="panel-heading"><h2><span class="step">01</span> Foto produk</h2><span class="muted small">INPUT</span></div>
<form id="inspection-form" action="proses/upload.php" method="post" enctype="multipart/form-data">
<label class="dropzone" id="dropzone" for="gambar"><input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp" required><span id="upload-hint"><span class="upload-icon" aria-hidden="true">↑</span><strong>Pilih foto produk</strong><span>atau tarik gambar ke area ini</span><small>JPG, PNG, WEBP · Maksimal 2 MB</small></span><img id="preview" alt="Pratinjau gambar yang dipilih" hidden></label>
<div class="file-info"><span id="file-name">Belum ada gambar dipilih</span><button type="button" class="text-button" id="reset-image" hidden>Ganti gambar</button></div>
<div class="tip">Gunakan foto yang terang, fokus, dan menampilkan produk dengan jelas.</div>
<button id="analyze" type="submit" class="primary-button" disabled>Menyiapkan model…</button>
<p id="model-state" class="model-state" role="status">Memuat model AI…</p><button type="button" id="retry-model" class="text-button" hidden>Coba muat ulang</button>
</form></section>
<section class="panel result-panel"><div class="panel-heading"><h2><span class="step">02</span> Hasil analisis</h2><span class="muted small" id="result-label">MENUNGGU GAMBAR</span></div>
<div id="result-empty" class="result-empty" ><span class="scan-icon" aria-hidden="true">◎</span><h3>Siap untuk pemeriksaan</h3><p>Pilih gambar, lalu tekan Analisis gambar.<br>Hasil akan tampil di sini.</p></div>
<div id="result-content" hidden><div class="verdict"><span id="result-status" class="badge neutral"></span><p class="muted">Keyakinan model</p><div class="confidence"><span id="confidence">0.00</span><span>%</span></div></div>
<div class="score"><div><span>OK <span class="muted">· Produk baik</span></span><strong id="ok-value">0.00%</strong></div><progress id="ok-bar" max="100" value="0.00" aria-label="Keyakinan OK"></progress></div>
<div class="score ng-score"><div><span>NG <span class="muted">· Produk cacat</span></span><strong id="ng-value">0.00%</strong></div><progress id="ng-bar" max="100" value="0.00" aria-label="Keyakinan NG"></progress></div>
<p class="result-source" id="result-source"></p></div>
<div id="feedback" role="status" hidden></div><p class="result-disclaimer">Keyakinan prediksi bukan ukuran akurasi model pada data uji. Model saat ini hanya mengenali OK dan NG.</p></section></div>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script><script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script><script src="js/script.js"></script>
<?php end_page(); ?>
