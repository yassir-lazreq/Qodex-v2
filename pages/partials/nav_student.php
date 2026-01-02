<?php
/**
 * PARTIAL: Navigation Étudiant
 * Barre de navigation pour les étudiants
 */

// Calculer les initiales
$userName = $userName ?? $_SESSION['user_nom'] ?? 'User';
$initials = strtoupper(substr($userName, 0, 1) . substr(explode(' ', $userName)[1] ?? '', 0, 1));

// Déterminer le chemin de base selon l'emplacement du fichier
$basePath = '';
if (strpos($_SERVER['PHP_SELF'], '/student/') !== false) {
    $basePath = '../';
}
?>
<!-- Navigation Étudiant -->
<nav class="bg-white shadow-lg fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-graduation-cap text-3xl text-indigo-600"></i>
                    <span class="ml-2 text-2xl font-bold text-gray-900">Qodex</span>
                    <span class="ml-3 px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Étudiant</span>
                </div>
                
                <!-- Menu Principal -->
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <!-- Dashboard -->
                    <a href="<?= $basePath ?>student/dashboard.php" 
                        class="<?= ($currentPage ?? '') === 'dashboard' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    
                    <!-- Mes Quiz -->
                    <a href="<?= $basePath ?>student/mes_quiz.php" 
                       class="<?= ($currentPage ?? '') === 'mes_quiz' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-clipboard-list mr-2"></i>Mes catégories
                    </a>
                    
                    <!-- Mes Résultats -->
                    <a href="<?= $basePath ?>student/mes_resultats.php" 
                       class="<?= ($currentPage ?? '') === 'mes_resultats' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-trophy mr-2"></i>Mes Résultats
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center">
                <!-- Avatar avec Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white font-bold">
                            <?= $initials ?>
                        </div>
                        <span class="hidden md:block text-gray-700 font-medium"><?= htmlspecialchars($userName) ?></span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50"
                            style="display: none;">
                        <a href="<?= $basePath ?>auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js pour le menu déroulant -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
