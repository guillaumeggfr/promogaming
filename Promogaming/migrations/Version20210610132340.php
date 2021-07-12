<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610132340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plateform_game_game (plateform_game_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_BFDCDAD386D4D5C8 (plateform_game_id), INDEX IDX_BFDCDAD3E48FD905 (game_id), PRIMARY KEY(plateform_game_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plateform_game_game ADD CONSTRAINT FK_BFDCDAD386D4D5C8 FOREIGN KEY (plateform_game_id) REFERENCES plateform_game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plateform_game_game ADD CONSTRAINT FK_BFDCDAD3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_C53D045FE48FD905 ON image (game_id)');
        $this->addSql('ALTER TABLE plateform_game ADD plateform_id INT NOT NULL');
        $this->addSql('ALTER TABLE plateform_game ADD CONSTRAINT FK_345C4630CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id)');
        $this->addSql('CREATE INDEX IDX_345C4630CCAA542F ON plateform_game (plateform_id)');
        $this->addSql('ALTER TABLE tag_game ADD game_id INT DEFAULT NULL, ADD tag_id INT NOT NULL');
        $this->addSql('ALTER TABLE tag_game ADD CONSTRAINT FK_CD248E3AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE tag_game ADD CONSTRAINT FK_CD248E3ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('CREATE INDEX IDX_CD248E3AE48FD905 ON tag_game (game_id)');
        $this->addSql('CREATE INDEX IDX_CD248E3ABAD26311 ON tag_game (tag_id)');
        $this->addSql('ALTER TABLE tag_user ADD tag_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_639C69FFBAD26311 ON tag_user (tag_id)');
        $this->addSql('CREATE INDEX IDX_639C69FFA76ED395 ON tag_user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plateform_game_game');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FE48FD905');
        $this->addSql('DROP INDEX IDX_C53D045FE48FD905 ON image');
        $this->addSql('ALTER TABLE image DROP game_id');
        $this->addSql('ALTER TABLE plateform_game DROP FOREIGN KEY FK_345C4630CCAA542F');
        $this->addSql('DROP INDEX IDX_345C4630CCAA542F ON plateform_game');
        $this->addSql('ALTER TABLE plateform_game DROP plateform_id');
        $this->addSql('ALTER TABLE tag_game DROP FOREIGN KEY FK_CD248E3AE48FD905');
        $this->addSql('ALTER TABLE tag_game DROP FOREIGN KEY FK_CD248E3ABAD26311');
        $this->addSql('DROP INDEX IDX_CD248E3AE48FD905 ON tag_game');
        $this->addSql('DROP INDEX IDX_CD248E3ABAD26311 ON tag_game');
        $this->addSql('ALTER TABLE tag_game DROP game_id, DROP tag_id');
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFBAD26311');
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFA76ED395');
        $this->addSql('DROP INDEX IDX_639C69FFBAD26311 ON tag_user');
        $this->addSql('DROP INDEX IDX_639C69FFA76ED395 ON tag_user');
        $this->addSql('ALTER TABLE tag_user DROP tag_id, DROP user_id');
    }
}
