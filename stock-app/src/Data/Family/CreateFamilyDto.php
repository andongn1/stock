<?php

namespace App\Data\Family;

use Symfony\Component\Validator\Constraints as Assert;

class CreateFamilyDto
{
    #[Assert\NotBlank(message: 'Le nom de la famille est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    #[Assert\Unique(entityClass: 'App\Entity\Family', fields: ['name'], message: 'Une famille avec ce nom existe deja.')]
    public string $name;
}
