<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824064456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        //$this->addSql('ALTER TABLE news ALTER editor_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN news.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER INDEX idx_1dd399509d1625d3 RENAME TO IDX_1DD399506995AC4C');
        $this->addSql('ALTER INDEX idx_1dd3995069ccbe9a RENAME TO IDX_1DD39950F675F31B');
        $this->addSql('ALTER TABLE news ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE news DROP created_at');
        $this->addSql('ALTER TABLE news ALTER editor_id DROP NOT NULL');
        $this->addSql('ALTER INDEX idx_1dd39950f675f31b RENAME TO idx_1dd3995069ccbe9a');
        $this->addSql('ALTER INDEX idx_1dd399506995ac4c RENAME TO idx_1dd399509d1625d3');
    }
}
