<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902085332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, comrcl_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, raison_sociale VARCHAR(255) DEFAULT NULL, date_souscrpt DATETIME DEFAULT NULL, date_effet DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, activite VARCHAR(255) DEFAULT NULL, imatriclt VARCHAR(255) DEFAULT NULL, partenaire VARCHAR(255) DEFAULT NULL, compagnie VARCHAR(255) DEFAULT NULL, formule VARCHAR(255) DEFAULT NULL, date_prelvm DATETIME DEFAULT NULL, fraction VARCHAR(255) DEFAULT NULL, cotisation VARCHAR(255) DEFAULT NULL, acompte VARCHAR(255) DEFAULT NULL, frais NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_6034999310C5C333 (comrcl_id), UNIQUE INDEX UNIQ_60349993F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_6034999310C5C333 FOREIGN KEY (comrcl_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993F347EFB FOREIGN KEY (produit_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cloture DROP FOREIGN KEY FK_D5D0B568D182060A');
        $this->addSql('DROP TABLE cloture');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cloture (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_cloture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cloture_at DATETIME DEFAULT NULL, comment VARCHAR(515) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D5D0B568D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cloture ADD CONSTRAINT FK_D5D0B568D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_6034999310C5C333');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F347EFB');
        $this->addSql('DROP TABLE contrat');
    }
}
