<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200218011658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE media (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX media_type_index (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE track_media (track_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', media_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_1ADB03AB5ED23C43 (track_id), INDEX IDX_1ADB03ABEA9FDD75 (media_id), PRIMARY KEY(track_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE track_media ADD CONSTRAINT FK_1ADB03AB5ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track_media ADD CONSTRAINT FK_1ADB03ABEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track_media DROP FOREIGN KEY FK_1ADB03ABEA9FDD75');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE track_media');
    }
}
