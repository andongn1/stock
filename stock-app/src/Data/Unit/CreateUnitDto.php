<?php

namespace App\Data\Unit;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUnitDto
{
    #[Assert\NotBlank(message: 'Le nom de l\'unite est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    #[Assert\Unique(entityClass: 'App\Entity\Unit', fields: ['name'], message: 'Cette unite existe deja.')]
    public string $name;
}
