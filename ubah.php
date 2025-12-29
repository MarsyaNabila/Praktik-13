<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// CEK ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// AMBIL DATA
$q = mysqli_query($koneksi, "SELECT * FROM data_barang WHERE id_barang='$id'");
$data = mysqli_fetch_assoc($q);

// JIKA DATA TIDAK ADA
if (!$data) {
    header("Location: index.php");
    exit;
}

// PROSES UPDATE
if (isset($_POST['submit'])) {

    $nama        = $_POST['nama'];
    $kategori    = $_POST['kategori'];
    $harga_beli  = $_POST['harga_beli'];
    $harga_jual  = $_POST['harga_jual'];
    $stok        = $_POST['stok'];

    $gambar_baru = $_FILES['gambar']['name'];
    $tmp         = $_FILES['gambar']['tmp_name'];

    if (!empty($gambar_baru)) {
        move_uploaded_file($tmp, "gambar/$gambar_baru");
        $g = $gambar_baru;
    } else {
        $g = $data['gambar'];
    }

    mysqli_query($koneksi, "
        UPDATE data_barang SET 
            nama='$nama',
            kategori='$kategori',
            harga_beli='$harga_beli',
            harga_jual='$harga_jual',
            stok='$stok',
            gambar='$g'
        WHERE id_barang='$id'
    ");

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Ubah Barang</h1>

    <form method="post" enctype="multipart/form-data">

    <label>Nama</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

    <label>Kategori</label>
    <select name="kategori" required>
        <option value="Elektronik" <?= ($data['kategori']=="Elektronik")?"selected":"" ?>>Elektronik</option>
        <option value="Komputer" <?= ($data['kategori']=="Komputer")?"selected":"" ?>>Komputer</option>
        <option value="Hand Phone" <?= ($data['kategori']=="Hand Phone")?"selected":"" ?>>Hand Phone</option>
    </select>

    <label>Harga Beli</label>
    <input type="number" name="harga_beli" value="<?= $data['harga_beli'] ?>" required>

    <label>Harga Jual</label>
    <input type="number" name="harga_jual" value="<?= $data['harga_jual'] ?>" required>

    <label>Stok</label>
    <input type="number" name="stok" value="<?= $data['stok'] ?>" required>

    <label>Gambar</label>
    <input type="file" name="gambar">
    <small>Gambar lama: <?= $data['gambar']; ?></small>

    <!-- TOMBOL SIMPAN (INI YANG HILANG) -->
    <button type="submit" name="submit" class="btn btn-submit">
        Simpan
    </button>

</form>
