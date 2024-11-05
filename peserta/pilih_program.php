<?php
include '../config.php';

if (!isLoggedIn() || isAdmin()) {
    header("Location: ../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['program_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM peserta WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $peseta = $stmt->fetch();

        if($peserta) {
            $stmt = $pdo->prepare("SELECT * FROM program_pelatihan WHERE peserta_id = ? AND program_id = ?");
            $stmt->execute([$peserta['id'], $_POST['program_id']]);

            if(!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO peserta_program (peserta_id, program_id) VALUES (?, ?)");
                $stmt->execute([$peserta['id'], $_POST['program_id']]);
                $success = "Berhasil mendaftar program pelatihan";
            } else {
                $error = "Anda sudah terdaftar di program ini";
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT pp.*, pel.nama_pelatih FROM program_pelatihan pp JOIN pelatih pel ON pp.pelatih_id = pel.id");
$stmt->execute();
$programs = $stmt->fetchAll();

// ger registered programs
$stmt = $pdo->prepare("SELECT program_id FROM peserta_program pp JOIN peserta p ON pp.peserta_id = p.id WHERE p.user_id = ?");
$stmt->execute([$peserta['id']]);
$registered_programs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pilih Program Pelatihan</title>
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
        <h2>Program Pelatihan Tersedia</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="program-grid">
            <?php foreach ($programs as $program): ?>
                <div class="program-card">
                    <h3><?php echo htmlspecialchars($program['nama_program']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($program['deskripsi']); ?></p>
                    <div class="program-details">
                        <p><strong>Durasi:</strong> <?php echo htmlspecialchars($program['durasi']); ?></p>
                        <p><strong>Pelatih:</strong> <?php echo htmlspecialchars($program['nama_pelatih']); ?></p>
                    </div>
                    
                    <?php if (in_array($program['id'], $registered)): ?>
                        <button class="btn-registered" disabled>Sudah Terdaftar</button>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                            <button type="submit" class="btn-register">Daftar Program</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>