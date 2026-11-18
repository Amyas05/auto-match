<?php if ($confirmation):
    $demande = $_SESSION['derniere_demande'];
?>
<section class="confirmation-page container">
    <div class="confirmation-heading"><span class="confirmation-check" aria-hidden="true">✓</span><h1>Votre demande a bien été envoyée.</h1><p>Votre intérêt pour ce <?= e($ad['marque'] . ' ' . $ad['modele']) ?> a été transmis avec vos coordonnées &amp; votre message.<br>Aucune inscription n’est nécessaire.</p></div>
    <div class="confirmation-card">
        <div class="confirmation-vehicle">
            <p class="eyebrow eyebrow-dark">VÉHICULE CONCERNÉ</p>
            <div class="confirmation-photo" style="background-image:url('<?= e($ad['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=800&q=85') ?>')"></div>
            <h2><?= e($ad['marque'] . ' ' . $ad['modele']) ?></h2>
            <p><?= e($ad['titre']) ?></p>
            <strong class="confirmation-price"><?= number_format((float) $ad['prix'], 0, ',', ' ') ?> €</strong>
        </div>
        <div class="confirmation-request">
            <h2>Votre demande de contact</h2>
            <p class="eyebrow eyebrow-dark">Coordonnées transmises</p>
            <dl><dt>Nom</dt><dd><?= e($demande['nom']) ?></dd><dt>E-mail</dt><dd><?= e($demande['email']) ?></dd><?php if ($demande['telephone']): ?><dt>Téléphone</dt><dd><?= e($demande['telephone']) ?></dd><?php endif; ?><dt>Votre message</dt><dd><?= nl2br(e($demande['message'])) ?></dd></dl>
        </div>
    </div>
    <div class="confirmation-actions"><a class="button button-dark" href="index.php#annonces">← Revenir au catalogue</a><a class="confirmation-secondary" href="annonce.php?id=<?= (int) $ad['annonce_id'] ?>">Revoir <?= e($ad['marque'] . ' ' . $ad['modele']) ?></a></div>
</section>
<?php else: ?>
<section class="detail-page container">
    <a class="back-link" href="index.php#annonces">← Retour au catalogue</a>
    <?php if ($message): ?><div class="alert alert-info detail-alert"><?= e($message) ?></div><?php endif; ?>
    <div class="detail-layout">
        <div class="detail-main">
            <h1><?= e($ad['titre']) ?></h1>
            <p class="detail-subtitle"><?= e($ad['marque'] . ' ' . $ad['modele']) ?> · <?= (int) $ad['annee'] ?> · <?= e($ad['carburant']) ?></p>
            <p class="detail-price-mobile"><span>Prix du véhicule</span><strong><?= number_format((float) $ad['prix'], 0, ',', ' ') ?> €</strong></p>
            <div class="detail-photo" style="background-image:url('<?= e($ad['image_url'] ?: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=90') ?>')"></div>
            <h2 class="detail-spec-title">L’essentiel du véhicule</h2>
            <div class="spec-grid"><div><span>Année</span><strong><?= (int) $ad['annee'] ?></strong></div><div><span>Kilométrage</span><strong><?= number_format((int) $ad['kilometrage'], 0, ',', ' ') ?> km</strong></div><div><span>Énergie</span><strong><?= e($ad['carburant']) ?></strong></div></div>
        </div>
        <aside class="detail-aside contact-aside">
            <h2>Ce véhicule vous intéresse ?</h2>
            <p class="contact-intro">Demandez à être contacté au sujet de ce <?= e($ad['marque'] . ' ' . $ad['modele']) ?>, sans vous inscrire.</p>
            <form method="POST" class="contact-form">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>"><input type="hidden" name="demande_contact" value="1">
                <label>Nom *<input class="<?= !empty($formErrors['nom']) ? 'field-invalid' : '' ?>" type="text" name="nom" value="<?= e($_POST['nom'] ?? '') ?>" placeholder="Votre nom"><small>À renseigner pour vous identifier.</small><?php if (!empty($formErrors['nom'])): ?><em><?= e($formErrors['nom']) ?></em><?php endif; ?></label>
                <label>E-mail *<input class="<?= !empty($formErrors['email']) ? 'field-invalid' : '' ?>" type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" placeholder="vous@exemple.fr"><small>L’adresse à laquelle nous vous rejoindrons.</small><?php if (!empty($formErrors['email'])): ?><em><?= e($formErrors['email']) ?></em><?php endif; ?></label>
                <label>Téléphone <span>(facultatif)</span><input type="tel" name="telephone" value="<?= e($_POST['telephone'] ?? '') ?>" placeholder="06 12 34 56 78"><small>À renseigner si vous préférez être appelé.</small></label>
                <label>Message<input class="message-input <?= !empty($formErrors['message']) ? 'field-invalid' : '' ?>" type="text" name="message" value="<?= e($_POST['message'] ?? 'Bonjour, je souhaite en savoir plus sur ce ' . $ad['marque'] . ' ' . $ad['modele'] . ' et convenir d’un échange. Merci.') ?>"><small>Précisez votre question ou votre demande.</small><?php if (!empty($formErrors['message'])): ?><em><?= e($formErrors['message']) ?></em><?php endif; ?></label>
                <button class="button button-dark contact-submit" type="submit">Envoyer ma demande <span aria-hidden="true">↗</span></button>
            </form>
            <p class="secure-note">Cette demande concerne uniquement cette annonce. Aucun paiement n’est effectué sur ce site.</p>
        </aside>
    </div>
</section>
<?php endif; ?>
