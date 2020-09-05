<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200905154428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEBD50622C');
        $this->addSql('DROP INDEX IDX_E00CEDDEBD50622C ON booking');
        $this->addSql('ALTER TABLE booking CHANGE cleaner_id_id cleaner_id INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEEDDDAE19 FOREIGN KEY (cleaner_id) REFERENCES cleaner (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEEDDDAE19 ON booking (cleaner_id)');
        $this->addSql('ALTER TABLE cleaner DROP FOREIGN KEY FK_6E8447A438B53C32');
        $this->addSql('DROP INDEX IDX_6E8447A438B53C32 ON cleaner');
        $this->addSql('ALTER TABLE cleaner CHANGE company_id_id company_id INT NOT NULL');
        $this->addSql('ALTER TABLE cleaner ADD CONSTRAINT FK_6E8447A4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_6E8447A4979B1AD6 ON cleaner (company_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEEDDDAE19');
        $this->addSql('DROP INDEX IDX_E00CEDDEEDDDAE19 ON booking');
        $this->addSql('ALTER TABLE booking CHANGE cleaner_id cleaner_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEBD50622C FOREIGN KEY (cleaner_id_id) REFERENCES cleaner (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E00CEDDEBD50622C ON booking (cleaner_id_id)');
        $this->addSql('ALTER TABLE cleaner DROP FOREIGN KEY FK_6E8447A4979B1AD6');
        $this->addSql('DROP INDEX IDX_6E8447A4979B1AD6 ON cleaner');
        $this->addSql('ALTER TABLE cleaner CHANGE company_id company_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE cleaner ADD CONSTRAINT FK_6E8447A438B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6E8447A438B53C32 ON cleaner (company_id_id)');
    }
}
