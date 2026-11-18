<?php

namespace App\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $database) {}

    public function findByEmail(string $email): array|false
    {
        $statement = $this->database->prepare('SELECT * FROM utilisateurs WHERE email = :email');
        $statement->execute(['email' => $email]);
        return $statement->fetch();
    }

    public function create(array $data): bool
    {
        $statement = $this->database->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, role, adresse, nom_concession)
             VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role, :adresse, :nom_concession)'
        );
        return $statement->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['password'], PASSWORD_DEFAULT),
            'telephone' => $data['telephone'],
            'role' => $data['role'],
            'adresse' => $data['role'] === 'client' ? ($data['extra'] ?: null) : null,
            'nom_concession' => $data['role'] === 'vendeur' ? $data['extra'] : null,
        ]);
    }
}
