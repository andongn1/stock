<?php

namespace App\Data\Packaging;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePackagingDto
{
    #[Assert\NotBlank(message: 'Le nom du conditionnement est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    public string $name;
}
