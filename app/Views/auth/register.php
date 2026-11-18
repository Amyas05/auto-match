<section class="form-page"><div class="form-shell form-shell-wide">
    <p class="eyebrow eyebrow-dark">BIENVENUE CHEZ AUTO-MATCH</p><h1>Créer un compte</h1><p class="form-intro">Achetez ou proposez votre prochaine voiture.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" action="inscription.php" class="stack-form">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <fieldset class="role-choice"><legend>Je souhaite</legend>
            <label><input type="radio" name="role" value="client" <?= $role === 'client' ? 'checked' : '' ?>> Acheter une voiture</label>
            <label><input type="radio" name="role" value="vendeur" <?= $role === 'vendeur' ? 'checked' : '' ?>> Vendre des voitures</label>
        </fieldset>
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
document.querySelectorAll('input[name="role"]').forEach(input => input.addEventListener('change', () => {
    document.querySelectorAll('.role-extra').forEach(field => field.hidden = field.dataset.role !== input.value);
}));
document.querySelectorAll('.role-extra').forEach(field => field.hidden = field.dataset.role !== document.querySelector('input[name="role"]:checked').value);
</script>
