<?php
include '../config.php';

if(!isLoggedIn() || isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("
SELECT pp.*
FROM program_pelatihan pp
JOIN peserta_program ppr ON pp.id = ppr.program_id
JOIN peserta p ON ppr.peserta_id = p.id
WHERE p.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetchAll();

$all_programs = $conn->query("SELECT * FROM program_pelatihan")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard Peserta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body> 
<nav class="top-menu">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="pilih_program.php">Pilih Program Pelatihan</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Program Pelatihan Saya</h2>
        
        <?php if (empty($registered_programs)): ?>
            <p>Anda belum terdaftar di program pelatihan apapun.</p>
        <?php else: ?>
            <div class="program-list">
                <?php foreach ($registered_programs as $program): ?>
                    <div class="program-card">
                        <h3><?php echo htmlspecialchars($program['nama_program']); ?></h3>
                        <p><?php echo htmlspecialchars($program['deskripsi']); ?></p>
                        <p>Durasi: <?php echo htmlspecialchars($program['durasi']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>