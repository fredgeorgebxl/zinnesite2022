<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211004153527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_user ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zsf_user ADD CONSTRAINT FK_4D0B258FEE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf_image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D0B258FEE45BDBF ON zsf_user (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_user DROP FOREIGN KEY FK_4D0B258FEE45BDBF');
        $this->addSql('DROP INDEX UNIQ_4D0B258FEE45BDBF ON zsf_user');
        $this->addSql('ALTER TABLE zsf_user DROP picture_id');
    }
}
