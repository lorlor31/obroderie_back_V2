<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402081435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract ADD user_id INT DEFAULT NULL, ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28599395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_E98F2859A76ED395 ON contract (user_id)');
        $this->addSql('CREATE INDEX IDX_E98F28599395C3F3 ON contract (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859A76ED395');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F28599395C3F3');
        $this->addSql('DROP INDEX IDX_E98F2859A76ED395 ON contract');
        $this->addSql('DROP INDEX IDX_E98F28599395C3F3 ON contract');
        $this->addSql('ALTER TABLE contract DROP user_id, DROP customer_id');
    }
}
