<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210410101102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE platforms (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', creator_id VARCHAR(36) NOT NULL, name VARCHAR(200) NOT NULL, client VARCHAR(255) DEFAULT NULL, btw VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_178186E35E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F14965455E237E06 ON programs (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE platforms');
        $this->addSql('DROP INDEX UNIQ_F14965455E237E06 ON programs');
    }
}
