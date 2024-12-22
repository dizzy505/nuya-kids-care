<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = 'admin';

            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);
            $success = "Data admin berhasil disimpan!";
        }
        elseif ($_POST['action'] == 'update') {
            $editId = $_POST['id'];
            $editUsername = $_POST['edit_username'];
            $editPassword = $_POST['edit_password'];

            $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password WHERE id = :id");
            $stmt->execute(['username' => $editUsername, 'password' => $editPassword, 'id' => $editId]);
            $success = "Data admin berhasil diupdate!";
        }
        elseif ($_POST['action'] == 'delete') {
            $deleteId = $_POST['id'];

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$deleteId]);
            $success = "Data admin berhasil dihapus!";
        }
    }
}

$stmtAdmin = $pdo->query("SELECT id, username FROM users WHERE role = 'admin'");
$adminList = $stmtAdmin->fetchAll(PDO::FETCH_ASSOC);

$editId = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
$editUsername = '';
$editPassword = '';
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$editId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $editUsername = $admin['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Admin</title>
    <link href="../output.css" rel="stylesheet">
    <style>
        footer {
            margin-top: 182px;
        }
        nav {
            z-index: 100;
        }
        .hero{
            padding-top: 80px;
        }
        .dropdown-enter {
            transform: scale(95%);
            opacity: 0;
            transition: transform 0.1s ease-out, opacity 0.1s ease-out;
        }
        .dropdown-enter-active {
            transform: scale(100%);
            opacity: 1;
        }
        .dropdown-leave {
            transform: scale(100%);
            opacity: 1;
            transition: transform 0.075s ease-in, opacity 0.075s ease-in;
        }
        .dropdown-leave-active {
            transform: scale(95%);
            opacity: 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto">
    <?php include '../navbar.php'; ?>
    </div>
    <div class="container mx-auto p-4">
    <section class="hero py-12">
        <h1 class="text-2xl font-bold mb-4">Input Data Admin</h1>
        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="admin.php" class="mb-4">
            <?php if ($editId): ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $editId; ?>">
                <div class="mb-4">
                    <label for="edit_username" class="block text-gray-700">Username</label>
                    <input type="text" name="edit_username" id="edit_username" class="border p-2 w-full" value="<?php echo $editUsername; ?>" required>
                </div>
                <div class="mb-4">
                    <label for="edit_username" class="block text-gray-700">Password</label>
                    <input type="text" name="edit_password" id="edit_password" class="border p-2 w-full" value="<?php echo $editPassword; ?>" required>
                </div>
                <button type="submit" class="mt-2 bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                <a href="admin.php" class="ml-2 text-gray-600">Batal</a>
            <?php else: ?>
                <input type="hidden" name="action" value="add">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Username</label>
                    <input type="text" name="username" id="username" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Password</label>
                    <input type="text" name="password" id="password" class="border p-2 w-full" required>
                </div>
                <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            <?php endif; ?>
        </form>
    </section>
        <div class="mb-4">
            <h2 class="text-xl font-bold mb-2">Daftar Admin</h2>
            <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="border px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Username</th>
                        <th class="border px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($adminList as $admin): ?>
                        <tr>
                            <td class="border border-gray-200 px-4 py-2"><?php echo $admin['username']; ?></td>
                            <td class="border border-gray-200 px-4 py-2">
                                <!-- Tombol Edit -->
                                <a href="admin.php?edit_id=<?php echo $admin['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>

                                <!-- Tombol Delete -->
                                <form method="POST" action="admin.php" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data admin ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menus = document.querySelectorAll('.relative.inline-block.text-left');
        
            function closeAllDropdowns() {
                menus.forEach(menu => {
                    const dropdown = menu.querySelector('.dropdown');
                    if (!dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('dropdown-leave');
                        dropdown.classList.add('dropdown-leave-active');
                        dropdown.classList.remove('dropdown-enter');
                        dropdown.classList.remove('dropdown-enter-active');
                        setTimeout(() => {
                            dropdown.classList.add('hidden');
                            dropdown.classList.remove('dropdown-leave');
                            dropdown.classList.remove('dropdown-leave-active');
                        }, 75);
                    }
                });
            }

            menus.forEach(menu => {
                const button = menu.querySelector('button');
                const dropdown = menu.querySelector('.dropdown');

                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    closeAllDropdowns();
                    dropdown.classList.toggle('hidden');
                    if (!dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('dropdown-enter');
                        dropdown.classList.add('dropdown-enter-active');
                        dropdown.classList.remove('dropdown-leave');
                        dropdown.classList.remove('dropdown-leave-active');
                    }
                });
            });

            document.addEventListener('click', () => {
                closeAllDropdowns();
            });
        });
    </script>
</body>
</html>
