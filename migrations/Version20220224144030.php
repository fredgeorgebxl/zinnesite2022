<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224144030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE zsf2_homeslide (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, link_name VARCHAR(255) DEFAULT NULL, link_url VARCHAR(255) DEFAULT NULL, selected TINYINT(1) NOT NULL, video VARCHAR(255) DEFAULT NULL, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F94EDDBB3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zsf2_homeslide ADD CONSTRAINT FK_F94EDDBB3DA5256D FOREIGN KEY (image_id) REFERENCES zsf2_image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE zsf2_homeslide');
    }
}
