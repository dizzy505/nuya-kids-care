<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$stmtPengasuh = $pdo->query("SELECT nama FROM karyawan");
$pengasuhList = $stmtPengasuh->fetchAll(PDO::FETCH_COLUMN);

$categories = ['Bayi', 'Toddler', 'Playgroup', 'TK'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $namaAnak = $_POST['nama'];
            $umur = $_POST['umur'];
            $pengasuh = $_POST['pengasuh'];
            $kategori = $_POST['kategori'];

            $stmt = $pdo->prepare("INSERT INTO anak (nama, umur, pengasuh, kategori) VALUES (:nama, :umur, :pengasuh, :kategori)");
            $stmt->execute(['nama' => $namaAnak, 'umur' => $umur, 'pengasuh' => $pengasuh, 'kategori' => $kategori]);
            $success = "Data anak berhasil disimpan!";
        } elseif ($action == 'edit_form') {
            $id = $_POST['id'];

            $stmt = $pdo->prepare("SELECT * FROM anak WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $anak = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($anak) {
                $editMode = true;
                $editId = $anak['id'];
                $editNama = $anak['nama'];
                $editUmur = $anak['umur'];
                $editpengasuh = $anak['pengasuh'];
                $editKategori = $anak['kategori'];
            }
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $umur = $_POST['umur'];
            $pengasuh = $_POST['pengasuh'];
            $kategori = $_POST['kategori'];

            $stmt = $pdo->prepare("UPDATE anak SET nama = :nama, umur = :umur, pengasuh = :pengasuh, kategori = :kategori WHERE id = :id");
            $stmt->execute(['nama' => $nama, 'umur' => $umur, 'pengasuh' => $pengasuh, 'kategori' => $kategori, 'id' => $id]);
            $success = "Data anak berhasil diupdate!";
        } elseif ($action == 'delete') {
            $id = $_POST['id'];

            $stmt = $pdo->prepare("DELETE FROM anak WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $success = "Data anak berhasil dihapus!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Anak</title>
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
    <div class ="container mx-auto">
        <?php include '../navbar.php'; ?>
    </div>
<div class="container mx-auto p-4">
<section class="hero py-12">
    <h1 class="text-2xl font-bold mb-4">Input Data Anak</h1>
    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($editMode) && $editMode): ?>
        <!-- Form Edit Anak -->
        <form method="POST" action="anak.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $editId; ?>">
            <div class="mb-4">
                <label for="nama" class="block text-gray-700">Nama</label>
                <input type="text" name="nama" id="nama" class="border p-2 w-full" value="<?php echo $editNama; ?>" required>
            </div>
            <div class="mb-4">
                <label for="umur" class="block text-gray-700">Umur</label>
                <input type="number" name="umur" id="umur" class="border p-2 w-full" value="<?php echo $editUmur; ?>" required>
            </div>
            <div class="mb-4">
                <label for="kategori" class="block text-gray-700">Kategori</label>
                <select name="kategori" id="kategori" class="border p-2 w-full" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category; ?>" <?php echo ($category == $editKategori) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="pengasuh" class="block text-gray-700">Pengasuh</label>
                <select name="pengasuh" id="pengasuh" class="border p-2 w-full" required>
                    <option value="">Pilih pengasuh</option>
                    <?php foreach ($pengasuhList as $pengasuh): ?>
                        <option value="<?php echo $pengasuh; ?>" <?php echo ($pengasuh == $editpengasuh) ? 'selected' : ''; ?>>
                            <?php echo $pengasuh; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Update
                </button>
            </div>
        </form>
        <!-- End Form Edit Anak -->
    <?php else: ?>
        <!-- Form Tambah Anak -->
        <form method="POST" action="anak.php">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label for="nama" class="block text-gray-700">Nama Anak</label>
                <input type="text" name="nama" id="nama" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="umur" class="block text-gray-700">Umur</label>
                <input type="number" name="umur" id="umur" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="kategori" class="block text-gray-700">Kategori</label>
                <select name="kategori" id="kategori" class="border p-2 w-full" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category; ?>"><?php echo ucfirst($category); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="pengasuh" class="block text-gray-700">Pengasuh</label>
                <select name="pengasuh" id="pengasuh" class="border p-2 w-full" required>
                    <option value="">Pilih Pengasuh</option>
                    <?php foreach ($pengasuhList as $pengasuh): ?>
                        <option value="<?php echo $pengasuh; ?>"><?php echo $pengasuh; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Simpan
                </button>
            </div>
        </form>
        <!-- End Form Tambah Anak -->
    <?php endif; ?>
<section class="hero py-12">
    <!-- Tampilkan daftar anak -->
    <h2 class="text-xl font-bold my-4">Daftar Anak</h2>
    <table class="min-w-full bg-white border-collapse border border-gray-200 shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-800">
        <tr>
            <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Nama Anak</th>
            <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Umur</th>
            <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Kategori</th>
            <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Pengasuh</th>
            <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $pdo->query("SELECT id, nama, umur, kategori, pengasuh FROM anak");
        $anakList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($anakList as $anak):
            ?>
            <tr>
                <td class="border border-gray-200 px-4 py-2"><?php echo $anak['nama']; ?></td>
                <td class="border border-gray-200 px-4 py-2"><?php echo $anak['umur']; ?></td>
                <td class="border border-gray-200 px-4 py-2"><?php echo ucfirst($anak['kategori']); ?></td>
                <td class="border border-gray-200 px-4 py-2"><?php echo $anak['pengasuh']; ?></td>
                <td class="border border-gray-200 px-4 py-2">
                        <!-- Tombol Edit -->
                        <form method="POST" action="anak.php" class="inline">
                            <input type="hidden" name="action" value="edit_form">
                            <input type="hidden" name="id" value="<?php echo $anak['id']; ?>">
                            <button type="submit" class="text-blue-500 hover:text-blue-700">Edit</button>
                        </form>
                        <!-- Tombol Delete -->
                        <form method="POST" action="anak.php" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $anak['id']; ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2">Delete</button>
                        </form>
                    </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
const menus = document.querySelectorAll(".relative.inline-block.text-left");

  function closeAllDropdowns() {
    menus.forEach((menu) => {
      const dropdown = menu.querySelector(".dropdown");
      if (!dropdown.classList.contains("hidden")) {
        dropdown.classList.add("dropdown-leave");
        dropdown.classList.add("dropdown-leave-active");
        setTimeout(() => {
          dropdown.classList.remove("dropdown-enter");
          dropdown.classList.remove("dropdown-enter-active");
          dropdown.classList.remove("dropdown-leave");
          dropdown.classList.remove("dropdown-leave-active");
          dropdown.classList.add("hidden");
        }, 75);
      }
    });
  }

  document.addEventListener("click", (event) => {
    let isClickInside = false;
    menus.forEach((menu) => {
      if (menu.contains(event.target)) {
        isClickInside = true;
      }
    });
    if (!isClickInside) {
      closeAllDropdowns();
    }
  });

  menus.forEach((menu) => {
    const button = menu.querySelector("button");
    const dropdown = menu.querySelector(".dropdown");

    button.addEventListener("click", () => {
      if (dropdown.classList.contains("hidden")) {
        closeAllDropdowns();
        dropdown.classList.remove("hidden");
        dropdown.classList.add("dropdown-enter");
        dropdown.classList.add("dropdown-enter-active");
      } else {
        dropdown.classList.add("dropdown-leave");
        dropdown.classList.add("dropdown-leave-active");
        setTimeout(() => {
          dropdown.classList.remove("dropdown-enter");
          dropdown.classList.remove("dropdown-enter-active");
          dropdown.classList.remove("dropdown-leave");
          dropdown.classList.remove("dropdown-leave-active");
          dropdown.classList.add("hidden");
        }, 75);
      }
    });
  });
});
</script>
</body>
</html>