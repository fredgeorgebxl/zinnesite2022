<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211004134834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_image CHANGE created created DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D0B258FE7927C74 ON zsf_user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf_image CHANGE created created DATETIME NOT NULL');
        $this->addSql('DROP INDEX UNIQ_4D0B258FE7927C74 ON zsf_user');
    }
}
