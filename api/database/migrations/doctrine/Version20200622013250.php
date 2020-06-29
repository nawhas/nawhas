<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200622013250 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE social_accounts (id UUID NOT NULL, user_id UUID NOT NULL, provider VARCHAR(255) NOT NULL, provider_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_44F90A80A76ED395 ON social_accounts (user_id)');
        $this->addSql('CREATE UNIQUE INDEX social_accounts_user_id_provider_unique ON social_accounts (user_id, provider)');
        $this->addSql('COMMENT ON COLUMN social_accounts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN social_accounts.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE social_accounts ADD CONSTRAINT FK_44F90A80A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE social_accounts');
    }
}
