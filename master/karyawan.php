<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $nama = $_POST['nama'];
            $umur = $_POST['umur'];
            $agama = $_POST['agama'];
            $alamat = $_POST['alamat'];

            $stmt = $pdo->prepare("INSERT INTO karyawan (nama, umur, agama, alamat) VALUES (:nama, :umur, :agama, :alamat)");
            $stmt->execute(['nama' => $nama, 'umur' => $umur, 'agama' => $agama, 'alamat' => $alamat]);
            $success = "Data karyawan berhasil disimpan!";
        } elseif ($action == 'edit_form') {
            $id = $_POST['id'];

            $stmt = $pdo->prepare("SELECT * FROM karyawan WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $karyawan = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($karyawan) {
                $editMode = true;
                $editId = $karyawan['id'];
                $editNama = $karyawan['nama'];
                $editUmur = $karyawan['umur'];
                $editAgama = $karyawan['agama'];
                $editAlamat = $karyawan['alamat'];
            }
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $umur = $_POST['umur'];
            $agama = $_POST['agama'];
            $alamat = $_POST['alamat'];
            
            $stmt = $pdo->prepare("UPDATE karyawan SET nama = :nama, umur = :umur, agama = :agama, alamat = :alamat WHERE id = :id");
            $stmt->execute(['nama' => $nama, 'umur' => $umur, 'agama' => $agama, 'alamat' => $alamat, 'id' => $id]);
            $success = "Data karyawan berhasil diupdate!";
        } elseif ($action == 'delete') {
            $id = $_POST['id'];

            $stmt = $pdo->prepare("DELETE FROM karyawan WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $success = "Data karyawan berhasil dihapus!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Karyawan</title>
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
        <h1 class="text-2xl font-bold mb-4">Input Data Karyawan</h1>
        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($editMode) && $editMode): ?>
            <!-- Form Edit Karyawan -->
            <form method="POST" action="karyawan.php">
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
                    <label for="agama" class="block text-gray-700">Agama</label>
                    <input type="text" name="agama" id="agama" class="border p-2 w-full" value="<?php echo $editAgama; ?>" required>
                </div>
                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" class="border p-2 w-full" required><?php echo $editAlamat; ?></textarea>
                </div>
                <div>
                    <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Update
                    </button>
                </div>
            </form>
            <!-- End Form Edit Karyawan -->
        <?php else: ?>
            <!-- Form Tambah Karyawan -->
            <form method="POST" action="karyawan.php">
                <input type="hidden" name="action" value="add">
                <div class="mb-4">
                    <label for="nama" class="block text-gray-700">Nama</label>
                    <input type="text" name="nama" id="nama" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="umur" class="block text-gray-700">Umur</label>
                    <input type="number" name="umur" id="umur" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="agama" class="block text-gray-700">Agama</label>
                    <input type="text" name="agama" id="agama" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" class="border p-2 w-full" required></textarea>
                </div>
                <div>
                    <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                        Simpan
                    </button>
                </div>
            </form>
            <!-- End Form Tambah Karyawan -->
        <?php endif; ?>
        </section>
        <!-- Tampilkan daftar karyawan -->
        <h2 class="text-xl font-bold my-4">Daftar Karyawan</h2>
        <table class="min-w-full bg-white border-collapse border border-gray-200 shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-800">
                <tr>
                    <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Nama</th>
                    <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Umur</th>
                    <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Agama</th>
                    <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Alamat</th>
                    <th class="border px-4 py-2 text-left text-xs text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM karyawan");
                $karyawanList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($karyawanList as $karyawan):
                ?>
                <tr>
                    <td class="border border-gray-200 px-4 py-2"><?php echo $karyawan['nama']; ?></td>
                    <td class="border border-gray-200 px-4 py-2"><?php echo $karyawan['umur']; ?></td>
                    <td class="border border-gray-200 px-4 py-2"><?php echo $karyawan['agama']; ?></td>
                    <td class="border border-gray- 200 px-4 py-2"><?php echo $karyawan['alamat']; ?></td>
                    <td class="border border-gray-200 px-4 py-2">
                        <!-- Tombol Edit -->
                        <form method="POST" action="karyawan.php">
                            <input type="hidden" name="action" value="edit_form">
                            <input type="hidden" name="id" value="<?php echo $karyawan['id']; ?>">
                            <button type="submit" class="text-blue-500 hover:text-blue-700">Edit</button>
                        </form>
                        
                        <!-- Tombol Delete -->
                        <form method="POST" action="karyawan.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $karyawan['id']; ?>">
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