<?php
session_start();
include 'config/db.php';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];

    // Ambil dua angka pertama dari username
    $kode_role = substr($username, 0, 2); 

    switch ($kode_role) {
        case '01':
            $kode_role = 'MHS'; // Mahasiswa
            $role = 'Mahasiswa'; // Role untuk mahasiswa
            break;
        case '02':
            $kode_role = 'PRD'; // Kaprodi
            $role = 'Kaprodi'; // Role untuk Kaprodi
            break;
        case '03':
            $kode_role = 'KRD'; // Koordinator HIMA
            $role = 'Koordinator HIMA'; // Role untuk Koordinator HIMA
            break;
        case '04':
            $kode_role = 'FKT'; // Fakultas
            $role = 'Fakultas'; // Role untuk Fakultas
            break;
        case '05':
            $kode_role = 'BKL'; // BKAL
            $role = 'Biro Kemahasiswaan Alumni'; // Role untuk BKAL
            break;
        default:
            $kode_role = 'Unknown'; // Default jika tidak cocok
            $role = 'Unknown'; // Role tidak diketahui
            break;
    }

    // Periksa username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika username sudah ada
        $_SESSION['error'] = "Username sudah terdaftar!";
        header('Location: register.php');
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, kode_role, role, nama_lengkap) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $kode_role, $role, $nama_lengkap);
        $stmt->execute();
        
        header('Location: login.php');
        exit();
    }
}
?>

<style>

body {
    background: linear-gradient(45deg, #0a3d61, #276a8e, #6cb4e3);
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: background-color 1s ease-out;
    animation: fadeIn 1.5s ease-out;
}

h1 {
    text-align: center;
    color: #F5EFE7;
    margin-bottom: 20px;
    font-size: 2.5rem;
}

.register a {
    text-decoration: none;
    color: black;
}

.register a:hover {
    text-decoration: none;
    color: #1a2b45;
    transform: scale(1.01);
}

.container {
    background-color: #3E5879;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); 
    text-align: center;
    width: 100%;
    max-width: 450px; 
    margin-bottom: 50px; 
    box-sizing: border-box;
}

input[type="text"], 
input[type="password"], 
button {
    width: 100%;
    padding: 14px 18px;
    margin: 15px 0;
    border: none;
    border-radius: 25px;
    outline: none;
    background-color: #F5EFE7;
    color: #213555;
    font-size: 1rem;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    box-sizing: border-box;
}

input[type="text"]::placeholder, 
input[type="password"]::placeholder {
    color: #999;
}

input[type="text"]:focus, 
input[type="password"]:focus {
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    transform: scale(1.02);
}

button {
    background-color: #213555;
    color: #F5EFE7;
    border-radius: 30px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    padding: 14px 18px;
    margin-top: 20px;
}

button:hover {
    background-color: #18273e;
    transform: scale(1.05);
}

button:active {
    background-color: #18273e;
    transform: scale(0.98);
}

p {
    color: #FF6B6B;
    font-size: 1rem;
    margin: 10px 0;
    text-align: center;
}


/** ANIMATION */
@keyframes fadeIn {
    0% {
        transform: translateY(-30px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 25px;
        margin: 0 10px; /* Mengurangi jarak margin */
    }
    h1 {
        font-size: 2.2rem;
    }
}

@media (max-width: 480px) {
    body {
        font-size: 14px;
    }

    h1 {
        font-size: 2rem;
    }

    .container {
        padding: 20px;
    }

    input[type="text"], 
    input[type="password"], 
    button {
        font-size: 0.9rem;
        padding: 12px 15px;
    }
}

</style>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style-login-regist.css">
</head>
<body>
    <h1>Register</h1>
    <div class="container">
        <?php if (isset($_SESSION['error'])) echo "<p style='color: red;'>".$_SESSION['error']."</p>"; ?>
        <form method="POST">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
        <div class="register">
            <p style="color: #F5EFE7;">Sudah memiliki akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>