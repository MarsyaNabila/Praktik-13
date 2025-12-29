<?php
include 'koneksi.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM data_barang WHERE id_barang='$id'");

header("Location: index.php");
?>