<?php
// admin/manage_berita.php
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
                $stmt = $pdo->prepare("INSERT INTO berita (judul, konten) VALUES (?, ?)");
                $stmt->execute([
                    $_POST['judul'],
                    $_POST['konten']
                ]);
                $_SESSION['success'] = "Berita berhasil ditambahkan";
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE berita SET judul = ?, konten = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['judul'],
                    $_POST['konten'],
                    $_POST['id']
                ]);
                $_SESSION['success'] = "Berita berhasil diupdate";
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Berita berhasil dihapus";
                break;
        }
        header("Location: manage_berita.php");
        exit();
    }
}

// Fetch all news
$berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal_post DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Berita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../component/admin_nav.php'; ?>

    <div class="container">
        <h2>Kelola Berita</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Add New News Form -->
        <div class="form-section">
            <h3>Tambah Berita Baru</h3>
            <form method="POST" class="add-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Judul:</label>
                    <input type="text" name="judul" required>
                </div>
                
                <div class="form-group">
                    <label>Konten:</label>
                    <textarea name="konten" rows="5" required></textarea>
                </div>
                
                <button type="submit">Tambah Berita</button>
            </form>
        </div>

        <!-- List of News -->
        <div class="table-section">
            <h3>Daftar Berita</h3>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Konten</th>
                        <th>Tanggal Post</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($berita as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['judul']); ?></td>
                        <td><?php echo substr(htmlspecialchars($b['konten']), 0, 100) . '...'; ?></td>
                        <td><?php echo htmlspecialchars($b['tanggal_post']); ?></td>
                        <td>
                            <button onclick="editBerita(<?php echo htmlspecialchars(json_encode($b)); ?>)">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus berita ini?')" class="delete-btn">
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
            <h3>Edit Berita</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Judul:</label>
                    <input type="text" name="judul" id="edit_judul" required>
                </div>
                
                <div class="form-group">
                    <label>Konten:</label>
                    <textarea name="konten" id="edit_konten" rows="5" required></textarea>
                </div>
                
                <button type="submit">Update Berita</button>
            </form>
        </div>
    </div>

    <script>
    var modal = document.getElementById("editModal");
    var span = document.getElementsByClassName("close")[0];

    function editBerita(berita) {
        modal.style.display = "block";
        document.getElementById("edit_id").value = berita.id;
        document.getElementById("edit_judul").value = berita.judul;
        document.getElementById("edit_konten").value = berita.konten;
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