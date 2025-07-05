<?php
// PHP LOGIC (atas) TETAP SAMA SEPERTI ASLINYA, TIDAK ADA PERUBAHAN.
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') { header("Location: asisten/dashboard.php"); }
    elseif ($_SESSION['role'] == 'mahasiswa') { header("Location: mahasiswa/dashboard.php"); }
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'asisten') { header("Location: asisten/dashboard.php"); }
                elseif ($user['role'] == 'mahasiswa') { header("Location: mahasiswa/dashboard.php"); }
                exit();
            } else { $message = "Password yang Anda masukkan salah."; }
        } else { $message = "Akun dengan email tersebut tidak ditemukan."; }
        $stmt->close();
    }
}
if(isset($conn)) { $conn->close(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMPRAK</title>
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
            <h2 class="text-3xl font-black text-center text-slate-800 mb-2">Login ke Akunmu</h2>
            <p class="text-center text-slate-500 mb-6 font-semibold">HALO! Selamat datang kembali!</p>

            <?php
                if (isset($_GET['status']) && $_GET['status'] == 'registered') {
                    echo '<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 font-semibold text-center text-sm">Registrasi berhasil! Silakan login.</div>';
                }
                if (!empty($message)) {
                    echo '<div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 font-semibold text-center text-sm">' . htmlspecialchars($message) . '</div>';
                }
            ?>
            <form action="login.php" method="post" class="space-y-5">
                <div>
                    <label for="email" class="block text-slate-700 font-bold text-sm mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" required>
                </div>
                <div>
                    <label for="password" class="block text-slate-700 font-bold text-sm mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" required>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 transition-colors duration-300 text-lg">
                    Masuk Sekarang
                </button>
            </form>
            <div class="text-center mt-6 text-sm">
                <p class="font-semibold text-slate-600">Belum punya akun? <a href="register.php" class="text-red-600 hover:underline font-bold">Daftar di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>