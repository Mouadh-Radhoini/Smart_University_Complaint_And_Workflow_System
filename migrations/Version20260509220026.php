<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509220026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaint ADD title VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD description TEXT NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD status VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE complaint ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5B03A8386 FOREIGN KEY (created_by_id) REFERENCES user_email_no (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_5F2732B5B03A8386 ON complaint (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaint DROP CONSTRAINT FK_5F2732B5B03A8386');
        $this->addSql('DROP INDEX IDX_5F2732B5B03A8386');
        $this->addSql('ALTER TABLE complaint DROP title');
        $this->addSql('ALTER TABLE complaint DROP description');
        $this->addSql('ALTER TABLE complaint DROP status');
        $this->addSql('ALTER TABLE complaint DROP created_at');
        $this->addSql('ALTER TABLE complaint DROP updated_at');
        $this->addSql('ALTER TABLE complaint DROP created_by_id');
    }
}
