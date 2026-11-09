<?php

namespace App\Data\Vat;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVatDto
{
    #[Assert\NotBlank(message: 'Le taux de TVA est obligatoire.')]
    #[Assert\Type('numeric', message: 'Le taux doit etre un nombre.')]
    #[Assert\Range(min: 0, max: 100, minMessage: 'Le taux ne peut pas etre negatif.', maxMessage: 'Le taux ne peut pas depasser 100.')]
    public string $rate;

    #[Assert\NotBlank(message: 'Le libelle de la TVA est obligatoire.')]
    #[Assert\Type('string', message: 'Le libelle doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le libelle ne peut pas depasser 255 caracteres.')]
    public string $label;
}
