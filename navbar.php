<nav class="bg-white shadow-lg fixed top-0 left-0 w-full z-50">
    <div class="px-4">
        <div class="flex justify-between items-center">
            <div class="flex space-x-4">
                <div>
                    <a href="../index.php" class="flex items-center py-5 px-2 text-blue-500 hover:text-gray-900">
                        <span class="font-bold">NUYA KIDS CARE</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-1">
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <!-- Master Dropdown -->
                        <div class="relative inline-block text-left mr-2">
                            <div>
                                <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-master" aria-expanded="false" aria-haspopup="true">
                                    Utama
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
                                Presensi
                                <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="absolute left-0 right-0 z-10 mt-2 w-48 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden dropdown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-transaksi" tabindex="-1">
                            <div class="py-1" role="none">
                                <a href="../transaksi/presensi.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Karyawan</a>
                                <a href="../transaksi/presensiAnak.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Anak</a>
                            </div>
                        </div>
                    </div>
                    <!-- End Transaksi Dropdown -->
                </div>
            </div>
            <div class="flex items-center">
                <a href="../logout.php" class="py-5 px-3 text-red-500 hover:text-gray-900">Logout</a>
            </div>
        </div>
    </div>
</nav>