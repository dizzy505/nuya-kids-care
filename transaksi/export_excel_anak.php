<?php
// Create a new file called export_excel_anak.php in the same directory
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="presensi_anak.xls"');
header('Cache-Control: max-age=0');

// Fetch data
$stmt = $pdo->query("SELECT anak.id, anak.nama, presensi_anak.tanggal, presensi_anak.jam_masuk, 
                            presensi_anak.jam_pulang, presensi_anak.durasi_belajar 
                     FROM anak 
                     LEFT JOIN presensi_anak ON anak.id = presensi_anak.anak_id");
$data = $stmt->fetchAll();

// Create Excel content
echo "
<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nama Anak</th>
        <th>Tanggal Presensi</th>
        <th>Jam Masuk</th>
        <th>Jam Pulang</th>
        <th>Durasi Belajar</th>
    </tr>";

foreach ($data as $row) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . $row['jam_masuk'] . "</td>";
    echo "<td>" . $row['jam_pulang'] . "</td>";
    echo "<td>" . $row['durasi_belajar'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>