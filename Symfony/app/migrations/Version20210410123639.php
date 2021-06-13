<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210410123639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platforms CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE programs CHANGE date date DATE NOT NULL');
        $this->addSql('ALTER TABLE reports CHANGE date date DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platforms CHANGE created_at created_at DATE NOT NULL');
        $this->addSql('ALTER TABLE programs CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reports CHANGE date date DATETIME DEFAULT NULL');
    }
}
