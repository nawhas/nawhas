<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200211131945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums CHANGE year year VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE tracks ADD slug VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_album_track_slug ON tracks (album_id, slug)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums CHANGE year year SMALLINT UNSIGNED NOT NULL');
        $this->addSql('DROP INDEX unique_album_track_slug ON tracks');
        $this->addSql('ALTER TABLE tracks DROP slug');
    }
}
