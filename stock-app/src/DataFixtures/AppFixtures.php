<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Entry;
use App\Entity\Family;
use App\Entity\Inventory;
use App\Entity\Packaging;
use App\Entity\PriceHistory;
use App\Entity\SellingPrice;
use App\Entity\SubFamily;
use App\Entity\Supplier;
use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Vat;
use App\Enum\PriceType;
use App\Enum\SaleType;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create Admin User
        $admin = new User();
        $admin->setName('Administrateur');
        $admin->setEmail('admin@mercuriale.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRole(UserRole::ADMIN);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $manager->persist($admin);

        // Create Regular User
        $user = new User();
        $user->setName('Utilisateur');
        $user->setEmail('user@mercuriale.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setRole(UserRole::USER);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $manager->persist($user);

        // Create Units
        $units = ['Piece', 'Kilogramme', 'Gramme', 'Litre', 'Millilitre', 'Metre', 'Centimetre'];
        $unitMap = [];
        foreach ($units as $unitName) {
            $unit = new Unit();
            $unit->setName($unitName);
            $unit->setCreatedAt(new \DateTime());
            $unit->setUpdatedAt(new \DateTime());
            $manager->persist($unit);
            $unitMap[$unitName] = $unit;
        }

        // Create Packagings
        $packagings = ['Sac', 'Boite', 'Carton', 'Bouteille', 'Paquet', 'Barquette', 'Palette'];
        $packagingMap = [];
        foreach ($packagings as $packagingName) {
            $packaging = new Packaging();
            $packaging->setName($packagingName);
            $packaging->setCreatedAt(new \DateTime());
            $packaging->setUpdatedAt(new \DateTime());
            $manager->persist($packaging);
            $packagingMap[$packagingName] = $packaging;
        }

        // Create VAT Rates
        $vats = [
            ['rate' => 0, 'label' => 'Exonere'],
            ['rate' => 5.5, 'label' => 'TVA 5,5%'],
            ['rate' => 10, 'label' => 'TVA 10%'],
            ['rate' => 20, 'label' => 'TVA 20%'],
        ];
        $vatMap = [];
        foreach ($vats as $vatData) {
            $vat = new Vat();
            $vat->setRate($vatData['rate']);
            $vat->setLabel($vatData['label']);
            $vat->setCreatedAt(new \DateTime());
            $vat->setUpdatedAt(new \DateTime());
            $manager->persist($vat);
            $vatMap[] = $vat;
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
        foreach ($families as $familyName) {
            $family = new Family();
            $family->setName($familyName);
            $family->setCreatedAt(new \DateTime());
            $family->setUpdatedAt(new \DateTime());
            $manager->persist($family);
            $familyMap[] = $family;
        }

        // Create Sub Families
        $subFamilles = [
            ['name' => 'Telephonie', 'family_index' => 0],
            ['name' => 'Informatique', 'family_index' => 0],
            ['name' => 'Electromenager', 'family_index' => 0],
            ['name' => 'Boissons', 'family_index' => 1],
            ['name' => 'Aliments secs', 'family_index' => 1],
            ['name' => 'Surgeles', 'family_index' => 1],
            ['name' => 'Salon', 'family_index' => 2],
            ['name' => 'Chambre', 'family_index' => 2],
            ['name' => 'Cuisine', 'family_index' => 2],
            ['name' => 'Homme', 'family_index' => 3],
            ['name' => 'Femme', 'family_index' => 3],
            ['name' => 'Enfant', 'family_index' => 3],
            ['name' => 'Outillage', 'family_index' => 4],
            ['name' => 'Quincaillerie', 'family_index' => 4],
            ['name' => 'Peinture', 'family_index' => 4],
        ];
        $subFamilyMap = [];
        foreach ($subFamilles as $subFamilyData) {
            $subFamily = new SubFamily();
            $subFamily->setName($subFamilyData['name']);
            $subFamily->setFamily($familyMap[$subFamilyData['family_index']]);
            $subFamily->setCreatedAt(new \DateTime());
            $subFamily->setUpdatedAt(new \DateTime());
            $manager->persist($subFamily);
            $subFamilyMap[] = $subFamily;
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
        foreach ($suppliers as $supplierData) {
            $supplier = new Supplier();
            $supplier->setName($supplierData['name']);
            $supplier->setContactInfo($supplierData['contact_info']);
            $supplier->setCreatedAt(new \DateTime());
            $supplier->setUpdatedAt(new \DateTime());
            $manager->persist($supplier);
            $supplierMap[] = $supplier;
        }

        // Flush to get IDs
        $manager->flush();

        // Reload articles to get their IDs
        $articles = [];
        $brands = ['Samsung', 'Apple', 'Sony', 'LG', 'Bosch', 'Nestle', 'Coca-Cola', 'IKEA', 'Nike', 'Adidas'];
        $unitIds = array_values($unitMap);
        $packagingIds = array_values($packagingMap);
        $vatIds = $vatMap;
        $familyIds = $familyMap;
        $subFamilyIds = $subFamilyMap;
        $supplierIds = $supplierMap;

        $now = new \DateTime();

        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setName('Article ' . ($i + 1) . ' - ' . $brands[array_rand($brands)]);
            $article->setBrand($brands[array_rand($brands)]);
            $article->setFamily($familyIds[array_rand($familyIds)]);
            $article->setSubFamily($subFamilyIds[array_rand($subFamilyIds)]);
            $article->setUnit($unitIds[array_rand($unitIds)]);
            $article->setPackaging($packagingIds[array_rand($packagingIds)]);
            $article->setPackagingUnitQuantity([1, 5, 10, 20, 25, 50, 100][array_rand([0, 1, 2, 3, 4, 5, 6])]);
            $article->setVat($vatIds[array_rand($vatIds)]);
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $manager->persist($article);
            $articles[] = $article;

            // Create price history for each article with random price type
            $numPrices = rand(1, 3);
            $priceTypes = [PriceType::PER_UNIT, PriceType::PER_PACKAGING];

            // Start with a date in the past
            $validFrom = (clone $now)->sub(new \DateInterval('P' . rand(30, 365) . 'D'));

            for ($j = 0; $j < $numPrices; $j++) {
                // Ensure valid_from is not in the future
                if ($validFrom > $now) {
                    $validFrom = clone $now;
                }

                // Set valid_to for all but the last price
                $validTo = null;
                if ($j < $numPrices - 1) {
                    $validTo = (clone $validFrom)->add(new \DateInterval('P' . rand(30, 180) . 'D'));
                    // Ensure valid_to is not in the future
                    if ($validTo > $now) {
                        $validTo = clone $now;
                    }
                }

                $priceHistory = new PriceHistory();
                $priceHistory->setArticle($article);
                $priceHistory->setSupplier($supplierIds[array_rand($supplierIds)]);
                $priceHistory->setPrice(number_format(rand(10, 1000) + rand(0, 99) / 100, 2, '.', ''));
                $priceHistory->setCurrency('EUR');
                $priceHistory->setPriceType($priceTypes[array_rand($priceTypes)]);
                $priceHistory->setValidFrom($validFrom);
                $priceHistory->setValidTo($validTo);
                $priceHistory->setCreatedAt(new \DateTime());
                $priceHistory->setUpdatedAt(new \DateTime());
                $manager->persist($priceHistory);

                // Move to next date period
                $validFrom = (clone $validFrom)->add(new \DateInterval('P' . rand(30, 180) . 'D'));
            }
        }

        // Create Selling Prices for half of the articles
        foreach (array_slice($articles, 0, 10) as $article) {
            $sellingPrice = new SellingPrice();
            $sellingPrice->setArticle($article);
            $sellingPrice->setPriceTtc(number_format(rand(15, 1500) + rand(0, 99) / 100, 2, '.', ''));
            $saleTypes = [SaleType::PER_UNIT, SaleType::PER_PACKAGING];
            $sellingPrice->setSaleType($saleTypes[array_rand($saleTypes)]);
            $sellingPrice->setQuantity(rand(1, 10));
            $sellingPrice->setCurrency('EUR');
            $sellingPrice->setCreatedAt(new \DateTime());
            $sellingPrice->setUpdatedAt(new \DateTime());
            $manager->persist($sellingPrice);
        }

        // Create some Entries
        foreach (array_slice($articles, 0, 5) as $article) {
            $entry = new Entry();
            $entry->setArticle($article);
            $entry->setQuantity(rand(1, 100));
            $entry->setDate((new \DateTime())->sub(new \DateInterval('P' . rand(1, 30) . 'D')));
            $entry->setCreatedAt(new \DateTime());
            $entry->setUpdatedAt(new \DateTime());
            $manager->persist($entry);
        }

        // Create some Inventories
        foreach (array_slice($articles, 5, 10) as $article) {
            $inventory = new Inventory();
            $inventory->setArticle($article);
            $inventory->setQuantity(rand(1, 200));
            $inventory->setDate((new \DateTime())->sub(new \DateInterval('P' . rand(1, 30) . 'D')));
            $inventory->setCreatedAt(new \DateTime());
            $inventory->setUpdatedAt(new \DateTime());
            $manager->persist($inventory);
        }

        $manager->flush();
    }
}
