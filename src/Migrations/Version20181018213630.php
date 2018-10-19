<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181018213630 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode ADD pristine_media_updated DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD pristine_media_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD pristine_media_original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD pristine_media_mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD pristine_media_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD pristine_media_dimensions TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN episode.pristine_media_dimensions IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode DROP pristine_media_updated');
        $this->addSql('ALTER TABLE episode DROP pristine_media_name');
        $this->addSql('ALTER TABLE episode DROP pristine_media_original_name');
        $this->addSql('ALTER TABLE episode DROP pristine_media_mime_type');
        $this->addSql('ALTER TABLE episode DROP pristine_media_size');
        $this->addSql('ALTER TABLE episode DROP pristine_media_dimensions');
    }
}
