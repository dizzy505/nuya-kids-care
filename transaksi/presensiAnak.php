<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anak_id = $_POST['anak_id'];
    $type = $_POST['type'];
    $tanggal = date('Y-m-d');
    $current_time = date('H:i:s');

    if ($type == 'masuk') {
        $stmt = $pdo->prepare("INSERT INTO presensi_anak (anak_id, tanggal, jam_masuk) VALUES (:anak_id, :tanggal, :jam_masuk)");
        $stmt->execute(['anak_id' => $anak_id, 'tanggal' => $tanggal, 'jam_masuk' => $current_time]);
        $success = "presensi_anak masuk berhasil dicatat!";
    } elseif ($type == 'pulang') {
        $stmt = $pdo->prepare("UPDATE presensi_anak SET jam_pulang = :jam_pulang WHERE anak_id = :anak_id AND tanggal = :tanggal");
        $stmt->execute(['anak_id' => $anak_id, 'tanggal' => $tanggal, 'jam_pulang' => $current_time]);
        $success = "presensi_anak pulang berhasil dicatat!";
    }
}

$anak_stmt = $pdo->query("SELECT anak.id, anak.nama, presensi_anak.tanggal, presensi_anak.jam_masuk, presensi_anak.jam_pulang, presensi_anak.durasi_belajar 
                              FROM anak 
                              LEFT JOIN presensi_anak ON anak.id = presensi_anak.anak_id");
$anak_list = $anak_stmt->fetchAll();

$anak_st = $pdo->query("SELECT * FROM anak");
$getanak_list = $anak_st->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Anak</title>
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
    <h1 class="text-2xl font-bold mb-4">Presensi Anak</h1>
    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
    <?php endif; ?>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="border py-2 px-4 text-left">ID</th>
                <th class="border py-2 px-4 text-left">Nama anak</th>
                <th class="border py-2 px-4 text-left">Tanggal Presensi</th>
                <th class="border py-2 px-4 text-left">Jam Masuk</th>
                <th class="border py-2 px-4 text-left">Jam Pulang</th>
                <th class="border py-2 px-4 text-left">Durasi Kerja</th>
            </tr>
        </thead>
        <div class="mb-4">
            <a href="export_excel_anak.php" class="bg-yellow-500 text-white px-4 py-2 rounded inline-block">
                Download Data
            </a>
        </div>
        <tbody class="text-gray-700">
            <?php foreach ($anak_list as $anak): ?>
                <tr>
                    <td class="py-2 px-4"><?php echo $anak['id']; ?></td>
                    <td class="py-2 px-4"><?php echo $anak['nama']; ?></td>
                    <td class="py-2 px-4"><?php echo $anak['tanggal']; ?></td>
                    <td class="py-2 px-4"><?php echo $anak['jam_masuk']; ?></td>
                    <td class="py-2 px-4"><?php echo $anak['jam_pulang']; ?></td>
                    <td class="py-2 px-4"><?php echo $anak['durasi_belajar']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST" action="presensiAnak.php">
        <div class="mb-4">
            <label for="anak_id" class="block text-gray-700">Nama Anak</label>
            <select name="anak_id" id="anak_id" class="border p-2 w-full" required>
                <?php foreach ($getanak_list as $anak): ?>
                    <option value="<?php echo $anak['id']; ?>"><?php echo $anak['nama']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="type" value="masuk" class="mt-2 bg-green-500 text-white px-4 py-2 rounded">Presensi Masuk</button>
        <button type="submit" name="type" value="pulang" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Presensi Pulang</button>
    </form>
</div>
</section>
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
