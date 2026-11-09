<?php

namespace App\Data\User;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    public string $name;

    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'L\'email doit etre valide.')]
    #[Assert\Unique(entityClass: 'App\Entity\User', fields: ['email'], message: 'Un utilisateur avec cet email existe deja.')]
    public string $email;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Type('string', message: 'Le mot de passe doit etre une chaine de caracteres.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins 8 caracteres.')]
    public string $password;

    #[Assert\NotBlank(message: 'Le role est obligatoire.')]
    #[Assert\Choice(callback: 'getRoles', message: 'Role invalide.')]
    public string $role;

    public static function getRoles(): array
    {
        return [UserRole::Admin->value, UserRole::User->value];
    }
}
