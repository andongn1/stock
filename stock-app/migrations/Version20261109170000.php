<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial migration to create all tables matching Laravel schema
 */
final class Version20261109170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create all tables for stock management application';
    }

    public function up(Schema $schema): void
    {
        // Users table
        $this->addSql('CREATE TABLE users ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'email VARCHAR(255) NOT NULL,'.
            'email_verified_at DATETIME DEFAULT NULL,'.
            'password VARCHAR(255) NOT NULL,'.
            'role VARCHAR(20) NOT NULL DEFAULT \'user\','.
            'remember_token VARCHAR(100) DEFAULT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'UNIQUE INDEX users_email_unique (email),'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Password reset tokens table
        $this->addSql('CREATE TABLE password_reset_tokens ('.
            'email VARCHAR(255) NOT NULL,'.
            'token VARCHAR(255) NOT NULL,'.
            'created_at DATETIME DEFAULT NULL,'.
            'PRIMARY KEY (email)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Sessions table
        $this->addSql('CREATE TABLE sessions ('.
            'id VARCHAR(255) NOT NULL,'.
            'user_id INT DEFAULT NULL,'.
            'ip_address VARCHAR(45) DEFAULT NULL,'.
            'user_agent TEXT DEFAULT NULL,'.
            'payload TEXT NOT NULL,'.
            'last_activity INT NOT NULL,'.
            'INDEX sessions_user_id_index (user_id),'.
            'INDEX sessions_last_activity_index (last_activity),'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Families table
        $this->addSql('CREATE TABLE families ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Sub families table
        $this->addSql('CREATE TABLE sub_families ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'family_id INT NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX sub_families_family_id_foreign (family_id),'.
            'CONSTRAINT sub_families_family_id_foreign FOREIGN KEY (family_id) REFERENCES families (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Units table
        $this->addSql('CREATE TABLE units ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Packagings table
        $this->addSql('CREATE TABLE packagings ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // VATs table
        $this->addSql('CREATE TABLE vats ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'rate DECIMAL(10, 2) NOT NULL,'.
            'label VARCHAR(255) NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Suppliers table
        $this->addSql('CREATE TABLE suppliers ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'contact_info TEXT DEFAULT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Articles table
        $this->addSql('CREATE TABLE articles ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'name VARCHAR(255) NOT NULL,'.
            'brand VARCHAR(255) DEFAULT NULL,'.
            'family_id INT NOT NULL,'.
            'sub_family_id INT NOT NULL,'.
            'unit_id INT NOT NULL,'.
            'packaging_id INT NOT NULL,'.
            'packaging_unit_quantity DECIMAL(10, 2) DEFAULT NULL,'.
            'vat_id INT NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX articles_family_id_foreign (family_id),'.
            'INDEX articles_sub_family_id_foreign (sub_family_id),'.
            'INDEX articles_unit_id_foreign (unit_id),'.
            'INDEX articles_packaging_id_foreign (packaging_id),'.
            'INDEX articles_vat_id_foreign (vat_id),'.
            'CONSTRAINT articles_family_id_foreign FOREIGN KEY (family_id) REFERENCES families (id) ON DELETE CASCADE,'.
            'CONSTRAINT articles_sub_family_id_foreign FOREIGN KEY (sub_family_id) REFERENCES sub_families (id) ON DELETE CASCADE,'.
            'CONSTRAINT articles_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units (id) ON DELETE CASCADE,'.
            'CONSTRAINT articles_packaging_id_foreign FOREIGN KEY (packaging_id) REFERENCES packagings (id) ON DELETE CASCADE,'.
            'CONSTRAINT articles_vat_id_foreign FOREIGN KEY (vat_id) REFERENCES vats (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Price histories table
        $this->addSql('CREATE TABLE price_histories ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'article_id INT NOT NULL,'.
            'supplier_id INT NOT NULL,'.
            'price DECIMAL(10, 2) NOT NULL,'.
            'currency VARCHAR(3) NOT NULL DEFAULT \'EUR\','.
            'price_type VARCHAR(20) NOT NULL DEFAULT \'per_unit\','.
            'valid_from DATE DEFAULT NULL,'.
            'valid_to DATE DEFAULT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX price_histories_article_id_foreign (article_id),'.
            'INDEX price_histories_supplier_id_foreign (supplier_id),'.
            'CONSTRAINT price_histories_article_id_foreign FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,'.
            'CONSTRAINT price_histories_supplier_id_foreign FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Selling prices table
        $this->addSql('CREATE TABLE selling_prices ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'article_id INT NOT NULL,'.
            'price_ttc DECIMAL(10, 2) NOT NULL,'.
            'sale_type VARCHAR(20) NOT NULL DEFAULT \'per_unit\','.
            'quantity DECIMAL(10, 4) NOT NULL DEFAULT 1.0000,'.
            'currency VARCHAR(3) NOT NULL DEFAULT \'EUR\','.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX selling_prices_article_id_foreign (article_id),'.
            'CONSTRAINT selling_prices_article_id_foreign FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Inventories table
        $this->addSql('CREATE TABLE inventories ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'article_id INT NOT NULL,'.
            'quantity DECIMAL(10, 4) NOT NULL,'.
            'date DATETIME NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX inventories_article_id_foreign (article_id),'.
            'CONSTRAINT inventories_article_id_foreign FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Entries table
        $this->addSql('CREATE TABLE entries ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'article_id INT NOT NULL,'.
            'quantity DECIMAL(10, 4) NOT NULL,'.
            'date DATETIME NOT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'PRIMARY KEY (id),'.
            'INDEX entries_article_id_date_index (article_id, date),'.
            'INDEX entries_article_id_foreign (article_id),'.
            'CONSTRAINT entries_article_id_foreign FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Personal access tokens table
        $this->addSql('CREATE TABLE personal_access_tokens ('.
            'id INT AUTO_INCREMENT NOT NULL,'.
            'tokenable_type VARCHAR(255) NOT NULL,'.
            'tokenable_id INT NOT NULL,'.
            'name TEXT NOT NULL,'.
            'token VARCHAR(64) NOT NULL,'.
            'abilities TEXT DEFAULT NULL,'.
            'last_used_at DATETIME DEFAULT NULL,'.
            'expires_at DATETIME DEFAULT NULL,'.
            'created_at DATETIME NOT NULL,'.
            'updated_at DATETIME NOT NULL,'.
            'UNIQUE INDEX personal_access_tokens_token_unique (token),'.
            'PRIMARY KEY (id)'.
            ') DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop all tables in reverse order
        $this->addSql('DROP TABLE personal_access_tokens');
        $this->addSql('DROP TABLE entries');
        $this->addSql('DROP TABLE inventories');
        $this->addSql('DROP TABLE selling_prices');
        $this->addSql('DROP TABLE price_histories');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE suppliers');
        $this->addSql('DROP TABLE vats');
        $this->addSql('DROP TABLE packagings');
        $this->addSql('DROP TABLE units');
        $this->addSql('DROP TABLE sub_families');
        $this->addSql('DROP TABLE families');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE password_reset_tokens');
        $this->addSql('DROP TABLE users');
    }
}
