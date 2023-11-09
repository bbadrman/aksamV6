<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027173641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BD57889920');
        $this->addSql('ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BDA76ED395');
        $this->addSql('ALTER TABLE user_product DROP FOREIGN KEY FK_8B471AA74584665A');
        $this->addSql('ALTER TABLE user_product DROP FOREIGN KEY FK_8B471AA7A76ED395');
        $this->addSql('DROP TABLE user_fonction');
        $this->addSql('DROP TABLE user_product');
        $this->addSql('ALTER TABLE client DROP firstname, DROP lastname, DROP phone, DROP email');
        $this->addSql('ALTER TABLE fonction DROP name, DROP description');
        $this->addSql('ALTER TABLE product DROP name, DROP descrption');
        $this->addSql('ALTER TABLE prospect DROP FOREIGN KEY FK_C9CE8C7D10C5C333');
        $this->addSql('ALTER TABLE prospect DROP FOREIGN KEY FK_C9CE8C7D14D45BBE');
        $this->addSql('ALTER TABLE prospect DROP FOREIGN KEY FK_C9CE8C7D296CD8AE');
        $this->addSql('DROP INDEX IDX_C9CE8C7D296CD8AE ON prospect');
        $this->addSql('DROP INDEX IDX_C9CE8C7D10C5C333 ON prospect');
        $this->addSql('DROP INDEX IDX_C9CE8C7D14D45BBE ON prospect');
        $this->addSql('ALTER TABLE prospect DROP autor_id, DROP comrcl_id, DROP team_id, DROP name, DROP lastname, DROP phone, DROP email, DROP gender, DROP city, DROP adress, DROP brith_at, DROP source, DROP type_prospect, DROP raison_sociale, DROP code_post, DROP gsm, DROP assure, DROP last_assure, DROP motif_resil, DROP motif_saise, DROP creat_at, DROP relaced_at');
        $this->addSql('ALTER TABLE Relance DROP FOREIGN KEY FK_9F06F8BA775D63D');
        $this->addSql('DROP INDEX IDX_9F06F8BA775D63D ON Relance');
        $this->addSql('ALTER TABLE Relance DROP prospects_id, DROP motif_relance, DROP relace_at, DROP coment');
        $this->addSql('ALTER TABLE team DROP name, DROP description');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D6365F12');
        $this->addSql('DROP INDEX IDX_8D93D649D6365F12 ON user');
        $this->addSql('ALTER TABLE user DROP teams_id, DROP roles, DROP password, DROP firstname, DROP lastname, DROP gender, DROP embuch_at, DROP remuneration, DROP status');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_fonction (user_id INT NOT NULL, fonction_id INT NOT NULL, INDEX IDX_E98D31BD57889920 (fonction_id), INDEX IDX_E98D31BDA76ED395 (user_id), PRIMARY KEY(user_id, fonction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_product (user_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8B471AA7A76ED395 (user_id), INDEX IDX_8B471AA74584665A (product_id), PRIMARY KEY(user_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_fonction ADD CONSTRAINT FK_E98D31BD57889920 FOREIGN KEY (fonction_id) REFERENCES fonction (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_fonction ADD CONSTRAINT FK_E98D31BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_product ADD CONSTRAINT FK_8B471AA74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_product ADD CONSTRAINT FK_8B471AA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD teams_id INT DEFAULT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) DEFAULT NULL, ADD lastname VARCHAR(255) DEFAULT NULL, ADD gender SMALLINT DEFAULT NULL, ADD embuch_at DATETIME DEFAULT NULL, ADD remuneration INT DEFAULT NULL, ADD status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D6365F12 FOREIGN KEY (teams_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649D6365F12 ON user (teams_id)');
        $this->addSql('ALTER TABLE product ADD name VARCHAR(255) NOT NULL, ADD descrption VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prospect ADD autor_id INT DEFAULT NULL, ADD comrcl_id INT DEFAULT NULL, ADD team_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD phone VARCHAR(255) NOT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD gender SMALLINT DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD adress LONGTEXT DEFAULT NULL, ADD brith_at DATETIME DEFAULT NULL, ADD source VARCHAR(50) DEFAULT NULL, ADD type_prospect VARCHAR(50) DEFAULT NULL, ADD raison_sociale VARCHAR(255) DEFAULT NULL, ADD code_post INT DEFAULT NULL, ADD gsm VARCHAR(255) DEFAULT NULL, ADD assure VARCHAR(20) DEFAULT NULL, ADD last_assure VARCHAR(20) DEFAULT NULL, ADD motif_resil VARCHAR(50) DEFAULT NULL, ADD motif_saise VARCHAR(255) DEFAULT NULL, ADD creat_at DATETIME NOT NULL, ADD relaced_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE prospect ADD CONSTRAINT FK_C9CE8C7D10C5C333 FOREIGN KEY (comrcl_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE prospect ADD CONSTRAINT FK_C9CE8C7D14D45BBE FOREIGN KEY (autor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE prospect ADD CONSTRAINT FK_C9CE8C7D296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C9CE8C7D296CD8AE ON prospect (team_id)');
        $this->addSql('CREATE INDEX IDX_C9CE8C7D10C5C333 ON prospect (comrcl_id)');
        $this->addSql('CREATE INDEX IDX_C9CE8C7D14D45BBE ON prospect (autor_id)');
        $this->addSql('ALTER TABLE fonction ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(20) NOT NULL, ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE relance ADD prospects_id INT DEFAULT NULL, ADD motif_relance VARCHAR(50) DEFAULT NULL, ADD relace_at DATETIME DEFAULT NULL, ADD coment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE relance ADD CONSTRAINT FK_9F06F8BA775D63D FOREIGN KEY (prospects_id) REFERENCES prospect (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9F06F8BA775D63D ON relance (prospects_id)');
    }
}
