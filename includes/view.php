<?php
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function pct($v) { return number_format(max(0, min(100, (float)$v)), 2); }
function rows($limit = null) {
 global $conn;
 require_once __DIR__ . '/../config/koneksi.php';
 $sql = 'SELECT * FROM pemeriksaan ORDER BY id DESC';
 if ($limit !== null) $sql .= ' LIMIT ' . (int)$limit;
 $result = mysqli_query($conn, $sql);
 return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}
function start_page($history = false) {
 $base = $history ? '../' : '';
 $title = $history ? 'Riwayat pemeriksaan' : 'Pemeriksaan produk';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $title ?> · Visual Inspection</title><link rel="stylesheet" href="<?= $base ?>css/style.css"></head>
<body><a class="skip" href="#main">Lewati ke konten</a>
<aside class="sidebar"><a class="brand" href="<?= $base ?>index.php"><span class="brand-mark">VI</span><span>Visual Inspection<small>QUALITY CONTROL</small></span></a>
<p class="nav-label">RUANG KERJA</p><nav aria-label="Navigasi utama"><a class="<?= !$history ? 'active' : '' ?>" <?= !$history ? 'aria-current="page"' : '' ?> href="<?= $base ?>index.php"><span aria-hidden="true">▣</span> Pemeriksaan</a><a class="<?= $history ? 'active' : '' ?>" <?= $history ? 'aria-current="page"' : '' ?> href="<?= $base ?>proses/riwayat.php"><span aria-hidden="true">≡</span> Riwayat</a></nav>
<div class="model-note"><small>MODEL SAAT INI</small><strong>Teachable Machine</strong><span>Klasifikasi gambar · OK / NG</span><p>FAILED belum didukung oleh model ini.</p></div></aside>
<div class="workspace"><header class="topbar"><span>Quality control <span class="separator">/</span> <?= $title ?></span><span class="tag">UI v2.0</span></header><main id="main"><div class="page-heading"><div><p class="eyebrow">VISUAL INSPECTION</p><h1><?= $title ?></h1><p class="muted"><?= $history ? 'Catatan hasil analisis yang tersimpan di database.' : 'Unggah foto produk dan periksa hasil analisisnya.' ?></p></div><span class="outline-tag">MODEL · 2 KELAS</span></div>
<?php }
function history_table($items, $base = '') { ?>
<div class="table-wrap"><table><thead><tr><th scope="col">Gambar produk</th><th scope="col">Status</th><th scope="col">OK</th><th scope="col">NG</th><th scope="col">Keyakinan</th><th scope="col">Waktu</th></tr></thead><tbody>
<?php if (!$items): ?><tr><td colspan="6" class="empty-cell">Belum ada pemeriksaan tersimpan.</td></tr><?php endif; ?>
<?php foreach ($items as $row): $status = $row['hasil'] === 'OK' ? 'ok' : ($row['hasil'] === 'NG' ? 'ng' : 'neutral'); ?>
<tr><td><div class="product-cell"><img loading="lazy" src="<?= $base ?>uploads/<?= esc(rawurlencode($row['nama_gambar'])) ?>" alt="Foto produk"><span><?= esc($row['nama_gambar']) ?><small>#<?= esc($row['id']) ?></small></span></div></td><td><span class="badge <?= $status ?>"><?= esc($row['hasil']) ?></span></td><td><?= pct($row['persentase_ok'] ?? 0) ?>%</td><td><?= pct($row['persentase_ng'] ?? 0) ?>%</td><td class="number"><?= pct($row['akurasi']) ?>%</td><td class="date"><?= esc($row['tanggal']) ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php }
function end_page() { ?><footer class="footer"><span>Visual Inspection · 2026</span><span>Hasil AI perlu diverifikasi oleh pemeriksa.</span></footer></main></div></body></html><?php }

