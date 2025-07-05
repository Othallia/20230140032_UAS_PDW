<?php
$pageTitle = 'Kelola Pengguna';
$activePage = 'pengguna';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';
$error = '';
$edit_data = null;

// Logika Form (Create, Update, Delete) - (Tidak ada perubahan di sini)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'delete') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['user_id']) { $error = "Anda tidak dapat menghapus akun Anda sendiri."; } 
        else {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql); $stmt->bind_param("i", $id);
            if ($stmt->execute()) { $message = "Pengguna berhasil dihapus."; } else { $error = "Gagal menghapus pengguna."; }
            $stmt->close();
        }
    } else {
        $id = intval($_POST['id'] ?? 0); $nama = trim($_POST['nama']); $email = trim($_POST['email']); $role = trim($_POST['role']); $password = trim($_POST['password']);
        if (empty($nama) || empty($email) || empty($role)) { $error = "Nama, Email, dan Peran tidak boleh kosong."; } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = "Format email tidak valid."; } 
        else {
            if ($id > 0) {
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql); $stmt->bind_param("ssssi", $nama, $email, $role, $hashed_password, $id);
                } else {
                    $sql = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql); $stmt->bind_param("sssi", $nama, $email, $role, $id);
                }
                if ($stmt->execute()) { $message = "Data pengguna berhasil diperbarui."; } else { $error = "Gagal memperbarui data. Mungkin email sudah digunakan."; }
            } else {
                if (empty($password)) { $error = "Password tidak boleh kosong untuk pengguna baru."; } 
                else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql); $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
                    if ($stmt->execute()) { $message = "Pengguna baru berhasil ditambahkan."; } else { $error = "Gagal menambahkan pengguna. Mungkin email sudah digunakan."; }
                }
            }
            $stmt->close();
        }
    }
}
// Logika untuk mengambil data yang akan di-edit
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) { $id = intval($_GET['id']); $sql_edit = "SELECT id, nama, email, role FROM users WHERE id = ?"; $stmt_edit = $conn->prepare($sql_edit); $stmt_edit->bind_param("i", $id); $stmt_edit->execute(); $result_edit = $stmt_edit->get_result(); $edit_data = $result_edit->fetch_assoc(); $stmt_edit->close(); }
?>

<?php if ($message): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $message; ?></p></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg font-bold" role="alert"><p><?php echo $error; ?></p></div><?php endif; ?>

<div class="bg-white p-8 rounded-2xl shadow-md mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6"><?php echo $edit_data ? '✏️ Edit Pengguna' : 'Tambahkan Pengguna Baru'; ?></h2>
    <form action="kelola_pengguna.php" method="POST" class="space-y-6">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nama" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" <?php echo !$edit_data ? 'required' : ''; ?>>
                <?php if ($edit_data): ?>
                    <p class="text-xs text-slate-500 mt-2 font-semibold">Kosongkan jika tidak ingin mengubah password.</p>
                <?php endif; ?>
            </div>
            <div>
                <label for="role" class="block text-sm font-bold text-slate-700 mb-2">Peran (Role)</label>
                <select name="role" id="role" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
                    <option value="mahasiswa" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                    <option value="asisten" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-all">
                <?php echo $edit_data ? 'Update Pengguna' : 'Tambah Pengguna'; ?>
            </button>
            <?php if ($edit_data): ?>
                <a href="kelola_pengguna.php" class="font-bold text-sm text-slate-600 hover:text-red-600">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Daftar Semua Pengguna</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-100">
                <tr>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Nama</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Email</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Role</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Tanggal Daftar</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-700">
                <?php
                $sql_read = "SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC";
                $result_read = $conn->query($sql_read);
                if ($result_read->num_rows > 0):
                    while($row = $result_read->fetch_assoc()):
                ?>
                <tr class="border-b border-slate-200 hover:bg-red-50">
                    <td class="py-4 px-4 font-bold"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="py-4 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo $row['role'] == 'asisten' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                            <?php echo htmlspecialchars($row['role']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-sm"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                    <td class="py-4 px-4 text-center space-x-2">
                        <a href="kelola_pengguna.php?action=edit&id=<?php echo $row['id']; ?>" class="inline-block bg-yellow-400 text-black font-bold py-1 px-3 rounded-md text-sm hover:bg-yellow-500 transition-all">Edit</a>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <form action="kelola_pengguna.php" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-600 text-white font-bold py-1 px-3 rounded-md text-sm hover:bg-red-700 transition-all">Hapus</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center font-semibold text-slate-500 py-6">Tidak ada pengguna terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>