<?php

namespace App\Data\SellingPrice;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSellingPriceDto
{
    #[Assert\NotBlank(message: 'Le prix TTC est obligatoire.')]
    #[Assert\Type('numeric', message: 'Le prix TTC doit etre un nombre.')]
    #[Assert\PositiveOrZero(message: 'Le prix TTC doit etre superieur ou egal a 0.')]
    public string $price_ttc;

    #[Assert\NotBlank(message: 'Le type de vente est obligatoire.')]
    #[Assert\Type('string', message: 'Le type de vente doit etre une chaine de caracteres.')]
    #[Assert\Choice(['per_unit', 'per_packaging'], message: 'Le type de vente doit etre soit "Par unite" soit "Par conditionnement".')]
    public string $sale_type = 'per_unit';

    #[Assert\Type('numeric', message: 'La quantite doit etre un nombre.')]
    #[Assert\PositiveOrZero(message: 'La quantite doit etre superieure ou egale a 0.')]
    public ?string $quantity = '1';

    #[Assert\NotBlank(message: 'La devise est obligatoire.')]
    #[Assert\Type('string', message: 'La devise doit etre une chaine de caracteres.')]
    #[Assert\Length(exactly: 3, exactMessage: 'La devise doit comporter 3 caracteres.')]
    public string $currency = 'EUR';
}
