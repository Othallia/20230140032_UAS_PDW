<?php
$pageTitle = 'Kelola Modul';
$activePage = 'modul';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';
$error = '';
$edit_data = null;
$upload_dir = '../uploads/materi/';

// Logika Form (Create, Update, Delete) - (Tidak ada perubahan di sini)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql_getfile = "SELECT file_materi FROM modul WHERE id = ?";
        $stmt_getfile = $conn->prepare($sql_getfile); $stmt_getfile->bind_param("i", $id); $stmt_getfile->execute(); $result_file = $stmt_getfile->get_result()->fetch_assoc();
        if ($result_file && !empty($result_file['file_materi'])) { if (file_exists($upload_dir . $result_file['file_materi'])) { unlink($upload_dir . $result_file['file_materi']); } }
        $stmt_getfile->close();
        $sql = "DELETE FROM modul WHERE id = ?";
        $stmt = $conn->prepare($sql); $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Modul berhasil dihapus."; } else { $error = "Gagal menghapus modul."; }
        $stmt->close();
    } else {
        $mata_praktikum_id = intval($_POST['mata_praktikum_id']); $judul_modul = trim($_POST['judul_modul']); $deskripsi = trim($_POST['deskripsi']); $id = intval($_POST['id'] ?? 0);
        if (empty($judul_modul) || empty($mata_praktikum_id)) { $error = "Mata praktikum dan judul modul tidak boleh kosong."; }
        else {
            $file_materi = $_POST['file_materi_lama'] ?? '';
            if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
                if ($id > 0 && !empty($file_materi) && file_exists($upload_dir . $file_materi)) { unlink($upload_dir . $file_materi); }
                $file_tmp = $_FILES['file_materi']['tmp_name']; $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
                move_uploaded_file($file_tmp, $upload_dir . $file_name);
                $file_materi = $file_name;
            }
            if ($id > 0) { $sql = "UPDATE modul SET mata_praktikum_id = ?, judul_modul = ?, deskripsi = ?, file_materi = ? WHERE id = ?"; $stmt = $conn->prepare($sql); $stmt->bind_param("isssi", $mata_praktikum_id, $judul_modul, $deskripsi, $file_materi, $id); if ($stmt->execute()) { $message = "Modul berhasil diperbarui."; } else { $error = "Gagal memperbarui modul."; }
            } else { $sql = "INSERT INTO modul (mata_praktikum_id, judul_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)"; $stmt = $conn->prepare($sql); $stmt->bind_param("isss", $mata_praktikum_id, $judul_modul, $deskripsi, $file_materi); if ($stmt->execute()) { $message = "Modul baru berhasil ditambahkan."; } else { $error = "Gagal menambahkan modul."; } }
            $stmt->close();
        }
    }
}
// Logika untuk mengambil data yang akan di-edit
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) { $id = intval($_GET['id']); $sql_edit = "SELECT * FROM modul WHERE id = ?"; $stmt_edit = $conn->prepare($sql_edit); $stmt_edit->bind_param("i", $id); $stmt_edit->execute(); $result_edit = $stmt_edit->get_result(); $edit_data = $result_edit->fetch_assoc(); $stmt_edit->close(); }
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
?>

<?php if ($message): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $message; ?></p></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $error; ?></p></div><?php endif; ?>

<div class="bg-white p-8 rounded-2xl shadow-md mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6"><?php echo $edit_data ? '✏️ Edit Modul' : 'Tambahkan Modul Baru'; ?></h2>
    <form action="kelola_modul.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="mata_praktikum_id" class="block text-sm font-bold text-slate-700 mb-2">Mata Praktikum</label>
                <select name="mata_praktikum_id" id="mata_praktikum_id" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
                    <option value="">-- Pilih Mata Praktikum --</option>
                    <?php while($prak = $praktikum_list->fetch_assoc()): ?>
                        <option value="<?php echo $prak['id']; ?>" <?php echo (isset($edit_data['mata_praktikum_id']) && $edit_data['mata_praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="judul_modul" class="block text-sm font-bold text-slate-700 mb-2">Judul Modul</label>
                <input type="text" name="judul_modul" id="judul_modul" value="<?php echo htmlspecialchars($edit_data['judul_modul'] ?? ''); ?>" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
            </div>
        </div>
        <div class="mb-6">
            <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div class="mb-6">
            <label for="file_materi" class="block text-sm font-bold text-slate-700 mb-2">File Materi (Opsional)</label>
            <input type="file" name="file_materi" id="file_materi" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
            <?php if (isset($edit_data['file_materi']) && !empty($edit_data['file_materi'])): ?>
                <p class="text-xs text-slate-500 mt-2 font-semibold">File saat ini: <a href="<?php echo $upload_dir . htmlspecialchars($edit_data['file_materi']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($edit_data['file_materi']); ?></a></p>
                <input type="hidden" name="file_materi_lama" value="<?php echo htmlspecialchars($edit_data['file_materi']); ?>">
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-all">
                <?php echo $edit_data ? 'Update Modul' : 'Simpan Modul'; ?>
            </button>
            <?php if ($edit_data): ?>
                <a href="kelola_modul.php" class="font-bold text-sm text-slate-600 hover:text-red-600">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Daftar Modul</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-100">
                <tr>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Judul Modul</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Mata Praktikum</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">File</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-700">
                <?php 
                mysqli_data_seek($praktikum_list, 0); // Reset pointer
                $sql_read = "SELECT m.id, m.judul_modul, m.file_materi, mp.nama_praktikum FROM modul m JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id ORDER BY mp.nama_praktikum, m.id";
                $result_read = $conn->query($sql_read);
                if ($result_read->num_rows > 0):
                    while($row = $result_read->fetch_assoc()):
                ?>
                <tr class="border-b border-slate-200 hover:bg-red-50">
                    <td class="py-4 px-4 font-bold"><?php echo htmlspecialchars($row['judul_modul']); ?></td>
                    <td class="py-4 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-4 px-4 text-center">
                        <?php if(!empty($row['file_materi'])): ?>
                            <a href="<?php echo $upload_dir . htmlspecialchars($row['file_materi']); ?>" target="_blank" class="text-blue-600 hover:underline font-semibold text-sm">Lihat</a>
                        <?php else: ?>
                            <span class="text-slate-400 text-sm">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4 text-center space-x-2">
                        <a href="kelola_modul.php?action=edit&id=<?php echo $row['id']; ?>#edit-form" class="inline-block bg-yellow-400 text-black font-bold py-1 px-3 rounded-md text-sm hover:bg-yellow-500 transition-all">Edit</a>
                        <form action="kelola_modul.php" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus modul ini?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-600 text-white font-bold py-1 px-3 rounded-md text-sm hover:bg-red-700 transition-all">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center font-semibold text-slate-500 py-6">Belum ada modul yang ditambahkan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if(isset($conn)) { $conn->close(); }
require_once 'templates/footer.php';
?>