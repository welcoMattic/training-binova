<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407140408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE matching (
              id BLOB NOT NULL --(DC2Type:uuid)
              ,
              for_user_id INTEGER NOT NULL,
              conference_id INTEGER NOT NULL,
              PRIMARY KEY(id),
              CONSTRAINT FK_DC10F2899B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
              CONSTRAINT FK_DC10F289604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_DC10F2899B5BB4B8 ON matching (for_user_id)');
        $this->addSql('CREATE INDEX IDX_DC10F289604B8382 ON matching (conference_id)');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__messenger_messages AS
            SELECT
              id,
              body,
              headers,
              queue_name,
              created_at,
              available_at,
              delivered_at
            FROM
              messenger_messages
        SQL);
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              body CLOB NOT NULL,
              headers CLOB NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO messenger_messages (
              id, body, headers, queue_name, created_at,
              available_at, delivered_at
            )
            SELECT
              id,
              body,
              headers,
              queue_name,
              created_at,
              available_at,
              delivered_at
            FROM
              __temp__messenger_messages
        SQL);
        $this->addSql('DROP TABLE __temp__messenger_messages');
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (
              queue_name, available_at, delivered_at,
              id
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE matching');
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__messenger_messages AS
            SELECT
              id,
              body,
              headers,
              queue_name,
              created_at,
              available_at,
              delivered_at
            FROM
              messenger_messages
        SQL);
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              body CLOB NOT NULL,
              headers CLOB NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
              ,
              delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO messenger_messages (
              id, body, headers, queue_name, created_at,
              available_at, delivered_at
            )
            SELECT
              id,
              body,
              headers,
              queue_name,
              created_at,
              available_at,
              delivered_at
            FROM
              __temp__messenger_messages
        SQL);
        $this->addSql('DROP TABLE __temp__messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
    }
}
