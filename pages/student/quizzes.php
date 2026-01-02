<?php
/**
 * Page: Liste des Quiz d'une Catégorie
 * Affiche tous les quiz actifs d'une catégorie spécifique
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';

// Vérifier que l'utilisateur est étudiant
Security::requireStudent();

// Récupérer l'ID de la catégorie
$categoryId = intval($_GET['category_id'] ?? 0);

if ($categoryId <= 0) {
    header('Location: mes_categories.php');
    exit();
}

// Variables pour la navigation
$currentPage = 'mes_quiz';
$pageTitle = 'Quiz Disponibles';

// Récupérer les données
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['user_nom'];

// Créer les objets
$categoryObj = new Category();
$quizObj = new Quiz();
$questionObj = new Question();

// Récupérer la catégorie
$category = $categoryObj->getById($categoryId);

if (!$category) {
    header('Location: mes_categories.php');
    exit();
}

// Récupérer les quiz actifs de cette catégorie
$quizzes = $quizObj->getActiveByCategory($categoryId, $studentId);
?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/nav_student.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center mb-4">
                <a href="mes_categories.php" class="text-white hover:text-blue-100 mr-4">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-bold">
                        <?= htmlspecialchars($category['nom']) ?>
                    </h1>
                    <p class="text-xl text-blue-100 mt-2"><?= htmlspecialchars($category['description']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (empty($quizzes)): ?>
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun quiz disponible</h3>
                <p class="text-gray-600 mb-6">Il n'y a pas de quiz actifs dans cette catégorie pour le moment</p>
                <a href="mes_categories.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux Catégories
                </a>
            </div>
        <?php else: ?>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    <?= count($quizzes) ?> Quiz Disponible<?= count($quizzes) > 1 ? 's' : '' ?>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($quizzes as $quiz): 
                    $questionCount = $questionObj->countByQuiz($quiz['id']);
                    $isCompleted = $quiz['is_completed'] > 0;
                ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 <?= $isCompleted ? 'border-2 border-green-300' : '' ?>">
                        <div class="p-6">
                            <?php if ($isCompleted): ?>
                                <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold mb-3 inline-block">
                                    <i class="fas fa-check-circle mr-1"></i>Complété
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3">
                                <?= htmlspecialchars($quiz['titre']) ?>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-4">
                                <?= htmlspecialchars($quiz['description'] ?? 'Aucune description') ?>
                            </p>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <i class="fas fa-question-circle mr-2 text-blue-600"></i>
                                <span><?= $questionCount ?> question<?= $questionCount > 1 ? 's' : '' ?></span>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>" 
                                   class="flex-1 text-center bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    <i class="fas fa-play mr-2"></i><?= $isCompleted ? 'Refaire' : 'Commencer' ?>
                                </a>
                                <?php if ($isCompleted): ?>
                                    <a href="mes_resultats.php" 
                                       class="bg-green-100 text-green-700 px-4 py-3 rounded-lg font-semibold hover:bg-green-200 transition"
                                       title="Voir mes résultats">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
