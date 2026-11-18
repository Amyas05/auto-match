<?php

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Repositories\AdRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\RequestRepository;
use PDOException;

require_once dirname(__DIR__, 2) . '/DemoData.php';

class AdController
{
    public function show(): void
    {
        $adId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $modeDemo = false;
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $ad = false;

        try {
            $repository = new AdRepository(Database::connection($config));
            $ad = $adId ? $repository->findPublished((int) $adId) : false;
        } catch (PDOException $exception) {
            $modeDemo = true;
            $ad = $adId ? \DemoData::trouver((int) $adId) : false;
        }

        if (!$ad) {
            http_response_code(404);
            $titrePage = 'Annonce introuvable';
            require dirname(__DIR__, 2) . '/header.php';
            echo '<section class="empty-state page-empty"><h1>Cette annonce n’est plus disponible.</h1><a class="button button-dark" href="index.php">Retour aux annonces</a></section>';
            require dirname(__DIR__, 2) . '/footer.php';
            return;
        }

        $message = '';
        $confirmation = isset($_GET['confirmation']) && $_GET['confirmation'] === '1'
            && isset($_SESSION['derniere_demande'])
            && (int) ($_SESSION['derniere_demande']['annonce_id'] ?? 0) === (int) $ad['annonce_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST['_csrf'] ?? null)) {
                $message = 'Votre session a expiré. Veuillez réessayer.';
            } elseif (isset($_POST['demande_contact'])) {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $contenu = trim($_POST['message'] ?? '');

                if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $contenu === '') {
                    $message = 'Veuillez vérifier les champs signalés.';
                    $formErrors = [
                        'nom' => $nom === '' ? 'Votre nom est requis.' : '',
                        'email' => !filter_var($email, FILTER_VALIDATE_EMAIL) ? 'Saisissez une adresse e-mail valide.' : '',
                        'message' => $contenu === '' ? 'Votre message est requis.' : '',
                    ];
                } else {
                    $_SESSION['derniere_demande'] = [
                        'annonce_id' => (int) $ad['annonce_id'],
                        'nom' => $nom,
                        'email' => $email,
                        'telephone' => $telephone,
                        'message' => $contenu,
                    ];
                    header('Location: annonce.php?id=' . (int) $ad['annonce_id'] . '&confirmation=1');
                    exit();
                }
            } elseif ($modeDemo) {
                $message = 'Les annonces sont en mode aperçu. Démarrez MySQL pour enregistrer vos demandes.';
            } else {
                $user = Session::get('utilisateur');
                if (!$user) { header('Location: connexion.php'); exit(); }
                if (($user['role'] ?? '') !== 'client') {
                    $message = 'Cette action est réservée aux comptes client.';
                } else {
                    $database = Database::connection($config);
                    if (isset($_POST['favori'])) {
                        (new FavoriteRepository($database))->toggle((int) $user['id'], (int) $ad['id']);
                        $message = 'Votre sélection a été mise à jour.';
                    } elseif (isset($_POST['reservation'])) {
                        $date = $_POST['date_essai'] ?? '';
                        if ($date < date('Y-m-d\TH:i')) $message = 'Choisissez une date d’essai dans le futur.';
                        else $message = (new RequestRepository($database))->reserve((int) $user['id'], (int) $ad['id'], date('Y-m-d H:i:s', strtotime($date))) ? 'Votre demande d’essai a bien été envoyée.' : 'Cette voiture n’est plus disponible.';
                    } elseif (isset($_POST['commande'])) {
                        $message = (new RequestRepository($database))->order((int) $user['id'], (int) $ad['id']) ? 'Votre demande d’achat est enregistrée.' : 'Cette voiture n’est plus disponible.';
                    }
                }
            }
        }

        $favorite = false;
        $user = Session::get('utilisateur');
        if (!$modeDemo && $user && ($user['role'] ?? '') === 'client') {
            try { $favorite = (new FavoriteRepository(Database::connection($config)))->contains((int) $user['id'], (int) $ad['id']); } catch (PDOException $exception) {}
        }

        $titrePage = $confirmation ? 'Demande envoyée' : $ad['marque'] . ' ' . $ad['modele'];
        $csrf = Csrf::token();
        $formErrors = $formErrors ?? [];
        require dirname(__DIR__, 2) . '/header.php';
        require dirname(__DIR__) . '/Views/ads/show.php';
        require dirname(__DIR__, 2) . '/footer.php';
    }
}
