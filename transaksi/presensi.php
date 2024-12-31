<?php
session_start();
require_once '../db.php';

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

$current_month = date('m');
$current_year = date('Y');

$stmt = $pdo->query("SELECT id, nama FROM karyawan ORDER BY nama ASC");
$employees = $stmt->fetchAll();

$num_days = date('t');
$dates = [];
for ($i = 1; $i <= $num_days; $i++) {
    $dates[] = date('Y-m-d', strtotime("$current_year-$current_month-$i"));
}

$attendance_query = $pdo->prepare("
    SELECT karyawan_id, tanggal, jam_masuk, jam_pulang, durasi_kerja, lokasi_masuk, lokasi_pulang 
    FROM presensi 
    WHERE tanggal BETWEEN :start_date AND :end_date
");
$attendance_query->execute([
    'start_date' => "$current_year-$current_month-01",
    'end_date' => "$current_year-$current_month-$num_days"
]);
$attendance_records = $attendance_query->fetchAll(PDO::FETCH_GROUP);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_POST['karyawan_id'];
    $type = $_POST['type'];
    $tanggal = date('Y-m-d');
    $current_time = date('H:i:s');
    $location = $_POST['location'] ?? ''; // New location field

    if ($type == 'masuk') {
        $check_masuk = $pdo->prepare("SELECT * FROM presensi WHERE karyawan_id = :karyawan_id AND tanggal = :tanggal");
        $check_masuk->execute(['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal]);
        
        if ($check_masuk->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO presensi (karyawan_id, tanggal, jam_masuk, lokasi_masuk, durasi_kerja) VALUES (:karyawan_id, :tanggal, :jam_masuk, :lokasi_masuk, 0)");
            $stmt->execute([
                'karyawan_id' => $karyawan_id, 
                'tanggal' => $tanggal, 
                'jam_masuk' => $current_time,
                'lokasi_masuk' => $location
            ]);
            $success = "Presensi masuk berhasil dicatat!";
            header("Refresh:0");
        } else {
            $error = "Sudah melakukan presensi masuk hari ini!";
        }
    } elseif ($type == 'pulang') {
        $check_stmt = $pdo->prepare("SELECT * FROM presensi WHERE karyawan_id = :karyawan_id AND tanggal = :tanggal AND jam_pulang IS NULL");
        $check_stmt->execute(['karyawan_id' => $karyawan_id, 'tanggal' => $tanggal]);
        $existing_record = $check_stmt->fetch();

        if ($existing_record) {
            $jam_masuk = new DateTime($existing_record['jam_masuk']);
            $jam_pulang = new DateTime($current_time);
            $interval = $jam_masuk->diff($jam_pulang);
            $durasi_kerja = $interval->h + ($interval->i / 60);

            $stmt = $pdo->prepare("UPDATE presensi SET 
                jam_pulang = :jam_pulang,
                lokasi_pulang = :lokasi_pulang,
                durasi_kerja = :durasi_kerja 
                WHERE karyawan_id = :karyawan_id 
                AND tanggal = :tanggal 
                AND jam_pulang IS NULL");
                
            $stmt->execute([
                'karyawan_id' => $karyawan_id,
                'tanggal' => $tanggal,
                'jam_pulang' => $current_time,
                'lokasi_pulang' => $location,
                'durasi_kerja' => number_format($durasi_kerja, 2, '.', '')
            ]);
            $success = "Presensi pulang berhasil dicatat!";
            header("Refresh:0");
        } else {
            $error = "Tidak dapat melakukan presensi pulang. Pastikan Anda sudah melakukan presensi masuk hari ini dan belum melakukan presensi pulang.";
        }
    }
}

function calculateTotalHours($records) {
    $total = 0;
    foreach ($records as $record) {
        if (!empty($record['durasi_kerja'])) {
            $total += floatval($record['durasi_kerja']);
        }
    }
    return number_format($total, 2, '.', '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Karyawan</title>
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
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        .attendance-table {
            border-collapse: collapse;
            width: 100%;
        }
        .attendance-table th, .attendance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .attendance-table th {
            background-color: #f8f9fa;
            white-space: nowrap;
        }
        .time-entry {
            margin: 4px 0;
        }
        .sticky-header {
            position: sticky;
            left: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
        .date-cell {
            min-width: 80px;
        }
        .location-cell {
            min-width: 200px;
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
            font-size: 0.875rem;
        }
        .status-column {
            position: sticky;
            left: 120px;
            background-color: #f8f9fa;
            z-index: 9;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container mx-auto">
        <?php include '../navbar.php'; ?>
    </div>

    <div class="container mx-auto p-4">
        <section class="hero py-12">
            <h1 class="text-2xl font-bold mb-4">Presensi Karyawan</h1>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Presensi -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Input Presensi</h2>
                <form method="POST" action="presensi.php" id="presensiForm">
                    <div class="mb-4">
                        <label for="karyawan_id" class="block text-gray-700 mb-2">Nama Karyawan</label>
                        <select name="karyawan_id" id="karyawan_id" class="border rounded p-2 w-full" required>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="location" id="location">
                    <div class="space-x-2">
                        <button type="button" onclick="getLocationAndSubmit('masuk')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            Presensi Masuk
                        </button>
                        <button type="button" onclick="getLocationAndSubmit('pulang')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Presensi Pulang
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Presensi -->
            <div class="table-container bg-white rounded-lg shadow-md overflow-hidden">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="sticky-header">Tanggal</th>
                            <th class="status-column">Status</th>
                            <?php foreach ($employees as $employee): ?>
                                <th colspan="2"><?= htmlspecialchars($employee['nama']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dates as $date): ?>
                            <tr>
                                <td class="sticky-header font-medium" rowspan="2">
                                    <?= date('d-m-Y', strtotime($date)) ?>
                                </td>
                                <td class="status-column">Masuk</td>
                                <?php foreach ($employees as $employee): 
                                    $attendance = isset($attendance_records[$employee['id']]) 
                                        ? array_filter($attendance_records[$employee['id']], function($record) use ($date) {
                                            return $record['tanggal'] == $date;
                                        })
                                        : [];
                                    $attendance = reset($attendance);
                                ?>
                                    <td class="date-cell">
                                        <?= $attendance ? date('H:i', strtotime($attendance['jam_masuk'])) : '-' ?>
                                    </td>
                                    <td class="location-cell">
                                        <?= $attendance && isset($attendance['lokasi_masuk']) ? $attendance['lokasi_masuk'] : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td class="status-column">Pulang</td>
                                <?php foreach ($employees as $employee): 
                                    $attendance = isset($attendance_records[$employee['id']]) 
                                        ? array_filter($attendance_records[$employee['id']], function($record) use ($date) {
                                            return $record['tanggal'] == $date;
                                        })
                                        : [];
                                    $attendance = reset($attendance);
                                ?>
                                    <td class="date-cell">
                                        <?= $attendance && $attendance['jam_pulang'] 
                                            ? date('H:i', strtotime($attendance['jam_pulang'])) 
                                            : '-' ?>
                                    </td>
                                    <td class="location-cell">
                                        <?= $attendance && isset($attendance['lokasi_pulang']) ? $attendance['lokasi_pulang'] : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50">
                            <td class="sticky-header font-medium" colspan="2">Total Jam (1 bulan)</td>
                            <?php foreach ($employees as $employee): ?>
                                <td class="font-medium" colspan="2">
                                    <?= isset($attendance_records[$employee['id']]) 
                                        ? calculateTotalHours($attendance_records[$employee['id']]) 
                                        : '0.00' ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>

    <script>
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function getLocationAndSubmit(type) {
        showLoading();
        
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                // Get location name using reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                    .then(response => response.json())
                    .then(data => {
                        const location = data.display_name;
                        document.getElementById('location').value = location;
                        
                        // Create and append the type input
                        const typeInput = document.createElement('input');
                        typeInput.type = 'hidden';
                        typeInput.name = 'type';
                        typeInput.value = type;
                        document.getElementById('presensiForm').appendChild(typeInput);
                        // Submit the form
                        document.getElementById('presensiForm').submit();
                    })
                    .catch(error => {
                        console.error('Error getting location name:', error);
                        alert('Gagal mendapatkan nama lokasi. Silakan coba lagi.');
                        hideLoading();
                    });
            }, function(error) {
                console.error('Error getting location:', error);
                hideLoading();
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Mohon izinkan akses lokasi untuk melakukan presensi.');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert('Informasi lokasi tidak tersedia.');
                        break;
                    case error.TIMEOUT:
                        alert('Waktu mendapatkan lokasi habis. Silakan coba lagi.');
                        break;
                    default:
                        alert('Terjadi kesalahan saat mendapatkan lokasi.');
                        break;
                }
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            hideLoading();
            alert('Browser Anda tidak mendukung geolokasi.');
        }
    }

    // Dropdown menu functionality
    document.addEventListener('DOMContentLoaded', () => {
        const menus = document.querySelectorAll('.relative.inline-block.text-left');
        
        function closeAllDropdowns() {
            menus.forEach(menu => {
                const dropdown = menu.querySelector('.dropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
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
            
            if (button && dropdown) {
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
            }
        });

        document.addEventListener('click', () => {
            closeAllDropdowns();
        });

        // Form submission handling
        const form = document.getElementById('presensiForm');
        form.addEventListener('submit', function(e) {
            if (!document.getElementById('location').value) {
                e.preventDefault();
                alert('Mohon tunggu hingga lokasi terdeteksi.');
            }
        });
    });
    </script>
</body>
</html>