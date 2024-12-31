<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="presensi_karyawan.xls"');
header('Cache-Control: max-age=0');

date_default_timezone_set('Asia/Jakarta');
$current_month = date('m');
$current_year = date('Y');
$num_days = date('t'); // Jumlah hari dalam bulan saat ini

// Ambil data karyawan
$employees_stmt = $pdo->query("SELECT id, nama FROM karyawan ORDER BY nama ASC");
$employees = $employees_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data presensi
$attendance_stmt = $pdo->prepare("
    SELECT karyawan_id, tanggal, jam_masuk, jam_pulang, durasi_kerja, lokasi_masuk, lokasi_pulang
    FROM presensi
    WHERE tanggal BETWEEN :start_date AND :end_date
");
$attendance_stmt->execute([
    'start_date' => "$current_year-$current_month-01",
    'end_date' => "$current_year-$current_month-$num_days"
]);
$attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

// Header untuk laporan Excel
echo "
<table border='0'>
    <tr>
        <td colspan='7' style='text-align: center; font-weight: bold; font-size: 16px;'>Rekapitulasi Presensi Karyawan - $current_month/$current_year</td>
    </tr>
    <tr>
        <td colspan='7'>&nbsp;</td> <!-- Baris kosong sebagai pemisah -->
    </tr>
</table>
";

// Tabel data presensi
echo "
<table border='1'>
    <tr>
        <th>Nama Karyawan</th>
        <th>Tanggal</th>
        <th>Jam Masuk</th>
        <th>Lokasi Masuk</th>
        <th>Jam Pulang</th>
        <th>Lokasi Pulang</th>
        <th>Durasi Kerja</th>
    </tr>";

// Iterasi setiap karyawan
foreach ($employees as $employee) {
    $id = $employee['id'];
    $name = $employee['nama'];
    $total_duration = 0; // Total durasi kerja dalam jam untuk karyawan ini

    // Iterasi tanggal dalam bulan
    for ($day = 1; $day <= $num_days; $day++) {
        $date = date('Y-m-d', strtotime("$current_year-$current_month-$day"));

        // Ambil presensi untuk tanggal dan karyawan ini
        $attendance = isset($attendance_records[$id]) ? array_filter($attendance_records[$id], function ($record) use ($date) {
            return $record['tanggal'] === $date;
        }) : [];
        $attendance = reset($attendance); // Ambil data pertama jika ada

        // Tambahkan durasi kerja ke total
        $durasi_kerja = isset($attendance['durasi_kerja']) ? (int)$attendance['durasi_kerja'] : 0;
        $total_duration += $durasi_kerja;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td>" . htmlspecialchars($date) . "</td>";
        echo "<td>" . ($attendance['jam_masuk'] ?? '-') . "</td>";
        echo "<td>" . ($attendance['lokasi_masuk'] ?? '-') . "</td>";
        echo "<td>" . ($attendance['jam_pulang'] ?? '-') . "</td>";
        echo "<td>" . ($attendance['lokasi_pulang'] ?? '-') . "</td>";
        echo "<td>" . ($durasi_kerja > 0 ? $durasi_kerja . " jam" : '-') . "</td>";
        echo "</tr>";
    }

    // Tambahkan baris total durasi kerja untuk karyawan ini
    echo "<tr style='font-weight: bold; background-color: #f0f0f0;'>";
    echo "<td colspan='6' style='text-align: right;'>Total Durasi Kerja:</td>";
    echo "<td>" . $total_duration . " jam</td>";
    echo "</tr>";
}

echo "</table>";
?>
