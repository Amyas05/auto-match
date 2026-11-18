<?php
require_once 'Utilisateur.php';

class Vendeur extends Utilisateur
{
    public function __construct($nom = null, $prenom = null, $email = null, $telephone = null, $nomConcession = null)
    {
        parent::__construct($nom, $prenom, $email, $telephone, 'vendeur');
        $this->nomConcession = $nomConcession;
    }

    public function publierAnnonce($voiture, $titre, $description)
    {
        require_once 'AnnonceModel.php';
        $annonce = new Annonce();
        return $annonce->publier($this->id, $voiture, $titre, $description);
    }

    public function modifierAnnonce($annonceId, $titre, $description)
    {
        require_once 'AnnonceModel.php';
        $annonce = new Annonce();
        return $annonce->modifier($annonceId, $this->id, $titre, $description);
    }

    public function supprimerAnnonce($annonceId)
    {
        require_once 'AnnonceModel.php';
        $annonce = new Annonce();
        return $annonce->archiver($annonceId, $this->id);
    }
}