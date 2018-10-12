<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181001215140 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE episode_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE episode (id INT NOT NULL, number VARCHAR(255) DEFAULT NULL, guid VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, subtitle VARCHAR(255) DEFAULT NULL, media_url VARCHAR(255) DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, background_image_url VARCHAR(255) DEFAULT NULL, background_image_width INT DEFAULT NULL, background_image_height INT DEFAULT NULL, background_image_credit_by VARCHAR(255) DEFAULT NULL, background_image_credit_url VARCHAR(255) DEFAULT NULL, background_image_credit_description VARCHAR(255) DEFAULT NULL, content_html TEXT DEFAULT NULL, itunes_summary_html TEXT DEFAULT NULL, transcript_html TEXT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE episode_id_seq CASCADE');
        $this->addSql('DROP TABLE episode');
    }
}
