<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_POST['karyawan_id'];
    $type = $_POST['type'];
    $tanggal = date('Y-m-d');
    $current_time = date('H:i:s');

    if ($type == 'masuk') {
        $stmt = $pdo->prepare("INSERT INTO presensi (karyawan_id, tanggal, jam_masuk) VALUES (:karyawan_id, :tanggal, :jam_masuk)");
        $stmt->execute(['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal, 'jam_masuk' => $current_time]);
        $success = "Presensi masuk berhasil dicatat!";
    } elseif ($type == 'pulang') {
        $stmt = $pdo->prepare("UPDATE presensi SET jam_pulang = :jam_pulang WHERE karyawan_id = :karyawan_id AND tanggal = :tanggal");
        $stmt->execute(['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal, 'jam_pulang' => $current_time]);
        $success = "Presensi pulang berhasil dicatat!";
    }
}

$karyawan_stmt = $pdo->query("SELECT karyawan.id, karyawan.nama, presensi.tanggal, presensi.jam_masuk, presensi.jam_pulang, presensi.durasi_kerja 
                              FROM karyawan 
                              LEFT JOIN presensi ON karyawan.id = presensi.karyawan_id");
$karyawan_list = $karyawan_stmt->fetchAll();

$karyawan_st = $pdo->query("SELECT * FROM karyawan");
$getkaryawan_list = $karyawan_st->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi</title>
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
    <h1 class="text-2xl font-bold mb-4">Presensi Karyawan</h1>
    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
    <?php endif; ?>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="border py-2 px-4 text-left">ID</th>
                <th class="border py-2 px-4 text-left">Nama Karyawan</th>
                <th class="border py-2 px-4 text-left">Tanggal Presensi</th>
                <th class="border py-2 px-4 text-left">Jam Masuk</th>
                <th class="border py-2 px-4 text-left">Jam Pulang</th>
                <th class="border py-2 px-4 text-left">Durasi Kerja</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php foreach ($karyawan_list as $karyawan): ?>
                <tr>
                    <td class="py-2 px-4"><?php echo $karyawan['id']; ?></td>
                    <td class="py-2 px-4"><?php echo $karyawan['nama']; ?></td>
                    <td class="py-2 px-4"><?php echo $karyawan['tanggal']; ?></td>
                    <td class="py-2 px-4"><?php echo $karyawan['jam_masuk']; ?></td>
                    <td class="py-2 px-4"><?php echo $karyawan['jam_pulang']; ?></td>
                    <td class="py-2 px-4"><?php echo $karyawan['durasi_kerja']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST" action="presensi.php">
        <div class="mb-4">
            <label for="karyawan_id" class="block text-gray-700">Nama Karyawan</label>
            <select name="karyawan_id" id="karyawan_id" class="border p-2 w-full" required>
                <?php foreach ($getkaryawan_list as $karyawan): ?>
                    <option value="<?php echo $karyawan['id']; ?>"><?php echo $karyawan['nama']; ?></option>
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
