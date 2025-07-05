<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once '../config.php';
require_once 'templates/header.php';

// Logika PHP untuk filter (tidak ada perubahan)
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
$mahasiswa_list = $conn->query("SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama");
$where_clauses = [];
$params = [];
$types = '';
if (!empty($_GET['praktikum_id'])) { $where_clauses[] = 'mp.id = ?'; $params[] = $_GET['praktikum_id']; $types .= 'i'; }
if (!empty($_GET['mahasiswa_id'])) { $where_clauses[] = 'u.id = ?'; $params[] = $_GET['mahasiswa_id']; $types .= 'i'; }
if (!empty($_GET['status'])) { $where_clauses[] = 'l.status = ?'; $params[] = $_GET['status']; $types .= 's'; }
$sql_read = "SELECT l.id, u.nama AS nama_mahasiswa, mp.nama_praktikum, m.judul_modul, l.tanggal_kumpul, l.status FROM laporan l JOIN users u ON l.mahasiswa_id = u.id JOIN modul m ON l.modul_id = m.id JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id";
if (!empty($where_clauses)) { $sql_read .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_read .= " ORDER BY l.tanggal_kumpul DESC";
$stmt = $conn->prepare($sql_read);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result_read = $stmt->get_result();
?>

<div class="bg-white p-8 rounded-2xl shadow-md mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Filter Laporan</h2>
    <form action="laporan_masuk.php" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
        <div>
            <label for="praktikum_id" class="block text-sm font-bold text-slate-700 mb-2">Mata Praktikum</label>
            <select name="praktikum_id" id="praktikum_id" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500">
                <option value="">Semua</option>
                <?php mysqli_data_seek($praktikum_list, 0); while($prak = $praktikum_list->fetch_assoc()): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo (isset($_GET['praktikum_id']) && $_GET['praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="mahasiswa_id" class="block text-sm font-bold text-slate-700 mb-2">Mahasiswa</label>
            <select name="mahasiswa_id" id="mahasiswa_id" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500">
                <option value="">Semua</option>
                <?php mysqli_data_seek($mahasiswa_list, 0); while($mhs = $mahasiswa_list->fetch_assoc()): ?>
                    <option value="<?php echo $mhs['id']; ?>" <?php echo (isset($_GET['mahasiswa_id']) && $_GET['mahasiswa_id'] == $mhs['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mhs['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-bold text-slate-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-red-500">
                <option value="">Semua</option>
                <option value="dikumpulkan" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dikumpulkan') ? 'selected' : ''; ?>>Belum Dinilai</option>
                <option value="dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-red-700 transition-all">Filter</button>
            <a href="laporan_masuk.php" class="w-full text-center bg-slate-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-slate-700 transition-all">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-extrabold text-slate-800 mb-6">Hasil Laporan</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-100">
                <tr>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Mahasiswa</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Praktikum / Modul</th>
                    <th class="text-left font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Tanggal Kumpul</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Status</th>
                    <th class="text-center font-extrabold text-slate-600 py-3 px-4 uppercase text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-700">
                <?php if ($result_read->num_rows > 0): while($row = $result_read->fetch_assoc()): ?>
                <tr class="border-b border-slate-200 hover:bg-red-50">
                    <td class="py-4 px-4 font-bold"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                    <td class="py-4 px-4">
                        <div class="font-semibold text-slate-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></div>
                        <div class="text-sm text-slate-500"><?php echo htmlspecialchars($row['judul_modul']); ?></div>
                    </td>
                    <td class="py-4 px-4 text-sm"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                    <td class="py-4 px-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo $row['status'] == 'dinilai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $row['status'] == 'dinilai' ? 'Dinilai' : 'Menunggu'; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <a href="beri_nilai.php?id=<?php echo $row['id']; ?>" class="inline-block bg-red-600 text-white font-bold py-2 px-4 rounded-lg text-sm hover:bg-red-700 transition-all">
                            <?php echo $row['status'] == 'dinilai' ? 'Lihat/Edit Nilai' : 'Beri Nilai'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center font-semibold text-slate-500 py-6">Tidak ada laporan yang cocok dengan filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt->close();
require_once 'templates/footer.php';
?>