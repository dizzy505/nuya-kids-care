<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="presensi_anak.xls"');
header('Cache-Control: max-age=0');

$stmt = $pdo->query("
    SELECT anak.nama, presensi_anak.tanggal, presensi_anak.jam_masuk, 
           presensi_anak.jam_pulang, presensi_anak.durasi_belajar,
           presensi_anak.lokasi_masuk, presensi_anak.lokasi_pulang
    FROM anak 
    LEFT JOIN presensi_anak ON anak.id = presensi_anak.anak_id
    ORDER BY anak.nama, presensi_anak.tanggal
");
$data = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

echo "
<table border='0'>
    <tr>
        <td colspan='7' style='text-align: center; font-weight: bold; font-size: 16px;'>Rekapitulasi Presensi Anak</td>
    </tr>
    <tr>
        <td colspan='7'>&nbsp;</td> <!-- Baris kosong sebagai pemisah -->
    </tr>
</table>
<table border='1'>
    <tr>
        <th>Nama Anak</th>
        <th>Tanggal Presensi</th>
        <th>Jam Masuk</th>
        <th>Lokasi Masuk</th>
        <th>Jam Pulang</th>
        <th>Lokasi Pulang</th>
        <th>Durasi Belajar</th>
    </tr>";

// Iterasi setiap anak
foreach ($data as $name => $records) {
    $total_duration = 0; // Total durasi belajar untuk anak ini

    foreach ($records as $row) {
        $durasi_belajar = isset($row['durasi_belajar']) ? (int)$row['durasi_belajar'] : 0;
        $total_duration += $durasi_belajar;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td>" . htmlspecialchars($row['tanggal'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['jam_masuk'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['lokasi_masuk'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['jam_pulang'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['lokasi_pulang'] ?? '-') . "</td>";
        echo "<td>" . ($durasi_belajar > 0 ? $durasi_belajar . " jam" : '-') . "</td>";
        echo "</tr>";
    }

    // Tambahkan baris total durasi belajar untuk anak ini
    echo "<tr style='font-weight: bold; background-color: #f0f0f0;'>";
    echo "<td colspan='6' style='text-align: right;'>Total Durasi Belajar:</td>";
    echo "<td>" . $total_duration . " jam</td>";
    echo "</tr>";
}

echo "</table>";
?>
