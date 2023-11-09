<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023221600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Relance (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_relance VARCHAR(50) DEFAULT NULL, relace_at DATETIME DEFAULT NULL, coment VARCHAR(255) DEFAULT NULL, INDEX IDX_9F06F8BAD182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Relance ADD CONSTRAINT FK_9F06F8BAD182060A FOREIGN KEY (prospect_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Relance DROP FOREIGN KEY FK_9F06F8BAD182060A');
        $this->addSql('DROP TABLE Relance');
    }
}
