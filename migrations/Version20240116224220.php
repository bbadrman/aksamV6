<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116224220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD prospect_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455D182060A ON client (prospect_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455D182060A');
        $this->addSql('DROP INDEX UNIQ_C7440455D182060A ON client');
        $this->addSql('ALTER TABLE client DROP prospect_id');
    }
}
