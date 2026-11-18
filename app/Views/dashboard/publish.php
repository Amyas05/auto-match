<section class="form-page"><div class="form-shell form-shell-wide">
    <p class="eyebrow eyebrow-dark">ESPACE VENDEUR</p><h1>Présentez votre voiture.</h1><p class="form-intro">Une fiche claire aide les acheteurs à se projeter.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" action="publier.php" class="stack-form">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
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
