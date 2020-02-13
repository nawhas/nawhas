<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200213031919 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lyrics (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', track_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3BDA6C665ED23C43 (track_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lyrics ADD CONSTRAINT FK_3BDA6C665ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id)');
        $this->addSql('ALTER TABLE tracks DROP FOREIGN KEY FK_246D2A2EDEDF9489');
        $this->addSql('DROP INDEX IDX_246D2A2EDEDF9489 ON tracks');
        $this->addSql('ALTER TABLE tracks ADD lyric_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP reciter_id');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EF9CD025C FOREIGN KEY (lyric_id) REFERENCES lyrics (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_246D2A2EF9CD025C ON tracks (lyric_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracks DROP FOREIGN KEY FK_246D2A2EF9CD025C');
        $this->addSql('DROP TABLE lyrics');
        $this->addSql('DROP INDEX UNIQ_246D2A2EF9CD025C ON tracks');
        $this->addSql('ALTER TABLE tracks ADD reciter_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', DROP lyric_id');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id)');
        $this->addSql('CREATE INDEX IDX_246D2A2EDEDF9489 ON tracks (reciter_id)');
    }
}
