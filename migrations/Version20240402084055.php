<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402084055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, contract_id INT NOT NULL, textile_id INT DEFAULT NULL, embroidery_id INT NOT NULL, name VARCHAR(100) NOT NULL, quantity INT NOT NULL, price NUMERIC(6, 2) NOT NULL, delivery_at DATE DEFAULT NULL, manufacturing_delay INT NOT NULL, product_order INT NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_D34A04AD2576E0FD (contract_id), INDEX IDX_D34A04AD8B8F28AF (textile_id), INDEX IDX_D34A04AD37CA7B14 (embroidery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8B8F28AF FOREIGN KEY (textile_id) REFERENCES textile (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD37CA7B14 FOREIGN KEY (embroidery_id) REFERENCES embroidery (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD2576E0FD');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8B8F28AF');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD37CA7B14');
        $this->addSql('DROP TABLE product');
    }
}
