<?php
/**
 * Page: Passer un Quiz
 * Affiche les questions d'un quiz et permet de le compléter
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est étudiant
Security::requireStudent();

// Récupérer l'ID du quiz
$quizId = intval($_GET['quiz_id'] ?? 0);

if ($quizId <= 0) {
    header('Location: mes_categories.php');
    exit();
}

// Variables
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['user_nom'];

// Créer les objets
$quizObj = new Quiz();
$questionObj = new Question();

// Récupérer le quiz
$quiz = $quizObj->getById($quizId);

if (!$quiz) {
    $_SESSION['quiz_error'] = 'Quiz introuvable';
    header('Location: mes_categories.php');
    exit();
}

// Vérifier que le quiz est actif
if (!$quiz['is_active']) {
    $_SESSION['quiz_error'] = 'Ce quiz n\'est plus disponible';
    header('Location: mes_categories.php');
    exit();
}

// Récupérer les questions
$questions = $questionObj->getAllByQuiz($quizId);

if (empty($questions)) {
    $_SESSION['quiz_error'] = 'Ce quiz ne contient aucune question';
    header('Location: mes_categories.php');
    exit();
}

$currentPage = 'mes_quiz';
$pageTitle = $quiz['titre'];
?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/nav_student.php'; ?>

<!-- Main Content -->
<div class="pt-16 pb-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center mb-4">
                <a href="quizzes.php?category_id=<?= $quiz['categorie_id'] ?>" class="text-white hover:text-indigo-100 mr-4">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
                <div class="flex-1">
                    <div class="text-sm text-indigo-200 mb-2">
                        <i class="fas fa-folder mr-1"></i><?= htmlspecialchars($quiz['categorie_nom']) ?>
                    </div>
                    <h1 class="text-4xl font-bold">
                        <?= htmlspecialchars($quiz['titre']) ?>
                    </h1>
                    <?php if ($quiz['description']): ?>
                        <p class="text-xl text-indigo-100 mt-2"><?= htmlspecialchars($quiz['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-indigo-500 bg-opacity-50 rounded-lg p-4 mt-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-question-circle text-2xl mr-3"></i>
                        <span class="text-lg font-semibold"><?= count($questions) ?> Question<?= count($questions) > 1 ? 's' : '' ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-2xl mr-3"></i>
                        <span class="text-lg font-semibold">~<?= count($questions) * 1 ?> min</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="../../actions/student/passe_quiz.php" method="POST" id="quizForm">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
            
            <!-- Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-blue-800 mb-1">Instructions</h3>
                        <p class="text-sm text-blue-700">
                            Sélectionnez la meilleure réponse pour chaque question. Toutes les questions sont obligatoires.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <?php foreach ($questions as $index => $question): ?>
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-start mb-4">
                        <div class="bg-indigo-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold mr-4 flex-shrink-0">
                            <?= $index + 1 ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?= htmlspecialchars($question['question']) ?>
                            </h3>
                        </div>
                    </div>
                    
                    <div class="ml-14 space-y-3">
                        <?php for ($i = 1; $i <= 4; $i++): 
                            $optionKey = 'option' . $i;
                            $optionValue = $question[$optionKey];
                        ?>
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200">
                                <input type="radio" 
                                       name="answers[<?= $question['id'] ?>]" 
                                       value="<?= $i ?>" 
                                       class="mt-1 w-5 h-5 text-indigo-600 focus:ring-indigo-500"
                                       required>
                                <span class="ml-3 text-gray-700 flex-1">
                                    <span class="font-semibold text-indigo-600 mr-2"><?= chr(64 + $i) ?>.</span>
                                    <?= htmlspecialchars($optionValue) ?>
                                </span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Submit Button -->
            <div class="bg-white rounded-xl shadow-md p-6 sticky bottom-4">
                <div class="flex items-center justify-between">
                    <div class="text-gray-600" id="progressText">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        <span class="font-semibold">0/<?= count($questions) ?></span> questions répondues (0%)
                    </div>
                    <button type="submit" 
                            class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold text-lg hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-check-circle mr-2"></i>Soumettre le Quiz
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal (JavaScript) -->
<script>
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const radios = document.querySelectorAll('input[type="radio"]');
    const answered = new Set();
    
    radios.forEach(radio => {
        if (radio.checked) {
            answered.add(radio.name);
        }
    });
    
    const totalQuestions = <?= count($questions) ?>;
    
    if (answered.size < totalQuestions) {
        e.preventDefault();
        alert('Veuillez répondre à toutes les questions avant de soumettre le quiz.');
        return false;
    }
    
    const confirmed = confirm('Êtes-vous sûr de vouloir soumettre vos réponses ? Vous ne pourrez pas les modifier après la soumission.');
    
    if (!confirmed) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php include '../partials/footer.php'; ?>
