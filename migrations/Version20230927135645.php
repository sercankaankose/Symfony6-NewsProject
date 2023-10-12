<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927135645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT fk_816934259d1625d3');
        $this->addSql('DROP INDEX uniq_816934259d1625d3');
        $this->addSql('ALTER TABLE edit_request ADD editor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE edit_request RENAME COLUMN editor_id_id TO news_id');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_81693425B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_816934256995AC4C FOREIGN KEY (editor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_81693425B5A459A0 ON edit_request (news_id)');
        $this->addSql('CREATE INDEX IDX_816934256995AC4C ON edit_request (editor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_81693425B5A459A0');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_816934256995AC4C');
        $this->addSql('DROP INDEX IDX_81693425B5A459A0');
        $this->addSql('DROP INDEX IDX_816934256995AC4C');
        $this->addSql('ALTER TABLE edit_request ADD editor_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE edit_request DROP news_id');
        $this->addSql('ALTER TABLE edit_request DROP editor_id');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT fk_816934259d1625d3 FOREIGN KEY (editor_id_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_816934259d1625d3 ON edit_request (editor_id_id)');
    }
}
