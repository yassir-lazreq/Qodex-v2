<?php
/**
 * Page: Tableau de bord Étudiant
 * Affiche les quiz disponibles par catégorie
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Result.php';
require_once '../../classes/User.php';

// Vérifier que l'utilisateur est étudiant
Security::requireStudent();

// Variables pour la navigation
$currentPage = 'dashboard';
$pageTitle = 'Tableau de bord';

// Récupérer les données de l'utilisateur
$studentId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

// Récupérer les catégories avec quiz actifs
$db = Database::getInstance();
$categoryObj = new Category();
$categories = $categoryObj->getAllCategories();

// Récupérer les statistiques de l'étudiant
$studentObj = new Result();
$stats = $studentObj->getMyStats($studentId);

// Initiales pour l'avatar
$userObj = new User();
$initials = $userObj->initialAvatar($userName);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_student.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-4">Bienvenue, <?= htmlspecialchars($userName) ?> !</h1>
            <p class="text-xl text-green-100 mb-6">Testez vos connaissances avec nos quiz interactifs</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Quiz Complétés -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Quiz Complétés</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $stats['total_quiz'] ?? 0 ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Score Moyen -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Score Moyen</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $stats['moyenne'] ? number_format($stats['moyenne'], 1) . '%' : '0%' ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Meilleur Score -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Meilleur Score</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $stats['meilleur_score'] ? number_format($stats['meilleur_score'], 0) . '%' : '0%' ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-trophy text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz par Catégorie -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quiz Disponibles par Catégorie</h2>
            
            <?php if (empty($categories)): ?>
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Aucun quiz disponible pour le moment</p>
                    <p class="text-gray-400 text-sm mt-2">Revenez plus tard pour de nouveaux quiz !</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($categories as $category): ?>
                        <?php
                        // Récupérer les quiz actifs pour cette catégorie
                        $quizObj = new Quiz();
                        $categoryQuizzes = $quizObj->getActiveByCategory($category['id'], $studentId);
                        ?>
                        
                        <!-- Carte Catégorie -->
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow">
                            <div class="p-6">
                                <!-- En-tête Catégorie -->
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <i class="fas fa-folder text-indigo-600 mr-2"></i>
                                        <?= htmlspecialchars($category['nom']) ?>
                                    </h3>
                                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">
                                        <?= $category['active_quiz_count'] ?> quiz
                                    </span>
                                </div>
                                
                                <?php if ($category['description']): ?>
                                    <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($category['description']) ?></p>
                                <?php endif; ?>
                                
                                <!-- Liste des Quiz -->
                                <div class="space-y-2">
                                    <?php foreach ($categoryQuizzes as $quiz): ?>
                                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1">
                                                        <?= htmlspecialchars($quiz['titre']) ?>
                                                    </h4>
                                                    <?php if ($quiz['description']): ?>
                                                        <p class="text-gray-500 text-xs mb-2"><?= htmlspecialchars(substr($quiz['description'], 0, 60)) ?>...</p>
                                                    <?php endif; ?>
                                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                                        <span><i class="fas fa-clock mr-1"></i><?= date('d/m/Y', strtotime($quiz['created_at'])) ?></span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 flex flex-col gap-2">
                                                    <?php if ($quiz['is_completed'] > 0): ?>
                                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold whitespace-nowrap">
                                                            <i class="fas fa-check mr-1"></i>Complété
                                                        </span>
                                                        <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>" 
                                                           class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold hover:bg-gray-200 transition text-center whitespace-nowrap">
                                                            <i class="fas fa-redo mr-1"></i>Refaire
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>" 
                                                           class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-indigo-700 transition whitespace-nowrap">
                                                            <i class="fas fa-play mr-1"></i>Commencer
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

