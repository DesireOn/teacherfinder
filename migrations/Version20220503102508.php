<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503102508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher ADD city_id INT DEFAULT NULL, DROP city');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D58BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_B0F6A6D58BAC62AF ON teacher (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D58BAC62AF');
        $this->addSql('DROP INDEX IDX_B0F6A6D58BAC62AF ON teacher');
        $this->addSql('ALTER TABLE teacher ADD city VARCHAR(255) DEFAULT NULL, DROP city_id');
    }
}
