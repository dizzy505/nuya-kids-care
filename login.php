<?php
session_start();
require_once 'db.php'; 

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password AND role = :role");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user_data = $stmt->fetch();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h1 class="text-xl font-bold mb-4">Login</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700">Role</label>
                <select name="role" id="role" class="border p-2 w-full">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-1/2 mr-2">Login</button>
                <a href="register.php" class="bg-green-500 text-white px-4 py-2 rounded w-1/2 text-center">Register</a>
            </div>
        </form>
    </div>
</body>
</html>
