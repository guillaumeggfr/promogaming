<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610133858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plateform_game ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE plateform_game ADD CONSTRAINT FK_345C4630E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_345C4630E48FD905 ON plateform_game (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plateform_game DROP FOREIGN KEY FK_345C4630E48FD905');
        $this->addSql('DROP INDEX IDX_345C4630E48FD905 ON plateform_game');
        $this->addSql('ALTER TABLE plateform_game DROP game_id');
    }
}
