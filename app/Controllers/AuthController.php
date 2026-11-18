<?php

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use PDOException;

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $this->auth = new AuthService(new UserRepository(Database::connection($config)));
    }

    public function login(): void
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['_csrf'] ?? null)) {
                $error = 'Votre session a expiré. Veuillez réessayer.';
            } else {
                try {
                    $email = trim($_POST['email'] ?? '');
                    $password = $_POST['mot_de_passe'] ?? '';
                    if ($this->auth->login($email, $password)) {
                        header('Location: index.php');
                        exit();
                    }
                    $error = 'Adresse e-mail ou mot de passe incorrect.';
                } catch (PDOException $exception) {
                    $error = 'Connexion à la base impossible. Vérifiez la configuration MAMP.';
                }
            }
        }
        $csrf = Csrf::token();
        $titrePage = 'Connexion';
        require dirname(__DIR__, 2) . '/header.php';
        require dirname(__DIR__) . '/Views/auth/login.php';
        require dirname(__DIR__, 2) . '/footer.php';
    }

    public function register(): void
    {
        $error = '';
        $role = $_POST['role'] ?? (($_GET['role'] ?? '') === 'vendeur' ? 'vendeur' : 'client');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['_csrf'] ?? null)) {
                $error = 'Votre session a expiré. Veuillez réessayer.';
            } else {
                $role = ($_POST['role'] ?? '') === 'vendeur' ? 'vendeur' : 'client';
                $data = [
                    'nom' => trim($_POST['nom'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'password' => $_POST['mot_de_passe'] ?? '',
                    'role' => $role,
                    'extra' => trim($_POST[$role === 'vendeur' ? 'nom_concession' : 'adresse'] ?? ''),
                ];

                if ($data['nom'] === '' || $data['prenom'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['password']) < 8) {
                    $error = 'Renseignez tous les champs et choisissez un mot de passe de 8 caractères minimum.';
                } elseif ($role === 'vendeur' && $data['extra'] === '') {
                    $error = 'Indiquez le nom de votre concession.';
                } else {
                    try {
                        $this->auth->register($data);
                        $this->auth->login($data['email'], $data['password']);
                        header('Location: ' . ($role === 'vendeur' ? 'publier.php' : 'index.php'));
                        exit();
                    } catch (PDOException $exception) {
                        $error = 'Cette adresse e-mail est déjà utilisée ou la base de données est inaccessible.';
                    }
                }
            }
        }

        $csrf = Csrf::token();
        $titrePage = 'Créer un compte';
        require dirname(__DIR__, 2) . '/header.php';
        require dirname(__DIR__) . '/Views/auth/register.php';
        require dirname(__DIR__, 2) . '/footer.php';
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: index.php');
        exit();
    }
}
