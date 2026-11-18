<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$utilisateurConnecte = isset($_SESSION['utilisateur']) ? $_SESSION['utilisateur'] : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#5679c2">
    <title><?= isset($titrePage) ? e($titrePage) . ' | Auto-Match' : 'Auto-Match | Voitures d’occasion' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar site-navbar container" aria-label="Navigation principale">
        <a class="navbar-brand brand" href="index.php">AUTO-MATCH</a>
        <div class="header-actions">
            <a class="account-button" href="<?= $utilisateurConnecte ? 'espace.php' : 'connexion.php' ?>">Mon compte</a>
        </div>
        <div class="dropdown navbar-menu">
            <button class="menu-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end menu-dropdown">
                <li><a class="dropdown-item" href="index.php">Accueil</a></li>
                <li><a class="dropdown-item" href="index.php#apropos">À propos</a></li>
                <li><a class="dropdown-item" href="index.php#aide">Aide</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="index.php#annonces">Annonces</a></li>
            <?php if ($utilisateurConnecte && $utilisateurConnecte['role'] === 'vendeur'): ?>
                <li><a class="dropdown-item" href="publier.php">Déposer une annonce</a></li>
                <li><a class="dropdown-item" href="espace.php">Mon espace</a></li>
            <?php elseif ($utilisateurConnecte && $utilisateurConnecte['role'] === 'client'): ?>
                <li><a class="dropdown-item" href="espace.php">Mes favoris et demandes</a></li>
            <?php else: ?>
                <li><a class="dropdown-item" href="inscription.php?role=vendeur">Vendre ma voiture</a></li>
            <?php endif; ?>
            <?php if ($utilisateurConnecte): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="deconnexion.php">Déconnexion</a></li>
            <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<main>