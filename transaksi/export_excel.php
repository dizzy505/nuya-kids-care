<?php
// Create a new file called export_excel.php in the same directory
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="presensi_karyawan.xls"');
header('Cache-Control: max-age=0');

// Fetch data
$stmt = $pdo->query("SELECT karyawan.id, karyawan.nama, presensi.tanggal, presensi.jam_masuk, presensi.jam_pulang, presensi.durasi_kerja 
                     FROM karyawan 
                     LEFT JOIN presensi ON karyawan.id = presensi.karyawan_id");
$data = $stmt->fetchAll();

// Create Excel content
echo "
<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nama Karyawan</th>
        <th>Tanggal Presensi</th>
        <th>Jam Masuk</th>
        <th>Jam Pulang</th>
        <th>Durasi Kerja</th>
    </tr>";

foreach ($data as $row) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['jam_masuk'] . "</td>";
    echo "<td>" . $row['jam_pulang'] . "</td>";
    echo "<td>" . $row['durasi_kerja'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>