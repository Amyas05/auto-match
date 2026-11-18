<?php

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Repositories\AdManagementRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\RequestRepository;

class DashboardController
{
    private array $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/database.php';
    }

    public function index(): void
    {
        $user = Session::get('utilisateur');
        if (!$user || !in_array($user['role'] ?? '', ['client', 'vendeur'], true)) {
            header('Location: connexion.php'); exit();
        }

        $database = Database::connection($this->config);
        $estVendeur = $user['role'] === 'vendeur';
        $annonces = [];
        $favoris = [];
        $reservations = [];
        $commandes = [];

        if ($estVendeur) {
            $management = new AdManagementRepository($database);
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archiver'])) {
                if (Csrf::validate($_POST['_csrf'] ?? null)) {
                    $management->archive((int) $_POST['archiver'], (int) $user['id']);
                    header('Location: espace.php?archive=1'); exit();
                }
            }
            $annonces = $management->forSeller((int) $user['id']);
        } else {
            $favorites = new FavoriteRepository($database);
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer_favori'])) {
                if (Csrf::validate($_POST['_csrf'] ?? null)) {
                    $favorites->toggle((int) $user['id'], (int) $_POST['retirer_favori']);
                    header('Location: espace.php?favori=retire#favoris'); exit();
                }
            }
            $favoris = $favorites->forUser((int) $user['id']);
            $requests = new RequestRepository($database);
            $reservations = $requests->reservationsFor((int) $user['id']);
            $commandes = $requests->ordersFor((int) $user['id']);
        }

        $titrePage = $estVendeur ? 'Mon espace vendeur' : 'Mon espace';
        $csrf = Csrf::token();
        require dirname(__DIR__, 2) . '/header.php';
        require dirname(__DIR__) . '/Views/dashboard/index.php';
        require dirname(__DIR__, 2) . '/footer.php';
    }

    public function publish(): void
    {
        $user = Session::get('utilisateur');
        if (!$user || ($user['role'] ?? '') !== 'vendeur') {
            header('Location: connexion.php'); exit();
        }

        $error = '';
        $csrf = Csrf::token();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['_csrf'] ?? null)) {
                $error = 'Votre session a expiré. Veuillez réessayer.';
            } else {
                $car = [
                    'marque' => trim($_POST['marque'] ?? ''), 'modele' => trim($_POST['modele'] ?? ''),
                    'annee' => (int) ($_POST['annee'] ?? 0), 'kilometrage' => (int) ($_POST['kilometrage'] ?? 0),
                    'carburant' => trim($_POST['carburant'] ?? ''), 'prix' => (float) ($_POST['prix'] ?? 0),
                    'image_url' => trim($_POST['image_url'] ?? ''),
                ];
                $title = trim($_POST['titre'] ?? '');
                $description = trim($_POST['description'] ?? '');
                if ($car['marque'] === '' || $car['modele'] === '' || $car['annee'] < 1950 || $car['annee'] > (int) date('Y') + 1 || $car['prix'] <= 0 || $title === '' || $description === '') {
                    $error = 'Vérifiez les informations de la voiture et complétez le titre et la description.';
                } else {
                    try {
                        $repository = new AdManagementRepository(Database::connection($this->config));
                        $repository->publish((int) $user['id'], $car, $title, $description);
                        header('Location: index.php?publie=1#annonces'); exit();
                    } catch (\Throwable $exception) {
                        $error = 'Impossible d’enregistrer l’annonce. Vérifiez la base de données.';
                    }
                }
            }
        }

        $titrePage = 'Déposer une annonce';
        require dirname(__DIR__, 2) . '/header.php';
        require dirname(__DIR__) . '/Views/dashboard/publish.php';
        require dirname(__DIR__, 2) . '/footer.php';
    }
}
