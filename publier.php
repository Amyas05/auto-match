<?php
require_once 'AnnonceModel.php';
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'vendeur') {
    header('Location: connexion.php');
    exit();
}

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donnees = [
        'marque' => trim($_POST['marque'] ?? ''),
        'modele' => trim($_POST['modele'] ?? ''),
        'annee' => (int) ($_POST['annee'] ?? 0),
        'kilometrage' => (int) ($_POST['kilometrage'] ?? 0),
        'carburant' => trim($_POST['carburant'] ?? ''),
        'prix' => (float) ($_POST['prix'] ?? 0),
        'image_url' => trim($_POST['image_url'] ?? '')
    ];
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($donnees['marque'] === '' || $donnees['modele'] === '' || $donnees['annee'] < 1950 || $donnees['annee'] > (int) date('Y') + 1 || $donnees['prix'] <= 0 || $titre === '' || $description === '') {
        $erreur = 'Vérifiez les informations de la voiture et complétez le titre et la description.';
    } else {
        try {
            $annonce = new Annonce();
            $annonce->publier($_SESSION['utilisateur']['id'], $donnees, $titre, $description);
            header('Location: index.php?publie=1#annonces');
            exit();
        } catch (PDOException $exception) {
            $erreur = 'Impossible d’enregistrer l’annonce. Vérifiez la base de données.';
        }
    }
}

$titrePage = 'Déposer une annonce';
require 'header.php';
?>
<section class="form-page"><div class="form-shell form-shell-wide">
    <p class="eyebrow eyebrow-dark">ESPACE VENDEUR</p><h1>Présentez votre voiture.</h1><p class="form-intro">Une fiche claire aide les acheteurs à se projeter.</p>
    <?php if ($erreur): ?><div class="alert alert-danger"><?= e($erreur) ?></div><?php endif; ?>
    <form method="POST" action="publier.php" class="stack-form">
        <h2 class="form-section-title">La voiture</h2>
        <div class="form-two-columns"><label>Marque<input name="marque" value="<?= e($_POST['marque'] ?? '') ?>" required></label><label>Modèle<input name="modele" value="<?= e($_POST['modele'] ?? '') ?>" required></label></div>
        <div class="form-three-columns"><label>Année<input type="number" name="annee" min="1950" max="<?= (int) date('Y') + 1 ?>" value="<?= e($_POST['annee'] ?? '') ?>" required></label><label>Kilométrage<input type="number" name="kilometrage" min="0" value="<?= e($_POST['kilometrage'] ?? '') ?>" required></label><label>Énergie<select name="carburant" required><option value="">Choisir</option><?php foreach (['Essence', 'Diesel', 'Hybride', 'Électrique'] as $carburant): ?><option <?= ($_POST['carburant'] ?? '') === $carburant ? 'selected' : '' ?>><?= e($carburant) ?></option><?php endforeach; ?></select></label></div>
        <div class="form-two-columns"><label>Prix (€)<input type="number" name="prix" min="1" step="0.01" value="<?= e($_POST['prix'] ?? '') ?>" required></label><label>URL de la photo (facultatif)<input type="url" name="image_url" value="<?= e($_POST['image_url'] ?? '') ?>" placeholder="https://..."></label></div>
        <h2 class="form-section-title">L’annonce</h2>
        <label>Titre<input name="titre" maxlength="180" value="<?= e($_POST['titre'] ?? '') ?>" required></label>
        <label>Description<textarea name="description" rows="5" required><?= e($_POST['description'] ?? '') ?></textarea></label>
        <button class="button button-dark" type="submit">Publier l’annonce <span aria-hidden="true">↗</span></button>
    </form>
</div></section>
<?php require 'footer.php'; ?>