<?php

namespace App\Services;

use App\Core\Session;
use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(private UserRepository $users) {}

    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            return false;
        }

        Session::regenerate();
        Session::put('utilisateur', [
            'id' => (int) $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);
        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function register(array $data): bool
    {
        return $this->users->create($data);
    }
}
