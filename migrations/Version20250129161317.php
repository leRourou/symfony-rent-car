<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129161317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review ADD car_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_794381C6C3C6F69F ON review (car_id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6C3C6F69F');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP INDEX IDX_794381C6C3C6F69F ON review');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('ALTER TABLE review DROP car_id, DROP user_id');
    }
}
