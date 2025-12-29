# Membuat Pencarian Data & Pagination

Nama: Marsya Nabila Putri 

NIM: 312410338

Kelas: TI.24.A4

Matakuliah: Pemrograman Web 1

## Index.php

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

$limit = 2;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
if ($halaman < 1) $halaman = 1;

$awalData = ($halaman - 1) * $limit;

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$where = ($cari != '') ? "WHERE nama LIKE '%$cari%' OR kategori LIKE '%$cari%'" : "";


$sql = "SELECT * FROM data_barang $where ORDER BY id_barang ASC LIMIT $awalData, $limit";
$result = mysqli_query($koneksi, $sql);


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


.bottom-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 35px;
    padding-bottom: 25px;
}


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
```

## Menentukan Jumlah Data per Halaman
```php
$limit = 2;
```
Variabel $limit digunakan untuk mengatur berapa banyak data barang yang ditampilkan dalam satu halaman. Pada kode ini, setiap halaman hanya menampilkan 2 data barang agar tampilan tidak terlalu panjang dan lebih rapi.


## Menentukan Halaman Aktif
```php
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
if ($halaman < 1) {
    $halaman = 1;
}
```
Kode ini digunakan untuk menentukan halaman yang sedang dibuka oleh pengguna. Jika parameter hal tidak ada di URL, maka halaman akan otomatis diset ke halaman pertama. Validasi juga dilakukan agar nilai halaman tidak kurang dari 1.


## Menentukan Posisi Awal Data (OFFSET)
```php
$awalData = ($halaman - 1) * $limit;
```
Baris kode ini berfungsi untuk menentukan data awal yang akan diambil dari database. Nilai ini digunakan sebagai OFFSET pada query SQL. Contohnya:

- Halaman 1 → (1 - 1) × 2 = 0

- Halaman 2 → (2 - 1) × 2 = 2

- Halaman 3 → (3 - 1) × 2 = 4


## Proses Pencarian Data
```php
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$where = "";

if ($cari != '') {
    $where = "WHERE nama LIKE '%$cari%' OR kategori LIKE '%$cari%'";
}
```

Bagian ini digunakan untuk fitur pencarian data barang. Jika pengguna mengisi kolom pencarian, maka query akan memfilter data berdasarkan nama barang atau kategori. Jika tidak ada pencarian, maka seluruh data akan ditampilkan.

## Query Data dengan LIMIT dan OFFSET
```php
$sql = "SELECT * FROM data_barang
        $where
        ORDER BY id_barang ASC
        LIMIT $awalData, $limit";
```

Query ini digunakan untuk mengambil data dari tabel data_barang sesuai halaman yang aktif. Parameter $awalData berfungsi sebagai OFFSET dan $limit sebagai batas jumlah data yang ditampilkan per halaman.

## Menghitung Total Data
```php
$totalQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_barang $where");
$totalRow = mysqli_fetch_assoc($totalQuery);
$totalData = $totalRow['total'];
```

Kode ini digunakan untuk menghitung jumlah seluruh data barang yang ada di database, termasuk hasil pencarian jika fitur pencarian digunakan. Nilai ini diperlukan untuk menentukan jumlah halaman pagination.

## Menentukan Jumlah Halaman
```php
$totalHalaman = ceil($totalData / $limit);
```

Jumlah halaman dihitung dengan membagi total data dengan jumlah data per halaman. Fungsi ceil() digunakan agar hasil pembagian dibulatkan ke atas.

## Link Pagination (Previous dan Next)
```php
<?php if ($halaman > 1) : ?>
    <a href="?hal=<?= $halaman - 1; ?>&cari=<?= $cari; ?>">Prev</a>
<?php endif; ?>

<?php if ($halaman < $totalHalaman) : ?>
    <a href="?hal=<?= $halaman + 1; ?>&cari=<?= $cari; ?>">Next</a>
<?php endif; ?>
```

Bagian ini berfungsi untuk navigasi pagination. Tombol Prev akan membawa pengguna ke halaman sebelumnya, sedangkan tombol Next akan mengarahkan ke halaman berikutnya. Parameter pencarian tetap dikirim agar hasil pencarian tidak hilang saat berpindah halaman.


## Latihan
Lengkapi link previous dan next sehingga ketika diklik akan mengarah ke halaman sebelumnya
atau selanjutnya.


## Hasil Akhir di Browser

<img width="955" height="1040" alt="Screenshot 2025-12-29 135401" src="https://github.com/user-attachments/assets/7b5647ee-e238-45af-9d70-fccee0692b75" />

Membuat Dua Halaman

<img width="955" height="916" alt="Screenshot 2025-12-29 135414" src="https://github.com/user-attachments/assets/7e081f86-9078-4e56-8117-9053b3f80cbc" />

