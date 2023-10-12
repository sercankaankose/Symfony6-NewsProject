<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927123914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_816934259D1625D3');
        $this->addSql('DROP INDEX UNIQ_816934259D1625D3');
        $this->addSql('ALTER TABLE edit_request RENAME COLUMN editor_id TO editor_id_id');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_816934259D1625D3 FOREIGN KEY (editor_id_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_816934259D1625D3 ON edit_request (editor_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT fk_816934259d1625d3');
        $this->addSql('DROP INDEX uniq_816934259d1625d3');
        $this->addSql('ALTER TABLE edit_request RENAME COLUMN editor_id_id TO editor_id');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT fk_816934259d1625d3 FOREIGN KEY (editor_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_816934259d1625d3 ON edit_request (editor_id)');
    }
}
