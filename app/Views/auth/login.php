<section class="form-page"><div class="form-shell">
    <p class="eyebrow eyebrow-dark">HEUREUX DE VOUS REVOIR</p><h1>Connexion</h1><p class="form-intro">Retrouvez vos annonces et vos démarches au même endroit.</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" action="connexion.php" class="stack-form">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <label>Adresse e-mail<input type="email" name="email" autocomplete="email" required></label>
        <label>Mot de passe<input type="password" name="mot_de_passe" autocomplete="current-password" required></label>
        <button class="button button-dark" type="submit">Se connecter <span aria-hidden="true">↗</span></button>
    </form>
    <p class="form-footnote">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
</div></section>
