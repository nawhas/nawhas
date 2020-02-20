<?php

namespace Database\Migrations\Doctrine;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20200220022423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE lyrics (id UUID NOT NULL, track_id UUID NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BDA6C665ED23C43 ON lyrics (track_id)');
        $this->addSql('COMMENT ON COLUMN lyrics.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lyrics.track_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE media (id UUID NOT NULL, type VARCHAR(255) NOT NULL, provider VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX media_type_index ON media (type)');
        $this->addSql('CREATE INDEX media_provider_index ON media (provider)');
        $this->addSql('COMMENT ON COLUMN media.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN media.type IS \'(DC2Type:App\\Enum\\MediaType)\'');
        $this->addSql('COMMENT ON COLUMN media.provider IS \'(DC2Type:App\\Enum\\MediaProvider)\'');
        $this->addSql('CREATE TABLE reciters (id UUID NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(191) NOT NULL, description TEXT DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_36DF47FC989D9B62 ON reciters (slug)');
        $this->addSql('COMMENT ON COLUMN reciters.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE albums (id UUID NOT NULL, reciter_id UUID NOT NULL, title VARCHAR(255) NOT NULL, year VARCHAR(20) NOT NULL, artwork VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F4E2474FDEDF9489 ON albums (reciter_id)');
        $this->addSql('CREATE UNIQUE INDEX albums_reciter_id_year_unique ON albums (reciter_id, year)');
        $this->addSql('COMMENT ON COLUMN albums.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN albums.reciter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tracks (id UUID NOT NULL, album_id UUID NOT NULL, reciter_id UUID NOT NULL, lyric_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(191) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_246D2A2E1137ABCF ON tracks (album_id)');
        $this->addSql('CREATE INDEX IDX_246D2A2EDEDF9489 ON tracks (reciter_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_246D2A2EF9CD025C ON tracks (lyric_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_album_track_slug ON tracks (album_id, slug)');
        $this->addSql('COMMENT ON COLUMN tracks.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tracks.album_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tracks.reciter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tracks.lyric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE track_media (track_id UUID NOT NULL, media_id UUID NOT NULL, PRIMARY KEY(track_id, media_id))');
        $this->addSql('CREATE INDEX IDX_1ADB03AB5ED23C43 ON track_media (track_id)');
        $this->addSql('CREATE INDEX IDX_1ADB03ABEA9FDD75 ON track_media (media_id)');
        $this->addSql('COMMENT ON COLUMN track_media.track_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN track_media.media_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE lyrics ADD CONSTRAINT FK_3BDA6C665ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2E1137ABCF FOREIGN KEY (album_id) REFERENCES albums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EDEDF9489 FOREIGN KEY (reciter_id) REFERENCES reciters (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2EF9CD025C FOREIGN KEY (lyric_id) REFERENCES lyrics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE track_media ADD CONSTRAINT FK_1ADB03AB5ED23C43 FOREIGN KEY (track_id) REFERENCES tracks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE track_media ADD CONSTRAINT FK_1ADB03ABEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tracks DROP CONSTRAINT FK_246D2A2EF9CD025C');
        $this->addSql('ALTER TABLE track_media DROP CONSTRAINT FK_1ADB03ABEA9FDD75');
        $this->addSql('ALTER TABLE albums DROP CONSTRAINT FK_F4E2474FDEDF9489');
        $this->addSql('ALTER TABLE tracks DROP CONSTRAINT FK_246D2A2EDEDF9489');
        $this->addSql('ALTER TABLE tracks DROP CONSTRAINT FK_246D2A2E1137ABCF');
        $this->addSql('ALTER TABLE lyrics DROP CONSTRAINT FK_3BDA6C665ED23C43');
        $this->addSql('ALTER TABLE track_media DROP CONSTRAINT FK_1ADB03AB5ED23C43');
        $this->addSql('DROP TABLE lyrics');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE reciters');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE tracks');
        $this->addSql('DROP TABLE track_media');
    }
}
