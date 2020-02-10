<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200210031551 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reciters (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE albums (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', reciter_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', year SMALLINT UNSIGNED NOT NULL, artwork VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F4E2474FDEDF9489 (reciter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracks (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', album_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', reciter_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_246D2A2E1137ABCF (album_id), INDEX IDX_246D2A2EDEDF9489 (reciter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id)');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2E1137ABCF FOREIGN KEY (album_id) REFERENCES albums (id)');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FDEDF9489');
        $this->addSql('ALTER TABLE tracks DROP FOREIGN KEY FK_246D2A2EDEDF9489');
        $this->addSql('ALTER TABLE tracks DROP FOREIGN KEY FK_246D2A2E1137ABCF');
        $this->addSql('DROP TABLE reciters');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE tracks');
    }
}
