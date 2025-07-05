<?php
// PHP LOGIC (atas) TETAP SAMA SEPERTI ASLINYA, TIDAK ADA PERUBAHAN.
require_once 'config.php';
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($nama) || empty($email) || empty($password) || empty($role)) { $message = "Semua field harus diisi!"; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $message = "Format email tidak valid!"; }
    elseif (!in_array($role, ['mahasiswa', 'asisten'])) { $message = "Peran tidak valid!"; }
    else {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) { $message = "Email sudah terdaftar. Silakan gunakan email lain."; }
        else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);
            if ($stmt_insert->execute()) { header("Location: login.php?status=registered"); exit(); }
            else { $message = "Terjadi kesalahan. Silakan coba lagi."; }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
if(isset($conn)) { $conn->close(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - SIMPRAKTIKUM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f87171, #fecaca);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-sm">
        <div class="bg-white p-8 rounded-2xl shadow-lg">
            <h2 class="text-3xl font-black text-center text-slate-800 mb-2">Buat Akun Baru</h2>
            <p class="text-center text-slate-500 mb-6 font-semibold">Ayo gaabung dengan kami di SIMPRAKTIKUM!</p>
            
            <?php if (!empty($message)): ?>
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 font-semibold text-center text-sm"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="register.php" method="post" class="space-y-5">
                <div>
                    <label for="nama" class="block text-slate-700 font-bold text-sm mb-2">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" required>
                </div>
                 <div>
                    <label for="email" class="block text-slate-700 font-bold text-sm mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" required>
                </div>
                <div>
                    <label for="password" class="block text-slate-700 font-bold text-sm mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" required>
                </div>
                <div>
                    <label for="role" class="block text-slate-700 font-bold text-sm mb-2">Daftar Sebagai</label>
                    <select id="role" name="role" class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-red-400" required>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="asisten">Asisten</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 transition-colors duration-300 text-lg">
                    Buat Akun
                </button>
            </form>
             <div class="text-center mt-6 text-sm">
                <p class="font-semibold text-slate-600">Sudah punya akun? <a href="login.php" class="text-red-600 hover:underline font-bold">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>