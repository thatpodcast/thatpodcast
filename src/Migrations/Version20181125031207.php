<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181125031207 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode ADD itunes_card_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD twitter_card_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD facebook_card_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD hd_card_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode DROP itunes_card_url');
        $this->addSql('ALTER TABLE episode DROP twitter_card_url');
        $this->addSql('ALTER TABLE episode DROP facebook_card_url');
        $this->addSql('ALTER TABLE episode DROP hd_card_url');
    }
}
