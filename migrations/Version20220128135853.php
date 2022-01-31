<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220128135853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf2_image ADD gallery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zsf2_image ADD CONSTRAINT FK_1C70D6104E7AF8F FOREIGN KEY (gallery_id) REFERENCES zsf2_gallery (id)');
        $this->addSql('CREATE INDEX IDX_1C70D6104E7AF8F ON zsf2_image (gallery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE zsf2_image DROP FOREIGN KEY FK_1C70D6104E7AF8F');
        $this->addSql('DROP INDEX IDX_1C70D6104E7AF8F ON zsf2_image');
        $this->addSql('ALTER TABLE zsf2_image DROP gallery_id');
    }
}
