<!-- Admin Topbar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center">
    <div class="flex items-center">
        <button id="mobile-menu-toggle" class="md:hidden text-gray-600 mr-4">
            <i class="fas fa-bars text-2xl"></i>
        </button>
        <h2 class="text-xl font-bold text-gray-800 hidden md:block">
            <?php echo $data['page_title'] ?? 'Dashboard'; ?>
        </h2>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="relative">
            <i class="fas fa-bell text-gray-600 text-xl cursor-pointer"></i>
            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
        </div>
        <a href="<?php echo BASE_URL; ?>/logout" class="text-gray-600 hover:text-red-600">
            <i class="fas fa-sign-out-alt text-xl"></i>
        </a>
    </div>
</header>
