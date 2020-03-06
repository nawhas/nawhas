<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200306013901 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE track_visits (id UUID NOT NULL, track_id UUID NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C8AF8035ED23C43 ON track_visits (track_id)');
        $this->addSql('COMMENT ON COLUMN track_visits.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN track_visits.track_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE reciter_visits (id UUID NOT NULL, reciter_id UUID NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9A0707D4DEDF9489 ON reciter_visits (reciter_id)');
        $this->addSql('COMMENT ON COLUMN reciter_visits.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reciter_visits.reciter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE track_visits ADD CONSTRAINT FK_C8AF8035ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reciter_visits ADD CONSTRAINT FK_9A0707D4DEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE track_visits');
        $this->addSql('DROP TABLE reciter_visits');
    }
}
