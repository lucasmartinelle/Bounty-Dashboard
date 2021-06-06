<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409162829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', creator_id VARCHAR(36) NOT NULL, title VARCHAR(200) NOT NULL, severity DOUBLE PRECISION DEFAULT NULL, date DATETIME DEFAULT NULL, endpoint VARCHAR(255) DEFAULT NULL, identifiant VARCHAR(200) NOT NULL, status VARCHAR(100) DEFAULT NULL, gain SMALLINT DEFAULT NULL, template_id VARCHAR(36) DEFAULT NULL, program_id VARCHAR(36) NOT NULL, steps_to_reproduce LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', impact LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', mitigation LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ressources LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_F11FA745C90409EC (identifiant), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reports');
    }
}
