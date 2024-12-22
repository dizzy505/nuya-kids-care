<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
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
    <div class="container mx-auto p-4">
    <nav class="bg-white shadow-lg">
            <div class="px-4">
                <div class="flex justify-between">
                    <div class="flex space-x-4">
                        <div>
                            <a href="../index.php" class="flex items-center py-5 px-2 text-gray-700 hover:text-gray-900">
                                <span class="font-bold">LOGO</span>
                            </a>
                        </div>
                        <div class="hidden md:flex items-center space-x-1">
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <!-- Master Dropdown -->
                                <div class="relative inline-block text-left mr-2">
                                    <div>
                                        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-master" aria-expanded="false" aria-haspopup="true">
                                            Master
                                            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute left-0 right-0 z-10 mt-2 w-48 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden dropdown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-master" tabindex="-1">
                                        <div class="py-1" role="none">
                                            <a href="../master/karyawan.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Karyawan</a>
                                            <a href="../master/anak.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Anak</a>
                                            <a href="../master/admin.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Admin</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Master Dropdown -->
                            <?php endif; ?>
                            <!-- Transaksi Dropdown -->
                            <div class="relative inline-block text-left mr-2">
                                <div>
                                    <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-transaksi" aria-expanded="false" aria-haspopup="true">
                                        Transaksi
                                        <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="absolute left-0 right-0 z-10 mt-2 w-48 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden dropdown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-transaksi" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="../transaksi/presensi.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Presensi</a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Transaksi Dropdown -->
                            <div>
                                <button type="button" class="inline-flex ml-2 w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-transaksi" aria-expanded="false" aria-haspopup="true">
                                    <a href="../contact.php">Contact</a>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="md:flex items-center space-x-1 mr-4">
                        <a href="logout.php" class="py-5 px-3 text-gray-700 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- Contact Section Start -->
    <section id="contact" class="pt-10 pb-8 flex justify-center">
      <div class="container">
        <div class="w-full px-4">
          <div class="max-w-xl mx-auto text-center mb-16">
            <h2
              class="font-bold text-dark text-3xl mb-4 sm:text-4xl lg:text-5xl"
            >
              Contact Us
            </h2>
          </div>
        </div>

        <form>
          <div class="w-full lg:w-2/3 lg:mx-auto">
            <div class="w-full px-4 mb-8">
              <label for="name" class="text-base text-primary font-bold"
                >Name</label
              >
              <input
                type="text"
                id="name"
                class="w-full shadow-md text-dark p-3 rounded-md focus:outline-none focus:ring-primary focus:ring-1 focus:border-primary"
              />
            </div>
            <div class="w-full px-4 mb-8">
              <label for="email" class="text-base text-primary font-bold"
                >Email</label
              >
              <input
                type="email"
                id="email"
                class="w-full shadow-md text-dark p-3 rounded-md focus:outline-none focus:ring-primary focus:ring-1 focus:border-primary"
              />
            </div>
            <div class="w-full px-4 mb-8">
              <label for="message" class="text-base text-primary font-bold"
                >Message</label
              >
              <textarea
                type="text"
                id="message"
                class="w-full shadow-md text-dark p-3 rounded-md focus:outline-none focus:ring-primary focus:ring-1 focus:border-primary h-32"
              ></textarea>
            </div>
            <div class="w-full px-4">
              <button
                class="text-base font-semibold text-white bg-gray-800 py-3 px-8 rounded-full w-full hover:opacity-80 hover:shadow-lg transition duration-500"
              >
                Send
              </button>
            </div>
          </div>
        </form>
      </div>
    </section>
    <!-- Contact Section End -->
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
