<?php

namespace App\Data\PriceHistory;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePriceHistoryDto
{
    #[Assert\NotBlank(message: 'L\'article est obligatoire.')]
    #[Assert\Type('integer', message: 'L\'article doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de l\'article doit etre positif.')]
    public int $article_id;

    #[Assert\NotBlank(message: 'Le fournisseur est obligatoire.')]
    #[Assert\Type('integer', message: 'Le fournisseur doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant du fournisseur doit etre positif.')]
    public int $supplier_id;

    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\Type('numeric', message: 'Le prix doit etre un nombre.')]
    #[Assert\PositiveOrZero(message: 'Le prix ne peut pas etre negatif.')]
    public string $price;

    #[Assert\NotBlank(message: 'La devise est obligatoire.')]
    #[Assert\Type('string', message: 'La devise doit etre une chaine de caracteres.')]
    #[Assert\Length(exactly: 3, exactMessage: 'La devise doit comporter 3 caracteres.')]
    public string $currency = 'EUR';

    #[Assert\NotBlank(message: 'Le type de prix est obligatoire.')]
    #[Assert\Type('string', message: 'Le type de prix doit etre une chaine de caracteres.')]
    #[Assert\Choice(['per_unit', 'per_packaging'], message: 'Le type de prix doit etre soit "Par unite" soit "Par conditionnement".')]
    public string $price_type = 'per_unit';

    #[Assert\NotBlank(message: 'La date de debut est obligatoire.')]
    #[Assert\Type('string', message: 'La date doit etre une chaine de caracteres.')]
    #[Assert\Date(message: 'La date doit etre au format valide.')]
    public string $valid_from;

    #[Assert\Type('string', message: 'La date doit etre une chaine de caracteres.')]
    #[Assert\Date(message: 'La date doit etre au format valide.')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'valid_from', message: 'La date de fin doit etre posterieure ou egale a la date de debut.')]
    public ?string $valid_to = null;
}
