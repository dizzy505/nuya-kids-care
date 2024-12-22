<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user';

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="absolute top-4 left-4">
        <button onclick="history.back()" class="flex items-center text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span class="ml-1">Back</span>
        </button>
    </div>
    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h1 class="text-xl font-bold mb-4">Register</h1>
        <form method="POST" action="register.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="border p-2 w-full" required>
            </div>
            <div>
                <input type="submit" value="Register" class="bg-blue-500 text-white p-2 rounded w-full">
            </div>
        </form>
    </div>
</body>
</html>
