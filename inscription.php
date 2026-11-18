<?php
require_once 'Utilisateur.php';
session_start();
$erreur = '';
$roleChoisi = $_POST['role'] ?? (($_GET['role'] ?? '') === 'vendeur' ? 'vendeur' : 'client');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) && $_POST['role'] === 'vendeur' ? 'vendeur' : 'client';
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $extra = trim($_POST[$role === 'vendeur' ? 'nom_concession' : 'adresse'] ?? '');

    if ($nom === '' || $prenom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($motDePasse) < 8) {
        $erreur = 'Renseignez tous les champs et choisissez un mot de passe de 8 caractères minimum.';
    } elseif ($role === 'vendeur' && $extra === '') {
        $erreur = 'Indiquez le nom de votre concession.';
    } else {
        try {
            $utilisateur = new Utilisateur($nom, $prenom, $email, $telephone, $role);
            $utilisateur->register($motDePasse, $extra);
            $utilisateur->seConnecter($motDePasse);
            header('Location: ' . ($role === 'vendeur' ? 'publier.php' : 'index.php'));
            exit();
        } catch (PDOException $exception) {
            $erreur = 'Cette adresse e-mail est déjà utilisée ou la base de données est inaccessible.';
        }
    }
}

$titrePage = 'Créer un compte';
require 'header.php';
?>
<section class="form-page"><div class="form-shell form-shell-wide">
    <p class="eyebrow eyebrow-dark">BIENVENUE CHEZ AUTO-MATCH</p><h1>Créer un compte</h1><p class="form-intro">Achetez ou proposez votre prochaine voiture.</p>
    <?php if ($erreur): ?><div class="alert alert-danger"><?= e($erreur) ?></div><?php endif; ?>
    <form method="POST" action="inscription.php" class="stack-form">
        <fieldset class="role-choice"><legend>Je souhaite</legend><label><input type="radio" name="role" value="client" <?= $roleChoisi === 'client' ? 'checked' : '' ?>> Acheter une voiture</label><label><input type="radio" name="role" value="vendeur" <?= $roleChoisi === 'vendeur' ? 'checked' : '' ?>> Vendre des voitures</label></fieldset>
        <div class="form-two-columns"><label>Prénom<input name="prenom" value="<?= e($_POST['prenom'] ?? '') ?>" autocomplete="given-name" required></label><label>Nom<input name="nom" value="<?= e($_POST['nom'] ?? '') ?>" autocomplete="family-name" required></label></div>
        <label>Adresse e-mail<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required></label>
        <label>Téléphone<input type="tel" name="telephone" value="<?= e($_POST['telephone'] ?? '') ?>" autocomplete="tel"></label>
        <label class="role-extra" data-role="client">Adresse (facultatif)<input name="adresse" value="<?= e($_POST['adresse'] ?? '') ?>" autocomplete="street-address"></label>
        <label class="role-extra" data-role="vendeur">Nom de la concession<input name="nom_concession" value="<?= e($_POST['nom_concession'] ?? '') ?>"></label>
        <label>Mot de passe<input type="password" name="mot_de_passe" autocomplete="new-password" minlength="8" required><small>8 caractères minimum</small></label>
        <button class="button button-dark" type="submit">Créer mon compte <span aria-hidden="true">↗</span></button>
    </form>
    <p class="form-footnote">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
</div></section>
<script>
document.querySelectorAll('input[name="role"]').forEach(function (input) {
    input.addEventListener('change', function () {
        document.querySelectorAll('.role-extra').forEach(function (field) {
            field.hidden = field.dataset.role !== input.value;
        });
    });
});
document.querySelectorAll('.role-extra').forEach(function (field) {
    field.hidden = field.dataset.role !== document.querySelector('input[name="role"]:checked').value;
});
</script>
<?php require 'footer.php'; ?>