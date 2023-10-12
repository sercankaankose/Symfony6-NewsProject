<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230907124017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE edit_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE edit_request (id INT NOT NULL, editor_id INT DEFAULT NULL, editor_note TEXT NOT NULL, request_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_816934259D1625D3 ON edit_request (editor_id)');
        $this->addSql('CREATE TABLE edit_request_news (edit_request_id INT NOT NULL, news_id INT NOT NULL, PRIMARY KEY(edit_request_id, news_id))');
        $this->addSql('CREATE INDEX IDX_53007F6CD7836BB1 ON edit_request_news (edit_request_id)');
        $this->addSql('CREATE INDEX IDX_53007F6CB5A459A0 ON edit_request_news (news_id)');
        $this->addSql('ALTER TABLE edit_request ADD CONSTRAINT FK_816934259D1625D3 FOREIGN KEY (editor_id) REFERENCES news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE edit_request_news ADD CONSTRAINT FK_53007F6CD7836BB1 FOREIGN KEY (edit_request_id) REFERENCES edit_request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE edit_request_news ADD CONSTRAINT FK_53007F6CB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE edit_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE edit_request DROP CONSTRAINT FK_816934259D1625D3');
        $this->addSql('ALTER TABLE edit_request_news DROP CONSTRAINT FK_53007F6CD7836BB1');
        $this->addSql('ALTER TABLE edit_request_news DROP CONSTRAINT FK_53007F6CB5A459A0');
        $this->addSql('DROP TABLE edit_request');
        $this->addSql('DROP TABLE edit_request_news');
    }
}
