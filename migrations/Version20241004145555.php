<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004145555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sav (id INT AUTO_INCREMENT NOT NULL, contrat_id INT DEFAULT NULL, creat_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', nature_demande VARCHAR(255) DEFAULT NULL, INDEX IDX_6C7681F41823061F (contrat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sav_user (sav_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A1D3B1D84F726353 (sav_id), INDEX IDX_A1D3B1D8A76ED395 (user_id), PRIMARY KEY(sav_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F41823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE sav_user ADD CONSTRAINT FK_A1D3B1D84F726353 FOREIGN KEY (sav_id) REFERENCES sav (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sav_user ADD CONSTRAINT FK_A1D3B1D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cloture DROP FOREIGN KEY FK_D5D0B568D182060A');
        $this->addSql('ALTER TABLE contrat_product DROP FOREIGN KEY FK_7A8457DB4584665A');
        $this->addSql('ALTER TABLE contrat_product DROP FOREIGN KEY FK_7A8457DB1823061F');
        $this->addSql('DROP TABLE cloture');
        $this->addSql('DROP TABLE contrat_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cloture (id INT AUTO_INCREMENT NOT NULL, prospect_id INT DEFAULT NULL, motif_cloture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cloture_at DATETIME DEFAULT NULL, comment VARCHAR(515) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D5D0B568D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE contrat_product (contrat_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_7A8457DB1823061F (contrat_id), INDEX IDX_7A8457DB4584665A (product_id), PRIMARY KEY(contrat_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cloture ADD CONSTRAINT FK_D5D0B568D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE contrat_product ADD CONSTRAINT FK_7A8457DB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contrat_product ADD CONSTRAINT FK_7A8457DB1823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F41823061F');
        $this->addSql('ALTER TABLE sav_user DROP FOREIGN KEY FK_A1D3B1D84F726353');
        $this->addSql('ALTER TABLE sav_user DROP FOREIGN KEY FK_A1D3B1D8A76ED395');
        $this->addSql('DROP TABLE sav');
        $this->addSql('DROP TABLE sav_user');
    }
}
