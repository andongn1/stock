<?php

namespace App\Data\Supplier;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSupplierDto
{
    #[Assert\NotBlank(message: 'Le nom du fournisseur est obligatoire.')]
    #[Assert\Type('string', message: 'Le nom doit etre une chaine de caracteres.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.')]
    public string $name;

    #[Assert\Type('string', message: 'Les informations de contact doivent etre une chaine de caracteres.')]
    public ?string $contact_info = null;
}
