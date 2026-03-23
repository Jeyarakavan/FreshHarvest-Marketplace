<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['txtEmail'];
    $password = $_POST['txtPassword'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Start session
            session_start();
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_id'] = $user['id'];
            
            header("Location: index.php");
            exit();
        } else {
            header("Location: account.php?error=invalid_credentials");
            exit();
        }
    } catch (PDOException $e) {
        die("Login failed: " . $e->getMessage());
    }
} else {
    header("Location: account.php");
    exit();
}
?>