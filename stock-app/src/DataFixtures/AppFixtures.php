<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Family;
use App\Entity\Packaging;
use App\Entity\PriceHistory;
use App\Entity\SubFamily;
use App\Entity\Supplier;
use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Vat;
use App\Enum\PriceType;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Admin User
        $admin = new User();
        $admin->setName('Administrateur');
        $admin->setEmail('admin@mercuriale.com');
        $admin->setPassword($this->passwordHasher->hash('password'));
        $admin->setRole(UserRole::ADMIN);
        $admin->setEmailVerifiedAt(new \DateTimeImmutable());
        $manager->persist($admin);
        $this->addReference('admin_user', $admin);

        // Create Regular User
        $user = new User();
        $user->setName('Utilisateur');
        $user->setEmail('user@mercuriale.com');
        $user->setPassword($this->passwordHasher->hash('password'));
        $user->setRole(UserRole::USER);
        $user->setEmailVerifiedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $this->addReference('regular_user', $user);

        // Create Units
        $units = ['Piece', 'Kilogramme', 'Gramme', 'Litre', 'Millilitre', 'Metre', 'Centimetre'];
        $unitMap = [];
        foreach ($units as $unit) {
            $unitEntity = new Unit();
            $unitEntity->setName($unit);
            $manager->persist($unitEntity);
            $unitMap[$unit] = $unitEntity;
            $this->addReference('unit_' . $unit, $unitEntity);
        }

        // Create Packagings
        $packagings = ['Sac', 'Boite', 'Carton', 'Bouteille', 'Paquet', 'Barquette', 'Palette'];
        $packagingMap = [];
        foreach ($packagings as $packaging) {
            $packagingEntity = new Packaging();
            $packagingEntity->setName($packaging);
            $manager->persist($packagingEntity);
            $packagingMap[$packaging] = $packagingEntity;
            $this->addReference('packaging_' . $packaging, $packagingEntity);
        }

        // Create VAT Rates
        $vats = [
            ['rate' => '0.00', 'label' => 'Exonere'],
            ['rate' => '5.50', 'label' => 'TVA 5,5%'],
            ['rate' => '10.00', 'label' => 'TVA 10%'],
            ['rate' => '20.00', 'label' => 'TVA 20%'],
        ];
        $vatMap = [];
        foreach ($vats as $vatData) {
            $vat = new Vat();
            $vat->setRate($vatData['rate']);
            $vat->setLabel($vatData['label']);
            $manager->persist($vat);
            $vatMap[$vatData['label']] = $vat;
            $this->addReference('vat_' . $vatData['label'], $vat);
        }

        // Create Families
        $families = [
            'Electronique',
            'Alimentation',
            'Meuble',
            'Textile',
            'Bricolage',
            'Jardinage',
            'Automobile',
            'Sante',
        ];
        $familyMap = [];
        foreach ($families as $family) {
            $familyEntity = new Family();
            $familyEntity->setName($family);
            $manager->persist($familyEntity);
            $familyMap[$family] = $familyEntity;
            $this->addReference('family_' . $family, $familyEntity);
        }

        // Create Sub Families
        $subFamilles = [
            ['name' => 'Telephonie', 'family' => 'Electronique'],
            ['name' => 'Informatique', 'family' => 'Electronique'],
            ['name' => 'Electromenager', 'family' => 'Electronique'],
            ['name' => 'Boissons', 'family' => 'Alimentation'],
            ['name' => 'Aliments secs', 'family' => 'Alimentation'],
            ['name' => 'Surgeles', 'family' => 'Alimentation'],
            ['name' => 'Salon', 'family' => 'Meuble'],
            ['name' => 'Chambre', 'family' => 'Meuble'],
            ['name' => 'Cuisine', 'family' => 'Meuble'],
            ['name' => 'Homme', 'family' => 'Textile'],
            ['name' => 'Femme', 'family' => 'Textile'],
            ['name' => 'Enfant', 'family' => 'Textile'],
            ['name' => 'Outillage', 'family' => 'Bricolage'],
            ['name' => 'Quincaillerie', 'family' => 'Bricolage'],
            ['name' => 'Peinture', 'family' => 'Bricolage'],
        ];
        $subFamilyMap = [];
        foreach ($subFamilles as $subFamily) {
            $subFamilyEntity = new SubFamily();
            $subFamilyEntity->setName($subFamily['name']);
            $subFamilyEntity->setFamily($familyMap[$subFamily['family']]);
            $manager->persist($subFamilyEntity);
            $subFamilyMap[$subFamily['name']] = $subFamilyEntity;
            $this->addReference('sub_family_' . $subFamily['name'], $subFamilyEntity);
        }

        // Create Suppliers
        $suppliers = [
            ['name' => 'Fournisseur Alpha', 'contact_info' => 'contact@alpha.com | 01 23 45 67 89'],
            ['name' => 'Fournisseur Beta', 'contact_info' => 'contact@beta.com | 02 34 56 78 90'],
            ['name' => 'Fournisseur Gamma', 'contact_info' => 'contact@gamma.com | 03 45 67 89 01'],
            ['name' => 'Fournisseur Delta', 'contact_info' => 'contact@delta.com | 04 56 78 90 12'],
            ['name' => 'Distributeur Central', 'contact_info' => 'sales@central.com | 05 67 89 01 23'],
        ];
        $supplierMap = [];
        foreach ($suppliers as $supplier) {
            $supplierEntity = new Supplier();
            $supplierEntity->setName($supplier['name']);
            $supplierEntity->setContactInfo($supplier['contact_info']);
            $manager->persist($supplierEntity);
            $supplierMap[$supplier['name']] = $supplierEntity;
            $this->addReference('supplier_' . $supplier['name'], $supplierEntity);
        }

        // Create Articles
        $brands = ['Samsung', 'Apple', 'Sony', 'LG', 'Bosch', 'Nestle', 'Coca-Cola', 'IKEA', 'Nike', 'Adidas'];
        $unitIds = array_keys($unitMap);
        $packagingIds = array_keys($packagingMap);
        $vatIds = array_keys($vatMap);
        $familyIds = array_keys($familyMap);
        $subFamilyIds = array_keys($subFamilyMap);
        $supplierIds = array_keys($supplierMap);

        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setName('Article ' . ($i + 1) . ' - ' . $brands[array_rand($brands)]);
            $article->setBrand($brands[array_rand($brands)]);
            $article->setFamily($familyMap[$familyIds[array_rand($familyIds)]]);
            $article->setSubFamily($subFamilyMap[$subFamilyIds[array_rand($subFamilyIds)]]);
            $article->setUnit($unitMap[$unitIds[array_rand($unitIds)]]);
            $article->setPackaging($packagingMap[$packagingIds[array_rand($packagingIds)]]);
            $article->setPackagingUnitQuantity((string)[1, 5, 10, 20, 25, 50, 100][array_rand([0, 1, 2, 3, 4, 5, 6])]);
            $article->setVat($vatMap[$vatIds[array_rand($vatIds)]]);
            $manager->persist($article);
            $this->addReference('article_' . $i, $article);

            // Create price history for each article with random price type
            $numPrices = rand(1, 3);
            $priceTypes = ['per_unit', 'per_packaging'];
            
            // Start with a date in the past
            $validFrom = new \DateTime('-' . rand(30, 365) . ' days');
            
            for ($j = 0; $j < $numPrices; $j++) {
                // Ensure valid_from is not in the future
                if ($validFrom > new \DateTime()) {
                    $validFrom = new \DateTime();
                }
                
                // Set valid_to for all but the last price
                $validTo = $j < $numPrices - 1 ? clone $validFrom->add(new \DateInterval('P' . rand(30, 180) . 'D')) : null;
                
                // Ensure valid_to is not in the future
                if ($validTo && $validTo > new \DateTime()) {
                    $validTo = null;
                }
                
                $priceHistory = new PriceHistory();
                $priceHistory->setArticle($article);
                $priceHistory->setSupplier($supplierMap[$supplierIds[array_rand($supplierIds)]]);
                $priceHistory->setPrice(number_format(rand(10, 1000) + rand(0, 99) / 100, 2, '.', ''));
                $priceHistory->setCurrency('EUR');
                $priceHistory->setPriceType($priceTypes[array_rand($priceTypes)]);
                $priceHistory->setValidFrom($validFrom);
                $priceHistory->setValidTo($validTo);
                $manager->persist($priceHistory);
                
                // Move to next date period
                $validFrom = clone $validFrom->add(new \DateInterval('P' . rand(30, 180) . 'D'));
            }
        }

        $manager->flush();
    }
}
