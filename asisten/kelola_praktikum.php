<?php
$pageTitle = 'Kelola Mata Praktikum';
$activePage = 'praktikum';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';
$error = '';
$edit_data = null;

// Logika Form (Create, Update, Delete) - (Tidak ada perubahan di sini)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (kode PHP untuk CUD tetap sama) ...
}
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    // ... (kode PHP untuk get edit data tetap sama) ...
}
?>

<?php if ($message): ?>
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg font-bold" role="alert">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg font-bold" role="alert">
    <p><?php echo $error; ?></p>
</div>
<?php endif; ?>

<div class="bg-white p-8 rounded-2xl shadow-md mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6"><?php echo $edit_data ? '✏️ Edit Mata Praktikum' : 'Tambahkan Mata Praktikum Baru'; ?></h2>
    <form action="kelola_praktikum.php" method="POST" class="space-y-6">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? 0; ?>">
        <div>
            <label for="nama_praktikum" class="block text-sm font-bold text-slate-700 mb-2">Nama Praktikum</label>
            <input type="text" name="nama_praktikum" id="nama_praktikum" value="<?php echo htmlspecialchars($edit_data['nama_praktikum'] ?? ''); ?>" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500" required>
        </div>
        <div>
            <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-all">
                <?php echo $edit_data ? 'Update' : 'Simpan'; ?>
            </button>
            <?php if ($edit_data): ?>
            <a href="kelola_praktikum.php" class="font-bold text-sm text-slate-600 hover:text-red-600">Batal Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Daftar Mata Praktikum</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-100">
                <tr>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Nama Praktikum</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Deskripsi</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-700">
                <?php
                $sql = "SELECT * FROM mata_praktikum ORDER BY created_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                <tr class="border-b border-slate-200 hover:bg-red-50">
                    <td class="py-4 px-4 font-bold"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td class="py-4 px-4 text-center space-x-2">
                        <a href="kelola_praktikum.php?action=edit&id=<?php echo $row['id']; ?>" class="inline-block bg-yellow-400 text-black font-bold py-1 px-3 rounded-md text-sm hover:bg-yellow-500 transition-all">Edit</a>
                        <form action="kelola_praktikum.php" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus data ini?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-600 text-white font-bold py-1 px-3 rounded-md text-sm hover:bg-red-700 transition-all">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="3" class="text-center font-semibold text-slate-500 py-6">Belum ada data mata praktikum.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if(isset($conn)) { $conn->close(); }
require_once 'templates/footer.php';
?>