<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929111311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE edit_requestt_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE edit_requestt (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE edit_request ALTER editor_note TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE edit_request ALTER request_at TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE edit_requestt_id_seq CASCADE');
        $this->addSql('DROP TABLE edit_requestt');
        $this->addSql('ALTER TABLE edit_request ALTER editor_note TYPE TEXT');
        $this->addSql('ALTER TABLE edit_request ALTER request_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }
}
