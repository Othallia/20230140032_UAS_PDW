<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'praktikum_saya';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';
$mahasiswa_id = $_SESSION['user_id'];
?>

<?php if(isset($_GET['status'])): ?>
    <div class="mb-4 p-4 rounded-xl font-bold <?php echo $_GET['status'] == 'sukses' ? 'bg-green-100 text-green-800 border-2 border-green-300' : 'bg-red-100 text-red-800 border-2 border-red-300'; ?>">
        <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<div>
    <h2 class="text-2xl font-bold text-slate-700 mb-6">Daftar Praktikum yang Kamu Ikuti</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php
        $sql = "SELECT mp.id, mp.nama_praktikum, mp.deskripsi FROM mata_praktikum mp JOIN pendaftaran_praktikum pp ON mp.id = pp.mata_praktikum_id WHERE pp.mahasiswa_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mahasiswa_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>
        <a href="detail_praktikum.php?id=<?php echo $row['id']; ?>" class="block group">
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 border-2 border-transparent hover:border-red-300 transition-all duration-300 h-full flex flex-col">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4 transition-all duration-300 group-hover:scale-110">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="flex-grow">
                    <h3 class="font-extrabold text-lg text-slate-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-slate-500 text-sm mt-1"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                 <div class="mt-4">
                    <span class="font-bold text-red-600 text-sm">Lihat Detail →</span>
                </div>
            </div>
        </a>
        <?php
            endwhile;
        else:
        ?>
        <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm">
            <p class="text-slate-500 font-bold text-xl">Kamu belum daftar praktikum apapun.</p>
            <a href="katalog.php" class="mt-4 inline-block bg-gradient-to-br from-red-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl hover:from-red-600 hover:to-red-700 transition-all">
                Yuk, Cari Praktikum!
            </a>
        </div>
        <?php
        endif;
        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>