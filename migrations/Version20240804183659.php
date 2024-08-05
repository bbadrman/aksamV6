<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240804183659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE relance_history (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_relanced VARCHAR(255) DEFAULT NULL, relaced_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', comment VARCHAR(515) DEFAULT NULL, INDEX IDX_4E9F42A5D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE relance_history ADD CONSTRAINT FK_4E9F42A5D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)');
        $this->addSql('ALTER TABLE cloture CHANGE comment comment VARCHAR(515) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relance_history DROP FOREIGN KEY FK_4E9F42A5D182060A');
        $this->addSql('DROP TABLE relance_history');
        $this->addSql('ALTER TABLE cloture CHANGE comment comment VARCHAR(255) DEFAULT NULL');
    }
}
