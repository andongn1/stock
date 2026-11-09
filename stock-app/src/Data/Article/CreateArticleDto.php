<?php

namespace App\Data\Article;

use Symfony\Component\Validator\Constraints as Assert;

class CreateArticleDto
{
    #[Assert\NotBlank(message: 'Le nom de l\'article est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    public string $name;

    #[Assert\Type('string', message: 'La marque doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'La marque ne peut pas depasser 255 caracteres.')]
    public ?string $brand = null;

    #[Assert\NotBlank(message: 'La famille est obligatoire.')]
    #[Assert\Type('integer', message: 'La famille doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de la famille doit etre positif.')]
    public int $family_id;

    #[Assert\NotBlank(message: 'La sous-famille est obligatoire.')]
    #[Assert\Type('integer', message: 'La sous-famille doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de la sous-famille doit etre positif.')]
    public int $sub_family_id;

    #[Assert\NotBlank(message: 'L\'unite est obligatoire.')]
    #[Assert\Type('integer', message: 'L\'unite doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de l\'unite doit etre positif.')]
    public int $unit_id;

    #[Assert\NotBlank(message: 'Le conditionnement est obligatoire.')]
    #[Assert\Type('integer', message: 'Le conditionnement doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant du conditionnement doit etre positif.')]
    public int $packaging_id;

    #[Assert\Type('numeric', message: 'La quantite unitaire doit etre un nombre.')]
    #[Assert\PositiveOrZero(message: 'La quantite ne peut pas etre negative.')]
    public ?string $packaging_unit_quantity = null;

    #[Assert\NotBlank(message: 'La TVA est obligatoire.')]
    #[Assert\Type('integer', message: 'La TVA doit etre un identifiant valide.')]
    #[Assert\Positive(message: 'L\'identifiant de la TVA doit etre positif.')]
    public int $vat_id;
}
