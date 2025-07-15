<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715073421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE format_ticket (format_id INT NOT NULL, ticket_id INT NOT NULL, INDEX IDX_E90E4B59D629F605 (format_id), INDEX IDX_E90E4B59700047D2 (ticket_id), PRIMARY KEY(format_id, ticket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE format_ticket ADD CONSTRAINT FK_E90E4B59D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE format_ticket ADD CONSTRAINT FK_E90E4B59700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE format_ticket DROP FOREIGN KEY FK_E90E4B59D629F605');
        $this->addSql('ALTER TABLE format_ticket DROP FOREIGN KEY FK_E90E4B59700047D2');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE format_ticket');
    }
}
