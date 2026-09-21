<?php
require __DIR__ . '/../includes/view.php';
$items = rows();
start_page(true);
?>
<section class="panel history-panel"><div class="panel-heading"><h2>Semua pemeriksaan <span class="count"><?= count($items) ?></span></h2><a class="text-link" href="../index.php">+ Pemeriksaan baru</a></div><?php history_table($items, '../'); ?></section>
<?php end_page(); ?>

