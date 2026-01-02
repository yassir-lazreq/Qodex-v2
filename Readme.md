# 🎓 Qodex - Plateforme de Quiz Éducatifs

Plateforme web interactive pour créer et passer des quiz. Développée en PHP natif avec architecture MVC, orientée sécurité et bonnes pratiques.

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0-38B2AC?style=flat&logo=tailwind-css&logoColor=white)

## ✨ Fonctionnalités

**👨‍🏫 Enseignant** : Gérer catégories, créer quiz/questions, activer/désactiver quiz, voir statistiques  
**👨‍🎓 Étudiant** : Parcourir quiz par catégorie, passer des quiz, consulter résultats et historique  
**🔐 Sécurité** : CSRF protection, BCrypt, requêtes préparées, sessions sécurisées (30min), contrôle d'accès par rôle

## 🛠️ Technologies

**Backend** : PHP 8.1+, MySQL 8.0+, PDO  
**Frontend** : HTML5, TailwindCSS 3.0, JavaScript, Font Awesome  
**Patterns** : MVC, Singleton, OOP, Prepared Statements

## 🚀 Installation

```bash
# 1. Cloner
git clone https://github.com/yassir-lazreq/Qodex-v2.git

# 2. Créer la base de données
CREATE DATABASE qodex_v2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 3. Importer SQL
mysql -u root -p qodex_v2_db < database/quiz_platform.sql

# 4. Configurer (config/database.php)
define('DB_HOST', 'localhost');
define('DB_NAME', 'qodex_v2_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3307');

# 5. Accéder
http://localhost/projects/Qodex-Student-Quiz-Platform-V2
```

## 📁 Structure

```
├── actions/          # Traitement formulaires (login, quiz, CRUD)
├── classes/          # Modèles (Database, Security, User, Quiz, etc.)
├── config/           # Configuration DB + sessions
├── database/         # Schema SQL
├── pages/            # Vues (auth, teacher, student, partials)
└── index.php         # Point d'entrée
```

## 🔒 Sécurité

- **CSRF** : Token sur tous les formulaires → `Security::generateCSRFToken()`
- **XSS** : Nettoyage inputs → `Security::clean()` + `htmlspecialchars()`
- **SQL Injection** : Requêtes préparées PDO
- **Passwords** : BCrypt → `password_hash()` / `password_verify()`
- **Sessions** : HTTPOnly, Secure, SameSite Strict, timeout 30min
- **Access Control** : `requireTeacher()`, `requireStudent()`, `isOwner()`

## 💻 Utilisation

**Enseignant** : Catégories → Quiz → Questions → Activer/Modifier/Supprimer  
**Étudiant** : Dashboard → Catégorie → Commencer Quiz → Soumettre → Voir Résultats

## 🎯 Concepts OOP

✅ **Encapsulation** : Propriétés privées + getters/setters  
✅ **Singleton** : `Database::getInstance()` (connexion unique)  
✅ **Visibilité** : private/public/protected  
✅ **Méthodes statiques** : `Security::hashPassword()`

## 🔌 Endpoints

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/actions/teacher/category_create.php` | POST | Créer catégorie |
| `/actions/teacher/quiz_create.php` | POST | Créer quiz avec questions |
| `/actions/teacher/quiz_toggle.php` | GET | Activer/Désactiver quiz |
| `/actions/student/passe_quiz.php` | POST | Soumettre quiz |
| `/actions/login_action.php` | POST | Connexion |
| `/actions/register_action.php` | POST | Inscription |

## 🚀 Améliorations Futures

- [ ] Rate limiting (anti force brute)
- [ ] Politique de mots de passe renforcée
- [ ] Pagination et recherche/filtres
- [ ] Détails des résultats (bonnes/mauvaises réponses)
- [ ] Export PDF/Excel
- [ ] Mode sombre
- [ ] Chronomètre pour quiz

## 👨‍💻 Auteur

**Yassir Lazreq**  
GitHub: [@yassir-lazreq](https://github.com/yassir-lazreq) | Repository: [Qodex-v2](https://github.com/yassir-lazreq/Qodex-v2)

