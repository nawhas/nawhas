<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200701003746 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE audit_records (id UUID NOT NULL, user_id UUID NOT NULL, type VARCHAR(255) NOT NULL, entity VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, old JSON DEFAULT NULL, new JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8DBF8D36A76ED395 ON audit_records (user_id)');
        $this->addSql('COMMENT ON COLUMN audit_records.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_records.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_records.type IS \'(DC2Type:App\\Enum\\ChangeType)\'');
        $this->addSql('COMMENT ON COLUMN audit_records.old IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN audit_records.new IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE audit_records ADD CONSTRAINT FK_8DBF8D36A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE audit_records');
    }
}
