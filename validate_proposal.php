<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

include 'config/db.php';

$user = $_SESSION['user'];

// Pastikan hanya Kaprodi atau Koordinator HIMA yang dapat mengakses
if (!in_array($user['kode_role'], ['PRD', 'KRD', 'FKT', 'BKL'])) {
    echo "Access denied.";
    exit();
}

// Periksa apakah ID dan tindakan (action) diterima
if (isset($_GET['id']) && isset($_GET['action'])) {
    $proposalId = intval($_GET['id']);
    $action = $_GET['action'];
    $role = $_GET['role'];

    // Validasi tindakan
    if (!in_array($action, ['approve', 'decline'])) {
        echo "Invalid action.";
        exit();
    }

    // Tentukan status berdasarkan tindakan
    $status = ($action === 'approve') ? 'Setuju' : 'Tidak Setuju';

    // Tentukan kolom status berdasarkan role
    if ($role == 'KRD') {
        $column = 'koordinator_hima';
    } elseif ($role == 'PRD') {
        $column = 'kaprodi';
    } elseif ($role == 'FKT') {
        $column = 'fakultas';
    
    } elseif ($role == 'BKL') {
        $column = 'bkal';
    } else {
        echo "Invalid role.";
        exit();
    }

    // Perbarui status di database
    $query = "UPDATE proposal SET $column = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $proposalId);

    if ($stmt->execute()) {
        // Set pesan sukses untuk ditampilkan di halaman proposal
        $_SESSION['flash_message'] = "Proposal $status successfully!";
        header("Location: dashboard.php"); // Pengalihan kembali ke halaman proposal
        exit();
    } else {
        $_SESSION['flash_message'] = "Failed to update proposal status.";
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "Invalid request.";
}
?>
