<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Data Kepegawaian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed inset-y-0 left-0 z-30 w-64 bg-indigo-900 text-white transition duration-300 transform lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center mt-8">
                <span class="text-2xl font-semibold">SiData</span>
            </div>
            <nav class="mt-10 px-4">
                <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700 bg-indigo-800">Dashboard</a>
                <a href="#" class="block mt-2 py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">Data Pengajar</a>
                <a href="#" class="block mt-2 py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">Data Karyawan</a>
                <a href="#" class="block mt-2 py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">Laporan</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                
                <div class="flex items-center">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none">
                            <img class="h-full w-full object-cover" src="https://ui-avatars.com/api/?name=Admin" alt="Your avatar">
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10" style="display: none;">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profil</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Keluar</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Body -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                @yield('content')
            </main>
        </div>

        <!-- Overlay for mobile sidebar -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-20 bg-black opacity-50 lg:hidden"
             style="display: none;">
        </div>
    </div>
</body>
</html>