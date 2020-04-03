<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200403204321 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lyrics ADD format SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE lyrics DROP version');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lyrics ADD version SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE lyrics DROP format');
    }
}
