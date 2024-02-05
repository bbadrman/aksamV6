<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240202181944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prospect_product (prospect_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_10061479D182060A (prospect_id), INDEX IDX_100614794584665A (product_id), PRIMARY KEY(prospect_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prospect_product ADD CONSTRAINT FK_10061479D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prospect_product ADD CONSTRAINT FK_100614794584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prospect_product DROP FOREIGN KEY FK_10061479D182060A');
        $this->addSql('ALTER TABLE prospect_product DROP FOREIGN KEY FK_100614794584665A');
        $this->addSql('DROP TABLE prospect_product');
    }
}
