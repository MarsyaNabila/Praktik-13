<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

/* ===============================
   PAGINATION SETTING
================================ */
$limit = 2;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
if ($halaman < 1) $halaman = 1;

$awalData = ($halaman - 1) * $limit;

/* ===============================
   PENCARIAN
================================ */
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$where = ($cari != '') ? "WHERE nama LIKE '%$cari%' OR kategori LIKE '%$cari%'" : "";

/* ===============================
   QUERY DATA
================================ */
$sql = "SELECT * FROM data_barang $where ORDER BY id_barang ASC LIMIT $awalData, $limit";
$result = mysqli_query($koneksi, $sql);

/* ===============================
   TOTAL DATA
================================ */
$totalQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_barang $where");
$totalRow = mysqli_fetch_assoc($totalQuery);
$totalData = $totalRow['total'];
$totalHalaman = ceil($totalData / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Barang</title>
<link rel="stylesheet" href="style.css">

<style>
/* ===============================
   SEARCH
================================ */
.search-box {
    display: flex;
    gap: 10px;
    width: 50%;
    margin: 20px 0;
}

.search-box input {
    flex: 1;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ff4f8b;
    outline: none;
}

.search-box button {
    width: auto !important;   /* ⬅ PENTING */
    padding: 10px 24px;
    background: #ff4f8b;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

/* ===============================
   PAGINATION + FOOTER WRAPPER
================================ */
.bottom-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 35px;
    padding-bottom: 25px;
}

/* ===============================
   PAGINATION
================================ */
.pagination {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
}

.pagination a {
    padding: 8px 14px;
    background: #ff4f8b;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
}

.pagination a.active {
    background: #e63b76;
}

.pagination a:hover {
    opacity: 0.85;
}

/* ===============================
   FOOTER
================================ */
footer {
    font-size: 13px;
    color: #6b1c3b;
    text-align: center;
}
</style>
</head>

<body>

<div class="container">

    <h1>Data Barang</h1>

    <a href="tambah.php" class="btn">+ Tambah Barang</a>

    <!-- SEARCH -->
    <form method="GET" class="search-box">
        <input type="text" name="cari" placeholder="Cari nama barang"
               value="<?= htmlspecialchars($cari); ?>">
        <button type="submit">Cari</button>
    </form>

    <!-- TABEL -->
    <table>
        <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = $awalData + 1;
        while ($row = mysqli_fetch_assoc($result)) :
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td>
                <?php if (!empty($row['gambar'])) : ?>
                    <img src="gambar/<?= $row['gambar']; ?>" class="thumb">
                <?php else : ?>
                    -
                <?php endif; ?>
            </td>
            <td><?= $row['nama']; ?></td>
            <td><?= $row['kategori']; ?></td>
            <td><?= number_format($row['harga_beli']); ?></td>
            <td><?= number_format($row['harga_jual']); ?></td>
            <td><?= $row['stok']; ?></td>
            <td>
                <a class="link" href="ubah.php?id=<?= $row['id_barang']; ?>">Ubah</a> |
                <a class="link"
                   href="hapus.php?id=<?= $row['id_barang']; ?>"
                   onclick="return confirm('Yakin hapus data?')">
                   Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- PAGINATION + FOOTER -->
    <div class="bottom-area">

        <div class="pagination">
            <?php if ($halaman > 1) : ?>
                <a href="?hal=<?= $halaman - 1; ?>&cari=<?= $cari; ?>">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalHalaman; $i++) : ?>
                <a href="?hal=<?= $i; ?>&cari=<?= $cari; ?>"
                   class="<?= ($halaman == $i) ? 'active' : ''; ?>">
                   <?= $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($halaman < $totalHalaman) : ?>
                <a href="?hal=<?= $halaman + 1; ?>&cari=<?= $cari; ?>">Next</a>
            <?php endif; ?>
        </div>

        <footer>
            © 2025 · Universitas Pelita Bangsa | Marsya Nabila Putri
        </footer>

    </div>

</div>

</body>
</html>
