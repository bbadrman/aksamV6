<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027221524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE relanced (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_relanced VARCHAR(255) DEFAULT NULL, relaced_at DATETIME DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_4A8074F0D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE relanced ADD CONSTRAINT FK_4A8074F0D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)');
        $this->addSql('ALTER TABLE Relance DROP FOREIGN KEY FK_50BBC126775D63D');
        $this->addSql('DROP TABLE Relance');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Relance (id INT AUTO_INCREMENT NOT NULL, prospects_id INT DEFAULT NULL, motif_relance VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, relace_at DATETIME DEFAULT NULL, coment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_50BBC126775D63D (prospects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE Relance ADD CONSTRAINT FK_50BBC126775D63D FOREIGN KEY (prospects_id) REFERENCES prospect (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE relanced DROP FOREIGN KEY FK_4A8074F0D182060A');
        $this->addSql('DROP TABLE relanced');
    }
}
