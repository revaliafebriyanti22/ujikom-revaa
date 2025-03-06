<?php
session_start();
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login

// Menambah tugas ke dalam database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $prioritas = $_POST['prioritas'];
    $tanggal = $_POST['tanggal'];

    // Pastikan input tidak kosong
    if (!empty($nama) && !empty($prioritas) && !empty($tanggal)) {
        $sql = "INSERT INTO tasks (nama, prioritas, tanggal, user_id, status) 
                VALUES ('$nama', '$prioritas', '$tanggal', '$user_id', 'Belum Selesai')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php"); // Refresh halaman setelah menambah tugas
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Semua field harus diisi!";
    }
}

// Cek apakah ada pencarian tugas
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Query untuk menampilkan daftar tugas
if ($search) {
    $result = $conn->query("SELECT * FROM tasks WHERE user_id='$user_id' AND nama LIKE '%$search%' ORDER BY id DESC");
} else {
    $result = $conn->query("SELECT * FROM tasks WHERE user_id='$user_id' ORDER BY id DESC");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>To-Do List</h2>
    <p>Halo, <?= $_SESSION['user_id']; ?>! <a href="logout.php">Logout</a></p>

    <!-- Form Menambahkan Tugas -->
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Tugas" required>
        <select name="prioritas">
            <option value="Tinggi">Tinggi</option>
            <option value="Sedang">Sedang</option>
            <option value="Rendah">Rendah</option>
        </select>
        <input type="date" name="tanggal" required>
        <button type="submit" name="add">Tambah</button>
    </form>

    <!-- Form Pencarian -->
    <form method="GET">
        <input type="text" name="search" placeholder="Cari tugas..." value="<?= htmlspecialchars($search); ?>">
        <button type="submit">Cari</button>
        <a href="index.php"><button type="button">Lihat Semua</button></a>
    </form>

    <h3>Daftar Tugas</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Prioritas</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['nama']; ?></td>
            <td><?= $row['prioritas']; ?></td>
            <td><?= $row['tanggal']; ?></td>
            <td><?= $row['status']; ?></td>
            <td>
                <a href="update.php?id=<?= $row['id']; ?>">Selesai</a> |
                <a href="delete.php?id=<?= $row['id']; ?>" class="delete">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
