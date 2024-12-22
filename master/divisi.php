<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $nama_divisi = $_POST['nama_divisi'];

        $stmt = $pdo->prepare("INSERT INTO divisi (nama_divisi) VALUES (?)");
        $stmt->execute([$nama_divisi]);
        $success = "Data divisi berhasil disimpan!";
    }
    elseif ($_POST['action'] == 'update') {
        $editId = $_POST['id'];
        $editNamaDivisi = $_POST['edit_nama_divisi'];

        $stmt = $pdo->prepare("UPDATE divisi SET nama_divisi = ? WHERE id = ?");
        $stmt->execute([$editNamaDivisi, $editId]);
        $success = "Data divisi berhasil diupdate!";
    }
    elseif ($_POST['action'] == 'delete') {
        $deleteId = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM divisi WHERE id = ?");
        $stmt->execute([$deleteId]);
        $success = "Data divisi berhasil dihapus!";
    }
}

$stmtDivisi = $pdo->query("SELECT id, nama_divisi FROM divisi");
$divisiList = $stmtDivisi->fetchAll(PDO::FETCH_ASSOC);

$editId = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
$editNamaDivisi = '';
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM divisi WHERE id = ?");
    $stmt->execute([$editId]);
    $divisi = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($divisi) {
        $editNamaDivisi = $divisi['nama_divisi'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Divisi</title>
    <link href="../output.css" rel="stylesheet">
    <style>
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
    <div class="container mx-auto p-4">
        <nav class="bg-white shadow-lg">
            <div class="px-4">
                <div class="flex justify-between">
                    <div class="flex space-x-4">
                        <div>
                            <a href="../index.php" class="flex items-center py-5 px-2 text-gray-700 hover:text-gray-900">
                                <span class="font-bold">LOGO</span>
                            </a>
                        </div>
                        <div class="hidden md:flex items-center space-x-1">
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <!-- Master Dropdown -->
                                <div class="relative inline-block text-left mr-2">
                                    <div>
                                        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-master" aria-expanded="false" aria-haspopup="true">
                                            Master
                                            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute left-0 right-0 z-10 mt-2 w-48 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden dropdown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-master" tabindex="-1">
                                        <div class="py-1" role="none">
                                            <a href="../master/karyawan.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Karyawan</a>
                                            <a href="../master/divisi.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Divisi</a>
                                            <a href="../master/jabatan.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Jabatan</a>
                                            <a href="../master/admin.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Admin</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Master Dropdown -->
                            <?php endif; ?>
                            <!-- Transaksi Dropdown -->
                            <div class="relative inline-block text-left">
                                <div>
                                    <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-transaksi" aria-expanded="false" aria-haspopup="true">
                                        Transaksi
                                        <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="absolute left-0 right-0 z-10 mt-2 w-48 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden dropdown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-transaksi" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="../transaksi/presensi.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Presensi</a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Transaksi Dropdown -->
                            <div>
                                <button type="button" class="inline-flex ml-2 w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-transaksi" aria-expanded="false" aria-haspopup="true">
                                    <a href="../contact.php">Contact</a>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="md:flex items-center space-x-1 mr-4">
                        <a href="../logout.php" class="py-5 px-3 text-gray-700 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Input Data Divisi</h1>
        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="divisi.php" class="mb-4">
            <?php if ($editId): ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $editId; ?>">
                <div class="mb-4">
                    <label for="edit_nama_divisi" class="block text-gray-700">Nama Divisi</label>
                    <input type="text" name="edit_nama_divisi" id="edit_nama_divisi" class="border p-2 w-full" value="<?php echo $editNamaDivisi; ?>" required>
                </div>
                <button type="submit" class="mt-2 bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                <a href="divisi.php" class="ml-2 text-gray-600">Batal</a>
            <?php else: ?>
                <input type="hidden" name="action" value="add">
                <div class="mb-4">
                    <label for="nama_divisi" class="block text-gray-700">Nama Divisi</label>
                    <input type="text" name="nama_divisi" id="nama_divisi" class="border p-2 w-full" required>
                </div>
                <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            <?php endif; ?>
        </form>

        <div class="mb-4">
            <h2 class="text-xl font-bold mb-2">Daftar Divisi</h2>
            <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="border px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Divisi</th>
                        <th class="border px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($divisiList as $divisi): ?>
                        <tr>
                            <td class="border border-gray-200 px-4 py-2"><?php echo $divisi['nama_divisi']; ?></td>
                            <td class="border border-gray-200 px-4 py-2">
                                <!-- Tombol Edit -->
                                <a href="divisi.php?edit_id=<?php echo $divisi['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>

                                <!-- Tombol Delete -->
                                <form method="POST" action="divisi.php" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data divisi ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $divisi['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
