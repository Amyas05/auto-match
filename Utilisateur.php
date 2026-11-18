<?php
require_once 'Db.php';

class Utilisateur
{
    protected $id;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $telephone;
    protected $role;
    private $connection;

    public function __construct($nom = null, $prenom = null, $email = null, $telephone = null, $role = 'client')
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->role = $role;
        $db = new Db();
        $this->connection = $db->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function register($password, $extra = null)
    {
        $sql = 'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, role, adresse, nom_concession)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role, :adresse, :nom_concession)';
        $query = $this->connection->prepare($sql);
        return $query->execute([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'telephone' => $this->telephone,
            'role' => $this->role,
            'adresse' => $this->role === 'client' ? $extra : null,
            'nom_concession' => $this->role === 'vendeur' ? $extra : null
        ]);
    }

    public function seConnecter($password)
    {
        $query = $this->connection->prepare('SELECT * FROM utilisateurs WHERE email = :email');
        $query->execute(['email' => $this->email]);
        $utilisateur = $query->fetch();

        if (!$utilisateur || !password_verify($password, $utilisateur['mot_de_passe'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email' => $utilisateur['email'],
            'role' => $utilisateur['role']
        ];
        return true;
    }

    public static function seDeconnecter()
    {
        $_SESSION = [];
        session_destroy();
    }

    public function modifierProfil($telephone, $adresse = null)
    {
        $query = $this->connection->prepare('UPDATE utilisateurs SET telephone = :telephone, adresse = :adresse WHERE id = :id');
        return $query->execute([
            'telephone' => $telephone,
            'adresse' => $adresse,
            'id' => $this->id
        ]);
    }
}