<?php
require_once 'Utilisateur.php';

class Client extends Utilisateur
{
    public function __construct($nom = null, $prenom = null, $email = null, $telephone = null, $adresse = null)
    {
        parent::__construct($nom, $prenom, $email, $telephone, 'client');
        $this->adresse = $adresse;
    }

    public function rechercherVoiture($filtres = [])
    {
        require_once 'Voiture.php';
        $voiture = new Voiture();
        return $voiture->rechercher($filtres);
    }

    public function ajouterFavori($voitureId)
    {
        require_once 'Favori.php';
        $favori = new Favori();
        return $favori->basculer($this->id, $voitureId);
    }

    public function reserverEssai($voitureId, $dateEssai)
    {
        require_once 'Reservation.php';
        $reservation = new Reservation();
        return $reservation->creer($this->id, $voitureId, $dateEssai);
    }
}