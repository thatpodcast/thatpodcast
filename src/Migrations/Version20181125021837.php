<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181125021837 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode ADD published TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD published_time_zone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode DROP published_date');
        $this->addSql('COMMENT ON COLUMN episode.published IS \'(DC2Type:datetime)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode ADD published_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE episode DROP published');
        $this->addSql('ALTER TABLE episode DROP published_time_zone');
    }
}
