<?php 
require_once '../config.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}
$stmt = $pdo->prepare('
SELECT p.nama_lengkap, pp.nama_program, pp.tanggal_daftar FROM peserta p
JOIN peserta_program ppr ON p.id = ppr.peserta_id
JOIN program_pelatihan pp ON ppr.program_id = pp.id
ORDER BY pp.tanggal_daftar DESC
');
$registrations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="top-menu">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="dropdown">
                <a href="#">Kelola Data</a>
                <div class="dropdown-content">
                    <a href="manage_peserta.php">Data Peserta</a>
                    <a href="manage_pelatih.php">Data Pelatih</a>
                    <a href="manage_program.php">Program Pelatihan</a>
                    <a href="manage_berita.php">Berita</a>
                </div>
            </li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>daftar Peserta Program Pelatihan</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Peserta</th>
                    <th>Program Pelatihan</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $reg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reg['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($reg['nama_program']); ?></td>
                    <td><?php echo htmlspecialchars($reg['tanggal_daftar']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>