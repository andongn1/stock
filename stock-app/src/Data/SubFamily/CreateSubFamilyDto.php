<?php

namespace App\Data\SubFamily;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSubFamilyDto
{
    #[Assert\NotBlank(message: 'Le nom de la sous-famille est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    public string $name;

    #[Assert\NotBlank(message: 'La famille est obligatoire.')]
    #[Assert\Type('integer', message: 'La famille doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de la famille doit etre positif.')]
    public int $family_id;
}
