<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210617153458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_user MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFA76ED395');
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFBAD26311');
        $this->addSql('ALTER TABLE tag_user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE tag_user DROP id');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_user ADD PRIMARY KEY (tag_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFBAD26311');
        $this->addSql('ALTER TABLE tag_user DROP FOREIGN KEY FK_639C69FFA76ED395');
        $this->addSql('ALTER TABLE tag_user ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE tag_user ADD CONSTRAINT FK_639C69FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
