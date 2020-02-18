<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200218153256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media ADD provider VARCHAR(255) NOT NULL COMMENT \'(DC2Type:App\\\\Enum\\\\MediaProvider)\', CHANGE type type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:App\\\\Enum\\\\MediaType)\'');
        $this->addSql('CREATE INDEX media_provider_index ON media (provider)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX media_provider_index ON media');
        $this->addSql('ALTER TABLE media DROP provider, CHANGE type type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
