<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929122704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE edit_requestt_id_seq CASCADE');
        $this->addSql('CREATE TABLE edit_request (id INT NOT NULL, news_id INT DEFAULT NULL, editor_id INT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, editor_note TEXT DEFAULT NULL, request_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, accepted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81693425B5A459A0 ON edit_request (news_id)');
        $this->addSql('CREATE INDEX IDX_816934256995AC4C ON edit_request (editor_id)');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_81693425B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_816934256995AC4C FOREIGN KEY (editor_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE edit_requestt_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_81693425B5A459A0');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_816934256995AC4C');
        $this->addSql('DROP TABLE edit_request');
    }
}
