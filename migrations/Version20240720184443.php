<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720184443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faq ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E8FF75CCA76ED395 ON faq (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CCA76ED395');
        $this->addSql('DROP INDEX IDX_E8FF75CCA76ED395 ON faq');
        $this->addSql('ALTER TABLE faq DROP user_id');
    }
}
