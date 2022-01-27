<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028145806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE zsf_event (id INT AUTO_INCREMENT NOT NULL, picture_id_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date DATETIME NOT NULL, season VARCHAR(255) NOT NULL, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, gallery INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, location LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_4969F5D1FF9E1919 (picture_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zsf_event ADD CONSTRAINT FK_4969F5D1FF9E1919 FOREIGN KEY (picture_id_id) REFERENCES zsf_image (id)');
        $this->addSql('DROP TABLE event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, picture_id_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date DATETIME NOT NULL, season VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, gallery INT DEFAULT NULL, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, location LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_3BAE0AA7FF9E1919 (picture_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7FF9E1919 FOREIGN KEY (picture_id_id) REFERENCES zsf_image (id)');
        $this->addSql('DROP TABLE zsf_event');
    }
}
