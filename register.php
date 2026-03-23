<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['txtName'];
    $email = $_POST['txtEmail'];
    $password = $_POST['txtPassword'];
    $confirm_password = $_POST['txtConfimPassword'];
    $address = $_POST['txtAddress'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            die("Email already registered.");
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashed_password, $address]);

        // Start session and redirect
        session_start();
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $full_name;
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage());
    }
} else {
    header("Location: account.php");
    exit();
}
?>