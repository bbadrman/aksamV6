<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240826135209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cloture DROP FOREIGN KEY FK_D5D0B568D182060A');
        $this->addSql('DROP TABLE cloture');
        $this->addSql('ALTER TABLE transaction ADD debit NUMERIC(10, 3) DEFAULT NULL, ADD credit NUMERIC(10, 3) DEFAULT NULL, ADD moyen VARCHAR(255) DEFAULT NULL, CHANGE montant motif VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cloture (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_cloture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cloture_at DATETIME DEFAULT NULL, comment VARCHAR(515) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D5D0B568D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cloture ADD CONSTRAINT FK_D5D0B568D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE transaction ADD montant VARCHAR(255) DEFAULT NULL, DROP motif, DROP debit, DROP credit, DROP moyen');
    }
}
