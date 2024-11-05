<?php
// admin/manage_pelatih.php
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
                $stmt = $pdo->prepare("INSERT INTO pelatih (nama_pelatih, keahlian, email, no_telp) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nama_pelatih'],
                    $_POST['keahlian'],
                    $_POST['email'],
                    $_POST['no_telp']
                ]);
                $_SESSION['success'] = "Pelatih berhasil ditambahkan";
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE pelatih SET nama_pelatih = ?, keahlian = ?, email = ?, no_telp = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['nama_pelatih'],
                    $_POST['keahlian'],
                    $_POST['email'],
                    $_POST['no_telp'],
                    $_POST['id']
                ]);
                $_SESSION['success'] = "Data pelatih berhasil diupdate";
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM pelatih WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Pelatih berhasil dihapus";
                break;
        }
        header("Location: manage_pelatih.php");
        exit();
    }
}

// Fetch all trainers
$pelatih = $pdo->query("SELECT * FROM pelatih ORDER BY nama_pelatih")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data Pelatih</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../component/admin_nav.php'; ?>

    <div class="container">
        <h2>Kelola Data Pelatih</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Add New Trainer Form -->
        <div class="form-section">
            <h3>Tambah Pelatih Baru</h3>
            <form method="POST" class="add-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Nama Pelatih:</label>
                    <input type="text" name="nama_pelatih" required>
                </div>
                
                <div class="form-group">
                    <label>Keahlian:</label>
                    <input type="text" name="keahlian" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>No. Telepon:</label>
                    <input type="text" name="no_telp" required>
                </div>
                
                <button type="submit">Tambah Pelatih</button>
            </form>
        </div>

        <!-- List of Trainers -->
        <div class="table-section">
            <h3>Daftar Pelatih</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Pelatih</th>
                        <th>Keahlian</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pelatih as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nama_pelatih']); ?></td>
                        <td><?php echo htmlspecialchars($p['keahlian']); ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td><?php echo htmlspecialchars($p['no_telp']); ?></td>
                        <td>
                            <button onclick="editPelatih(<?php echo htmlspecialchars(json_encode($p)); ?>)">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus pelatih ini?')" class="delete-btn">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Data Pelatih</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Nama Pelatih:</label>
                    <input type="text" name="nama_pelatih" id="edit_nama_pelatih" required>
                </div>
                
                <div class="form-group">
                    <label>Keahlian:</label>
                    <input type="text" name="keahlian" id="edit_keahlian" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                
                <div class="form-group">
                    <label>No. Telepon:</label>
                    <input type="text" name="no_telp" id="edit_no_telp" required>
                </div>
                
                <button type="submit">Update Pelatih</button>
            </form>
        </div>
    </div>

    <script>
    // Get the modal
    var modal = document.getElementById("editModal");
    var span = document.getElementsByClassName("close")[0];

    function editPelatih(pelatih) {
        modal.style.display = "block";
        document.getElementById("edit_id").value = pelatih.id;
        document.getElementById("edit_nama_pelatih").value = pelatih.nama_pelatih;
        document.getElementById("edit_keahlian").value = pelatih.keahlian;
        document.getElementById("edit_email").value = pelatih.email;
        document.getElementById("edit_no_telp").value = pelatih.no_telp;
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>