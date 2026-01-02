<?php
/**
 * Action: Soumettre un Quiz
 * Traite la soumission d'un quiz, calcule le score et enregistre le résultat
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Question.php';
require_once '../../classes/Result.php';

// Vérifier que l'utilisateur est étudiant
Security::requireStudent();

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

// Récupérer les données
$quizId = intval($_POST['quiz_id'] ?? 0);
$answers = $_POST['answers'] ?? [];
$studentId = $_SESSION['user_id'];

// Validation du quiz
if ($quizId <= 0) {
    $_SESSION['quiz_error'] = 'Quiz invalide';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

// Créer les objets
$quizObj = new Quiz();
$questionObj = new Question();
$resultObj = new Result();

// Vérifier que le quiz existe et est actif
$quiz = $quizObj->getById($quizId);

if (!$quiz) {
    $_SESSION['quiz_error'] = 'Quiz introuvable';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

if (!$quiz['is_active']) {
    $_SESSION['quiz_error'] = 'Ce quiz n\'est plus disponible';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

// Récupérer toutes les questions du quiz
$questions = $questionObj->getAllByQuiz($quizId);

if (empty($questions)) {
    $_SESSION['quiz_error'] = 'Ce quiz ne contient aucune question';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}

// Vérifier que toutes les questions ont une réponse
if (count($answers) !== count($questions)) {
    $_SESSION['quiz_error'] = 'Veuillez répondre à toutes les questions';
    header('Location: ../../pages/student/take_quiz.php?quiz_id=' . $quizId);
    exit();
}

// Calculer le score
$score = 0;
$totalQuestions = count($questions);

foreach ($questions as $question) {
    $questionId = $question['id'];
    $correctAnswer = $question['correct_option'];
    $studentAnswer = intval($answers[$questionId] ?? 0);
    
    // Vérifier que la réponse est valide (1-4)
    if ($studentAnswer < 1 || $studentAnswer > 4) {
        $_SESSION['quiz_error'] = 'Réponse invalide détectée';
        header('Location: ../../pages/student/take_quiz.php?quiz_id=' . $quizId);
        exit();
    }
    
    // Comparer avec la bonne réponse
    if ($studentAnswer == $correctAnswer) {
        $score++;
    }
}

// Enregistrer le résultat dans la base de données
$resultId = $resultObj->save($quizId, $studentId, $score, $totalQuestions);

if ($resultId) {
    // Calculer le pourcentage
    $percentage = ($score / $totalQuestions) * 100;
    
    // Message de succès avec le score
    $_SESSION['quiz_success'] = "Quiz complété avec succès ! Vous avez obtenu {$score}/{$totalQuestions} ({$percentage}%)";
    
    // Rediriger vers les résultats
    header('Location: ../../pages/student/mes_resultats.php');
    exit();
} else {
    $_SESSION['quiz_error'] = 'Erreur lors de l\'enregistrement du résultat';
    header('Location: ../../pages/student/mes_categories.php');
    exit();
}
