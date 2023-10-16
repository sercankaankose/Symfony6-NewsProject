<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930102441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_816934256995AC4C');
        $this->addSql('ALTER TABLE edit_request ALTER editor_note SET NOT NULL');
        $this->addSql('ALTER TABLE edit_request ALTER request_at SET NOT NULL');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_816934256995AC4C FOREIGN KEY (editor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT fk_816934256995ac4c');
        $this->addSql('ALTER TABLE edit_request ALTER editor_note DROP NOT NULL');
        $this->addSql('ALTER TABLE edit_request ALTER request_at DROP NOT NULL');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT fk_816934256995ac4c FOREIGN KEY (editor_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
