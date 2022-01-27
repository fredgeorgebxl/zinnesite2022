<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125111344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_events DROP FOREIGN KEY FK_EA9344BCEE45BDBF');
        $this->addSql('ALTER TABLE zsf_user DROP FOREIGN KEY FK_4D0B258FEE45BDBF');
        $this->addSql('CREATE TABLE zsf2_events (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date DATETIME NOT NULL, season VARCHAR(255) NOT NULL, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, gallery INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, location LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_B53D4699EE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zsf2_image (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, path VARCHAR(255) NOT NULL, weight INT DEFAULT NULL, created DATETIME DEFAULT NULL, crop_coordinations VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zsf2_repertoire (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, active TINYINT(1) DEFAULT NULL, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zsf2_user (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, voice VARCHAR(4) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_161B45A0E7927C74 (email), UNIQUE INDEX UNIQ_161B45A0EE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zsf2_events ADD CONSTRAINT FK_B53D4699EE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf2_image (id)');
        $this->addSql('ALTER TABLE zsf2_user ADD CONSTRAINT FK_161B45A0EE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf2_image (id)');
        $this->addSql('DROP TABLE zsf_events');
        $this->addSql('DROP TABLE zsf_image');
        $this->addSql('DROP TABLE zsf_repertoire');
        $this->addSql('DROP TABLE zsf_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf2_events DROP FOREIGN KEY FK_B53D4699EE45BDBF');
        $this->addSql('ALTER TABLE zsf2_user DROP FOREIGN KEY FK_161B45A0EE45BDBF');
        $this->addSql('CREATE TABLE zsf_events (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date DATETIME NOT NULL, season VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) NOT NULL, gallery INT DEFAULT NULL, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, location LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_EA9344BCEE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE zsf_image (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, alt VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, width INT DEFAULT NULL, height INT DEFAULT NULL, path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, weight INT DEFAULT NULL, created DATETIME DEFAULT NULL, crop_coordinations VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE zsf_repertoire (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) DEFAULT NULL, datecreated DATETIME NOT NULL, datemodified DATETIME DEFAULT NULL, published TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE zsf_user (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lastname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, voice VARCHAR(4) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) DEFAULT NULL, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4D0B258FE7927C74 (email), UNIQUE INDEX UNIQ_4D0B258FEE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE zsf_events ADD CONSTRAINT FK_EA9344BCEE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf_image (id)');
        $this->addSql('ALTER TABLE zsf_user ADD CONSTRAINT FK_4D0B258FEE45BDBF FOREIGN KEY (picture_id) REFERENCES zsf_image (id)');
        $this->addSql('DROP TABLE zsf2_events');
        $this->addSql('DROP TABLE zsf2_image');
        $this->addSql('DROP TABLE zsf2_repertoire');
        $this->addSql('DROP TABLE zsf2_user');
    }
}
