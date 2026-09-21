<?php

include "config/koneksi.php";

// Ambil hasil pemeriksaan terakhir
$query_last = "SELECT * FROM pemeriksaan ORDER BY id DESC LIMIT 1";
$result_last = mysqli_query($conn, $query_last);
$last_data = mysqli_fetch_assoc($result_last);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inspection - Visual Inspection AI</title>

    <link rel="stylesheet" href="css/style.css">


    <!-- TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>


    <!-- Teachable Machine -->
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

</head>


<body>


<header>

    <h1>Visual Inspection</h1>

    <p>Sistem Deteksi Produk OK / NG / Failed</p>

</header>


<nav>

    <a href="index.php">Beranda</a> |

    <a href="inspection.php">Inspection</a> |

    <a href="proses/riwayat.php">Riwayat</a>

</nav>


<main>


    <!-- ========================= -->
    <!-- INSPECTION -->
    <!-- ========================= -->

    <section>

        <h2>Inspection</h2>

        <p>
            Upload gambar produk untuk melakukan pemeriksaan menggunakan AI.
        </p>


        <form
            action="proses/upload.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <label for="gambar">
                Upload Gambar Produk
            </label>

            <br><br>


            <input
                type="file"
                id="gambar"
                name="gambar"
                accept="image/*"
            >


            <br><br>


            <img
                id="preview"
                width="300"
                alt="Preview gambar"
            >


            <br><br>


            <button type="submit">
                Analisis
            </button>


        </form>

    </section>



    <hr>



    <!-- ========================= -->
    <!-- HASIL PEMERIKSAAN TERAKHIR -->
    <!-- ========================= -->

    <section>

        <h2>Hasil Deteksi Terakhir</h2>


        <?php if ($last_data): ?>


            <p>

                <strong>Status:</strong>

                <span
                    style="
                        font-size:24px;
                        color:
                        <?= $last_data['hasil'] == 'OK'
                            ? 'green'
                            : 'red'
                        ?>;
                    "
                >

                    <?= htmlspecialchars($last_data['hasil']) ?>

                </span>

            </p>


            <p>

                <strong>Akurasi:</strong>

                <?= htmlspecialchars($last_data['akurasi']) ?>%

            </p>


            <ul>

                <li>

                    <strong>OK:</strong>

                    <?= isset($last_data['persentase_ok'])
                        ? htmlspecialchars($last_data['persentase_ok'])
                        : '0'
                    ?>%

                </li>


                <li>

                    <strong>NG:</strong>

                    <?= isset($last_data['persentase_ng'])
                        ? htmlspecialchars($last_data['persentase_ng'])
                        : '0'
                    ?>%

                </li>

            </ul>


            <p>
                <strong>Gambar:</strong>
            </p>


            <img
                src="uploads/<?= htmlspecialchars($last_data['nama_gambar']) ?>"
                width="300"
                alt="Hasil pemeriksaan"
            >


            <p>

                <strong>Tanggal:</strong>

                <?= date(
                    'd M Y H:i',
                    strtotime($last_data['tanggal'])
                ) ?>

            </p>


        <?php else: ?>


            <p>
                Belum ada hasil pemeriksaan.
            </p>


        <?php endif; ?>


    </section>


</main>



<footer>

    <p>
        © 2026 Visual Inspection AI
    </p>

</footer>



<script src="js/script.js"></script>


</body>

</html>