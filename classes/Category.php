<?php
/**
 * Classe Category
 * Gère les opérations CRUD sur les catégories
 */

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    public function create($nom, $description, $createdBy)
    {
        if (empty($nom) || empty($createdBy)) {
            return false;
        }

        $sql = "INSERT INTO categories (nom, description, created_by) VALUES (?, ?, ?)";
        try {
            $this->db->query($sql, [$nom, $description, $createdBy]);
            return $this->db->getConnection()->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }


    public function getAllByTeacher($teacherId)
    {
        $sql = "SELECT c.*, 
                COUNT(DISTINCT q.id) as quiz_count
                FROM categories c
                LEFT JOIN quiz q ON c.id = q.categorie_id
                WHERE c.created_by = ?
                GROUP BY c.id
                ORDER BY c.created_at DESC";

        $result = $this->db->query($sql, [$teacherId]);
        return $result->fetchAll();
    }


    public function getById($id): mixed
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }

    //  Vérifie si l'enseignant est propriétaire de la catégorie


    public function isOwner($categoryId, $teacherId)
    {
        $sql = "SELECT id FROM categories WHERE id = ? AND created_by = ?";
        $result = $this->db->query($sql, [$categoryId, $teacherId]);
        return $result->rowCount() > 0;
    }

    // Met à jour une catégorie

    public function update($id, $nom, $description, $teacherId)
    {
        // Vérifier la propriété
        if (!$this->isOwner($id, $teacherId)) {
            return false;
        }

        $sql = "UPDATE categories SET nom = ?, description = ? WHERE id = ?";
        try {

            $this->db->query($sql, [$nom, $description, $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //  Supprime une catégorie

    public function delete($id, $teacherId)
    {
        // Vérifier la propriété
        if (!$this->isOwner($id, $teacherId)) {
            return false;
        }

        // Vérifier s'il y a des quiz associés
        if ($this->hasQuizzes($id)) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE id = ?";
        try {
            $this->db->query($sql, [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Vérifie si une catégorie a des quiz

    public function hasQuizzes($categoryId)
    {
        $sql = "SELECT COUNT(*) as count FROM quiz WHERE categorie_id = ?";
        $result = $this->db->query($sql, [$categoryId]);
        $data = $result->fetch();
        return $data['count'] > 0;
    }

    // Récupère toutes les catégories (pour les sélections)

    public function getAllCategories(): array
    {
        $sql = "SELECT c.id, c.nom, c.description, 
            COUNT(DISTINCT q.id) as quiz_count,
            COUNT(DISTINCT CASE WHEN q.is_active = 1 THEN q.id END) as active_quiz_count
            FROM categories c
            LEFT JOIN quiz q ON c.id = q.categorie_id
            GROUP BY c.id, c.nom, c.description
            HAVING active_quiz_count > 0
            ORDER BY c.nom ASC";
        $result = $this->db->query($sql);
        return $result->fetchAll();
    }
}