<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907224545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_assignment (id INT AUTO_INCREMENT NOT NULL, cleaner_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_A95F27CBEDDDAE19 (cleaner_id), INDEX IDX_A95F27CB3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaner (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_6E8447A4979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking_assignment ADD CONSTRAINT FK_A95F27CBEDDDAE19 FOREIGN KEY (cleaner_id) REFERENCES cleaner (id)');
        $this->addSql('ALTER TABLE booking_assignment ADD CONSTRAINT FK_A95F27CB3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE cleaner ADD CONSTRAINT FK_6E8447A4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking_assignment DROP FOREIGN KEY FK_A95F27CB3301C60');
        $this->addSql('ALTER TABLE cleaner DROP FOREIGN KEY FK_6E8447A4979B1AD6');
        $this->addSql('ALTER TABLE booking_assignment DROP FOREIGN KEY FK_A95F27CBEDDDAE19');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE booking_assignment');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE cleaner');
    }
}
