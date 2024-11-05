<?php
// admin/manage_peserta.php
require_once '../config.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'edit':
                $stmt = $pdo->prepare("UPDATE peserta SET nama_lengkap = ?, email = ?, no_telp = ?, alamat = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['nama_lengkap'],
                    $_POST['email'],
                    $_POST['no_telp'],
                    $_POST['alamat'],
                    $_POST['id']
                ]);
                $_SESSION['success'] = "Data peserta berhasil diupdate";
                break;
                
            case 'delete':
                // Delete user and related peserta data
                $stmt = $pdo->prepare("
                    DELETE users, peserta 
                    FROM users 
                    INNER JOIN peserta ON users.id = peserta.user_id 
                    WHERE peserta.id = ?
                ");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Peserta berhasil dihapus";
                break;
        }
        header("Location: manage_peserta.php");
        exit();
    }
}

// Fetch all participants with their program selections
$stmt = $pdo->query("
    SELECT 
        p.*,
        u.username,
        GROUP_CONCAT(pp.nama_program SEPARATOR ', ') as program_yang_diikuti
    FROM peserta p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN peserta_program ppr ON p.id = ppr.peserta_id
    LEFT JOIN program_pelatihan pp ON ppr.program_id = pp.id
    GROUP BY p.id
    ORDER BY p.nama_lengkap
");
$peserta = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data Peserta</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .program-list {
            font-size: 0.9em;
            color: #666;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <?php include '../component/admin_nav.php'; ?>

    <div class="container">
        <h2>Kelola Data Peserta</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <!-- List of Participants -->
        <div class="table-section">
            <h3>Daftar Peserta</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Program yang Diikuti</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($peserta as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['username']); ?></td>
                        <td><?php echo htmlspecialchars($p['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td><?php echo htmlspecialchars($p['no_telp']); ?></td>
                        <td><?php echo htmlspecialchars($p['alamat']); ?></td>
                        <td>
                            <div class="program-list">
                                <?php 
                                    echo $p['program_yang_diikuti'] 
                                        ? htmlspecialchars($p['program_yang_diikuti']) 
                                        : '<em>Belum memilih program</em>';
                                ?>
                            </div>
                        </td>
                        <td>
                            <button onclick="editPeserta(<?php echo htmlspecialchars(json_encode($p)); ?>)" class="btn-edit">
                                Edit
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus peserta ini? Semua data terkait akan ikut terhapus.')" class="btn-delete">
                                    Hapus
                                </button>
                            </form>
                            <button onclick="viewDetail(<?php echo $p['id']; ?>)" class="btn-view">
                                Detail
                            </button>
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
            <h3>Edit Data Peserta</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_lengkap" id="edit_nama_lengkap" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                
                <div class="form-group">
                    <label>No. Telepon:</label>
                    <input type="text" name="no_telp" id="edit_no_telp">
                </div>
                
                <div class="form-group">
                    <label>Alamat:</label>
                    <textarea name="alamat" id="edit_alamat" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Update Data</button>
            </form>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <h3>Detail Peserta</h3>
            <div id="detailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    var editModal = document.getElementById("editModal");
    var detailModal = document.getElementById("detailModal");
    var span = document.getElementsByClassName("close")[0];

    function editPeserta(peserta) {
        editModal.style.display = "block";
        document.getElementById("edit_id").value = peserta.id;
        document.getElementById("edit_nama_lengkap").value = peserta.nama_lengkap;
        document.getElementById("edit_email").value = peserta.email;
        document.getElementById("edit_no_telp").value = peserta.no_telp;
        document.getElementById("edit_alamat").value = peserta.alamat;
    }

    async function viewDetail(id) {
        try {
            const response = await fetch('get_peserta_detail.php?id=' + id);
            const data = await response.json();
            
            let detailHTML = `
                <div class="detail-info">
                    <p><strong>Nama:</strong> ${data.nama_lengkap}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>No. Telepon:</strong> ${data.no_telp}</p>
                    <p><strong>Alamat:</strong> ${data.alamat}</p>
                    <h4>Program yang Diikuti:</h4>
                    <ul>
                        ${data.programs.map(p => `
                            <li>${p.nama_program} (Terdaftar: ${p.tanggal_daftar})</li>
                        `).join('')}
                    </ul>
                </div>
            `;
            
            document.getElementById("detailContent").innerHTML = detailHTML;
            detailModal.style.display = "block";
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat detail peserta');
        }
    }

    function closeDetailModal() {
        detailModal.style.display = "none";
    }

    span.onclick = function() {
        editModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
        if (event.target == detailModal) {
            detailModal.style.display = "none";
        }
    }
    </script>
</body>
</html>