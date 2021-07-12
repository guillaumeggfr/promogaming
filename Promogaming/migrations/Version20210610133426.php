<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610133426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plateform_game DROP FOREIGN KEY FK_345C4630CCAA542F');
        $this->addSql('DROP INDEX IDX_345C4630CCAA542F ON plateform_game');
        $this->addSql('ALTER TABLE plateform_game DROP plateform_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plateform_game ADD plateform_id INT NOT NULL');
        $this->addSql('ALTER TABLE plateform_game ADD CONSTRAINT FK_345C4630CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id)');
        $this->addSql('CREATE INDEX IDX_345C4630CCAA542F ON plateform_game (plateform_id)');
    }
}
