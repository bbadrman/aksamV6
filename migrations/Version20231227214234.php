<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227214234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD cmrl_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F8BA72B4 FOREIGN KEY (cmrl_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C7440455F8BA72B4 ON client (cmrl_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F8BA72B4');
        $this->addSql('DROP INDEX IDX_C7440455F8BA72B4 ON client');
        $this->addSql('ALTER TABLE client DROP cmrl_id');
    }
}
