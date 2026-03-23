<?php
require_once 'db.php';

$email = 'admin@freshharvest.com';
$new_password = 'admin123'; // Change this to your desired password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);
    
    echo "Password reset successfully!<br>";
    echo "New password: ".$new_password."<br>";
    echo "Hash: ".$hashed_password;
} catch (PDOException $e) {
    die("Error: ".$e->getMessage());
}
?>