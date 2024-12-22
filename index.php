<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

function getCount($pdo, $table) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM $table");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['count'];
}

try {
    $karyawanCount = getCount($pdo, 'karyawan');
    $anakCount = getCount($pdo, 'anak');
    $adminCount = getCount($pdo, 'users');
    $presensiCount = getCount($pdo, 'presensi');
    $presensiAnakCount = getCount($pdo, 'presensi_anak');
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        footer {
            margin-top: 182px;
        }
        nav {
            z-index: 100;
        }
        .hero{
            padding-top: 120px;
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
        <?php include 'navbar.php'; ?>
    </div>
    <div class="container mx-auto pt-20">
        <!-- Hero Section -->
        <section class="hero py-12">
            <div class="container mx-auto justify-center flex flex-col md:flex-row items-center">
                <div class="hero-text md:w-1/2 md:pr-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4 text-center">NUYA KID'S CARE</h1>
                    <p class="text-gray-700 mb-6 text-center">Kami menyediakan lingkungan yang aman, penuh perhatian, dan edukatif bagi anak-anak untuk belajar, tumbuh, dan berkembang. Tim profesional kami yang berdedikasi berkomitmen untuk mendukung perkembangan individu setiap anak sekaligus menumbuhkan kecintaan terhadap belajar.</p>
                </div>
            </div>
        </section>
        <!-- End Hero Section -->
        <div class="container mx-auto">
            <div class="flex justify-center flex-wrap">
                <div class="card-product bg-white p-4 rounded-lg shadow-lg m-4 flex items-center justify-center transition-transform transform hover:scale-105">
                    <div class="card h-32 w-32 bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <a href="master/karyawan.php" class="text-black mt-2 flex items-center">
                            <i class="fas fa-users mr-2"></i> Karyawan: <?php echo $karyawanCount; ?>
                        </a>
                    </div>
                </div>
                <div class="card-product bg-white p-4 rounded-lg shadow-lg m-4 flex items-center justify-center transition-transform transform hover:scale-105">
                    <div class="card h-32 w-32 bg-gradient-to-r from-green-500 to-green-700 rounded-lg flex items-center justify-center">
                        <a href="master/anak.php" class="text-black mt-2 flex items-center">
                            <i class="fas fa-child mr-2"></i> Anak: <?php echo $anakCount; ?>
                        </a>
                    </div>
                </div>
                <div class="card-product bg-white p-4 rounded-lg shadow-lg m-4 flex items-center justify-center transition-transform transform hover:scale-105">
                    <div class="card h-32 w-32 bg-gradient-to-r from-red-500 to-red-700 rounded-lg flex items-center justify-center">
                        <a href="master/admin.php" class="text-black mt-2 flex items-center">
                            <i class="fas fa-user-shield mr-2"></i> Admin: <?php echo $adminCount; ?>
                        </a>
                    </div>
                </div>
                <div class="card-product bg-white p-4 rounded-lg shadow-lg m-4 flex items-center justify-center transition-transform transform hover:scale-105">
                    <div class="card h-32 w-32 bg-gradient-to-r from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                        <a href="transaksi/presensi.php" class="text-black mt-2 flex items-center">
                            <i class="fas fa-calendar-check mr-2"></i> Presensi Karyawan: <?php echo $presensiCount; ?>
                        </a>
                    </div>
                </div>
                <div class="card-product bg-white p-4 rounded-lg shadow-lg m-4 flex items-center justify-center transition-transform transform hover:scale-105">
                    <div class="card h-32 w-32 bg-gradient-to-r from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                        <a href="transaksi/presensiAnak.php" class="text-black mt-2 flex items-center">
                            <i class="fas fa-calendar-check mr-2"></i> Presensi Anak: <?php echo $presensiAnakCount; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
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