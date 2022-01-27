<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211029132635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_events DROP FOREIGN KEY FK_EA9344BCFF9E1919');
        $this->addSql('DROP INDEX UNIQ_EA9344BCFF9E1919 ON zsf_events');
        $this->addSql('ALTER TABLE zsf_events CHANGE picture_id_id picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zsf_events ADD CONSTRAINT FK_EA9344BCEE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf_image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA9344BCEE45BDBF ON zsf_events (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_events DROP FOREIGN KEY FK_EA9344BCEE45BDBF');
        $this->addSql('DROP INDEX UNIQ_EA9344BCEE45BDBF ON zsf_events');
        $this->addSql('ALTER TABLE zsf_events CHANGE picture_id picture_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zsf_events ADD CONSTRAINT FK_EA9344BCFF9E1919 FOREIGN KEY (picture_id_id) REFERENCES zsf_image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA9344BCFF9E1919 ON zsf_events (picture_id_id)');
    }
}
