<?php
require_once '../src/utils/auth.php';
?>

<!DOCTYPE html>
<html>
<head><title>Welcome</title></head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    <a href="logout.php">Logout</a>
    <br>
    <a href="../admin/admin_dashboard.php">Admin Panel</a>
</body>
</html>
