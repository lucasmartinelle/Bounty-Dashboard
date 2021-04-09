<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409091949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC224CAAA76ED395 ON billing (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC224CAAE7927C74 ON billing (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_EC224CAAA76ED395 ON billing');
        $this->addSql('DROP INDEX UNIQ_EC224CAAE7927C74 ON billing');
        $this->addSql('ALTER TABLE billing CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
