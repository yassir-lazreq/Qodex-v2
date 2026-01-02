<?php

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';
require_once '../../classes/Quiz.php';

// Vérifier que l'utilisateur est étudiant
Security::requireStudent();

// Variables pour la navigation
$currentPage = 'mes_quiz';
$pageTitle = 'Mes Quiz';

// Récupérer les données de l'utilisateur
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['user_nom'];

// Créer les objets
$categoryObj = new Category();
$quizObj = new Quiz();
// Récupérer les catégories avec quiz actifs
$mesCategories = $categoryObj->getAllCategories();
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/nav_student.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-4">
                <i class="fas fa-question-circle mr-3"></i>Mes Quiz
            </h1>
            <p class="text-xl text-green-100">Parcourez les quiz disponibles par catégorie</p>
        </div>
    </div>

    <!-- Categories List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($mesCategories as $category): ?>
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($category['nom']) ?></h2>
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($category['description']) ?></p>
                    <div class="text-sm text-gray-500 mb-4">
                        Quiz Disponibles: <?= $category['active_quiz_count'] ?>
                    </div>
                    <a href="quizzes.php?category_id=<?= $category['id'] ?>" class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-300">
                        Voir les Quiz
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include '../partials/footer.php'; ?>