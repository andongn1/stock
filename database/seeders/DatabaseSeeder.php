<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Family;
use App\Models\Packaging;
use App\Models\PriceHistory;
use App\Models\SubFamily;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@mercuriale.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Create Regular User
        User::create([
            'name' => 'Utilisateur',
            'email' => 'user@mercuriale.com',
            'password' => bcrypt('password'),
            'role' => UserRole::USER,
        ]);

        // Create Units
        $units = ['Piece', 'Kilogramme', 'Gramme', 'Litre', 'Millilitre', 'Metre', 'Centimetre'];
        $unitMap = [];
        foreach ($units as $unit) {
            $created = Unit::create(['name' => $unit]);
            $unitMap[$unit] = $created->id;
        }

        // Create Packagings (simple names only - unit and quantity are now on articles)
        $packagings = ['Sac', 'Boite', 'Carton', 'Bouteille', 'Paquet', 'Barquette', 'Palette'];
        foreach ($packagings as $packaging) {
            Packaging::create(['name' => $packaging]);
        }

        // Create VAT Rates
        $vats = [
            ['rate' => 0, 'label' => 'Exonere'],
            ['rate' => 5.5, 'label' => 'TVA 5,5%'],
            ['rate' => 10, 'label' => 'TVA 10%'],
            ['rate' => 20, 'label' => 'TVA 20%'],
        ];
        foreach ($vats as $vat) {
            Vat::create($vat);
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
        foreach ($families as $family) {
            Family::create(['name' => $family]);
        }

        // Create Sub Families
        $subFamilles = [
            ['name' => 'Telephonie', 'family_id' => 1],
            ['name' => 'Informatique', 'family_id' => 1],
            ['name' => 'Electromenager', 'family_id' => 1],
            ['name' => 'Boissons', 'family_id' => 2],
            ['name' => 'Aliments secs', 'family_id' => 2],
            ['name' => 'Surgeles', 'family_id' => 2],
            ['name' => 'Salon', 'family_id' => 3],
            ['name' => 'Chambre', 'family_id' => 3],
            ['name' => 'Cuisine', 'family_id' => 3],
            ['name' => 'Homme', 'family_id' => 4],
            ['name' => 'Femme', 'family_id' => 4],
            ['name' => 'Enfant', 'family_id' => 4],
            ['name' => 'Outillage', 'family_id' => 5],
            ['name' => 'Quincaillerie', 'family_id' => 5],
            ['name' => 'Peinture', 'family_id' => 5],
        ];
        foreach ($subFamilles as $subFamily) {
            SubFamily::create($subFamily);
        }

        // Create Suppliers
        $suppliers = [
            ['name' => 'Fournisseur Alpha', 'contact_info' => 'contact@alpha.com | 01 23 45 67 89'],
            ['name' => 'Fournisseur Beta', 'contact_info' => 'contact@beta.com | 02 34 56 78 90'],
            ['name' => 'Fournisseur Gamma', 'contact_info' => 'contact@gamma.com | 03 45 67 89 01'],
            ['name' => 'Fournisseur Delta', 'contact_info' => 'contact@delta.com | 04 56 78 90 12'],
            ['name' => 'Distributeur Central', 'contact_info' => 'sales@central.com | 05 67 89 01 23'],
        ];
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create Articles
        $brands = ['Samsung', 'Apple', 'Sony', 'LG', 'Bosch', 'Nestle', 'Coca-Cola', 'IKEA', 'Nike', 'Adidas'];
        $unitIds = Unit::pluck('id')->toArray();
        $packagingIds = Packaging::pluck('id')->toArray();
        $vatIds = Vat::pluck('id')->toArray();
        $familyIds = Family::pluck('id')->toArray();
        $subFamilyIds = SubFamily::pluck('id')->toArray();
        $supplierIds = Supplier::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            $article = Article::create([
                'name' => 'Article ' . ($i + 1) . ' - ' . $brands[array_rand($brands)],
                'brand' => $brands[array_rand($brands)],
                'family_id' => $familyIds[array_rand($familyIds)],
                'sub_family_id' => $subFamilyIds[array_rand($subFamilyIds)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'packaging_id' => $packagingIds[array_rand($packagingIds)],
                'packaging_unit_quantity' => [1, 5, 10, 20, 25, 50, 100][array_rand([0, 1, 2, 3, 4, 5, 6])],
                'vat_id' => $vatIds[array_rand($vatIds)],
            ]);

            // Create price history for each article with random price type
            $numPrices = rand(1, 3);
            $validFrom = now()->subDays(rand(30, 365));
            $priceTypes = ['per_unit', 'per_packaging'];
            
            for ($j = 0; $j < $numPrices; $j++) {
                PriceHistory::create([
                    'article_id' => $article->id,
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'price' => number_format(rand(10, 1000) + rand(0, 99) / 100, 2, '.', ''),
                    'currency' => 'EUR',
                    'price_type' => $priceTypes[array_rand($priceTypes)],
                    'valid_from' => $validFrom->format('Y-m-d'),
                    'valid_to' => $j < $numPrices - 1 ? $validFrom->copy()->addDays(rand(30, 180))->format('Y-m-d') : null,
                ]);
                
                $validFrom = $validFrom->copy()->addDays(rand(30, 180));
            }
        }
    }
}
