<?php
/**
 * Action: Afficher les catégories disponibles pour les étudiants
 * Retourne un JSON avec toutes les catégories actives
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est connecté en tant qu'étudiant
Security::requireStudent();

// Récupérer toutes les catégories avec le nombre de quiz actifs
$category = new Category();
$db = Database::getInstance();

try {
    $categories = $category->getAllCategories();
    
    // Retourner les données en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des catégories'
    ]);
}

