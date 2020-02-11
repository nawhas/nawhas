<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200211130444 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reciters ADD slug VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_36DF47FC989D9B62 ON reciters (slug)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_36DF47FC989D9B62 ON reciters');
        $this->addSql('ALTER TABLE reciters DROP slug');
    }
}
