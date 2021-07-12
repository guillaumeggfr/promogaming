<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210614105603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plateform_game_game');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plateform_game_game (plateform_game_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_BFDCDAD386D4D5C8 (plateform_game_id), INDEX IDX_BFDCDAD3E48FD905 (game_id), PRIMARY KEY(plateform_game_id, game_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE plateform_game_game ADD CONSTRAINT FK_BFDCDAD386D4D5C8 FOREIGN KEY (plateform_game_id) REFERENCES plateform_game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plateform_game_game ADD CONSTRAINT FK_BFDCDAD3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
    }
}
