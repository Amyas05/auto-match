<?php
require_once 'Utilisateur.php';
session_start();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utilisateur = new Utilisateur(null, null, trim($_POST['email'] ?? ''));
    try {
        if ($utilisateur->seConnecter($_POST['mot_de_passe'] ?? '')) {
            header('Location: index.php');
            exit();
        }
        $erreur = 'Adresse e-mail ou mot de passe incorrect.';
    } catch (PDOException $exception) {
        $erreur = 'Connexion à la base impossible. Vérifiez la configuration MAMP.';
    }
}

$titrePage = 'Connexion';
require 'header.php';
?>
<section class="form-page"><div class="form-shell">
    <p class="eyebrow eyebrow-dark">HEUREUX DE VOUS REVOIR</p><h1>Connexion</h1><p class="form-intro">Retrouvez vos annonces et vos démarches au même endroit.</p>
    <?php if ($erreur): ?><div class="alert alert-danger"><?= e($erreur) ?></div><?php endif; ?>
    <form method="POST" action="connexion.php" class="stack-form">
        <label>Adresse e-mail<input type="email" name="email" autocomplete="email" required></label>
        <label>Mot de passe<input type="password" name="mot_de_passe" autocomplete="current-password" required></label>
        <button class="button button-dark" type="submit">Se connecter <span aria-hidden="true">↗</span></button>
    </form>
    <p class="form-footnote">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
</div></section>
<?php require 'footer.php'; ?>