<?php
// admin/manage_program.php
require_once '../config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO program_pelatihan (nama_program, deskripsi, durasi, pelatih_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nama_program'],
                    $_POST['deskripsi'],
                    $_POST['durasi'],
                    $_POST['pelatih_id']
                ]);
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE program_pelatihan SET nama_program = ?, deskripsi = ?, durasi = ?, pelatih_id = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['nama_program'],
                    $_POST['deskripsi'],
                    $_POST['durasi'],
                    $_POST['pelatih_id'],
                    $_POST['id']
                ]);
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM program_pelatihan WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
    }
}

// Fetch all programs
$programs = $pdo->query("SELECT * FROM program_pelatihan")->fetchAll();
// Fetch all trainers for dropdown
$pelatih = $pdo->query("SELECT * FROM pelatih")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Program Pelatihan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../component/admin_nav.php'; ?>

    <div class="container">
        <h2>Kelola Program Pelatihan</h2>
        
        <!-- Add New Program Form -->
        <div class="form-section">
            <h3>Tambah Program Baru</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Nama Program:</label>
                    <input type="text" name="nama_program" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi:</label>
                    <textarea name="deskripsi" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Durasi:</label>
                    <input type="text" name="durasi" required>
                </div>
                
                <div class="form-group">
                    <label>Pelatih:</label>
                    <select name="pelatih_id" required>
                        <?php foreach ($pelatih as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['nama_pelatih']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit">Tambah Program</button>
            </form>
        </div>

        <!-- List of Programs -->
        <div class="table-section">
            <h3>Daftar Program</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Program</th>
                        <th>Deskripsi</th>
                        <th>Durasi</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $program): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($program['nama_program']); ?></td>
                        <td><?php echo htmlspecialchars($program['deskripsi']); ?></td>
                        <td><?php echo htmlspecialchars($program['durasi']); ?></td>
                        <td>
                            <button onclick="editProgram(<?php echo $program['id']; ?>)">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $program['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function editProgram(id) {
        // Implement edit functionality with modal or redirect
        // This is a simplified version
        alert('Edit program with ID: ' + id);
    }
    </script>
</body>
</html>