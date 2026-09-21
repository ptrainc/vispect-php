<?php

include "../config/koneksi.php";

if(isset($_FILES['gambar'])){

    $namaFile = $_FILES['gambar']['name'];
    $tmpFile = $_FILES['gambar']['tmp_name'];
    $folder = "../uploads/" . $namaFile;

    // Upload gambar
    if(move_uploaded_file($tmpFile, $folder)){

        // Ambil hasil prediksi dari JavaScript
        $hasil = isset($_POST['status']) ? $_POST['status'] : 'Belum Dicek';
        $akurasi = isset($_POST['akurasi']) ? $_POST['akurasi'] : '0';
        $persentase_ok = isset($_POST['persentase_ok']) ? $_POST['persentase_ok'] : '0';
        $persentase_ng = isset($_POST['persentase_ng']) ? $_POST['persentase_ng'] : '0';

        // Simpan ke database dengan prepared statement (lebih aman)
        $stmt = $conn->prepare("INSERT INTO pemeriksaan 
            (nama_gambar, hasil, akurasi, persentase_ok, persentase_ng, tanggal) 
            VALUES (?, ?, ?, ?, ?, NOW())");
        
        $stmt->bind_param("sssss", $namaFile, $hasil, $akurasi, $persentase_ok, $persentase_ng);

        if($stmt->execute()){
            echo "Upload berhasil!\n\nHasil: $hasil\nAkurasi: $akurasi%\nOK: $persentase_ok%\nNG: $persentase_ng%";
        }else{
            echo "Database gagal menyimpan: " . $conn->error;
        }

        $stmt->close();

    }else{
        echo "❌ Upload gambar gagal";
    }

}else{
    echo "❌ Tidak ada file yang diupload";
}

?>