<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715073142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('CREATE INDEX IDX_795FD9BB700047D2 ON attachment (ticket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BB700047D2');
        $this->addSql('DROP INDEX IDX_795FD9BB700047D2 ON attachment');
        $this->addSql('ALTER TABLE attachment DROP ticket_id');
    }
}
