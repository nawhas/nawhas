<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200217171534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracks ADD reciter_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id)');
        $this->addSql('CREATE INDEX IDX_246D2A2EDEDF9489 ON tracks (reciter_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracks DROP FOREIGN KEY FK_246D2A2EDEDF9489');
        $this->addSql('DROP INDEX IDX_246D2A2EDEDF9489 ON tracks');
        $this->addSql('ALTER TABLE tracks DROP reciter_id');
    }
}
