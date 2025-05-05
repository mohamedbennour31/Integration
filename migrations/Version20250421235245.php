<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421235245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE poll (id INT AUTO_INCREMENT NOT NULL, chat_id INT DEFAULT NULL, question VARCHAR(255) NOT NULL, is_closed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_84BCFA451A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE poll_option (id INT AUTO_INCREMENT NOT NULL, poll_id INT DEFAULT NULL, text VARCHAR(255) NOT NULL, vote_count INT NOT NULL, INDEX IDX_B68343EB3C947C0F (poll_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE poll_vote (id INT AUTO_INCREMENT NOT NULL, poll_id INT DEFAULT NULL, option_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_ED568EBE3C947C0F (poll_id), INDEX IDX_ED568EBEA7C41D6F (option_id), INDEX IDX_ED568EBEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll ADD CONSTRAINT FK_84BCFA451A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_option ADD CONSTRAINT FK_B68343EB3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote ADD CONSTRAINT FK_ED568EBE3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote ADD CONSTRAINT FK_ED568EBEA7C41D6F FOREIGN KEY (option_id) REFERENCES poll_option (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote ADD CONSTRAINT FK_ED568EBEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (idUser)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE polls DROP FOREIGN KEY FK_1D3CC6EE1A9A7125
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_options DROP FOREIGN KEY FK_2C6077B83C947C0F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes DROP FOREIGN KEY FK_373A070EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes DROP FOREIGN KEY FK_373A070EA7C41D6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes DROP FOREIGN KEY FK_373A070E3C947C0F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE polls
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poll_options
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poll_votes
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE communaute CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE id_participant id_participant INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FCF8DA6E6 FOREIGN KEY (id_participant) REFERENCES user (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AB55E24FCF8DA6E6 ON participation (id_participant)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE polls (id INT NOT NULL, chat_id INT DEFAULT NULL, question VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_closed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1D3CC6EE1A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE poll_options (id INT NOT NULL, poll_id INT DEFAULT NULL, text VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, vote_count INT NOT NULL, INDEX IDX_2C6077B83C947C0F (poll_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE poll_votes (id INT NOT NULL, poll_id INT DEFAULT NULL, option_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_373A070EA7C41D6F (option_id), INDEX IDX_373A070EA76ED395 (user_id), INDEX IDX_373A070E3C947C0F (poll_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE polls ADD CONSTRAINT FK_1D3CC6EE1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_options ADD CONSTRAINT FK_2C6077B83C947C0F FOREIGN KEY (poll_id) REFERENCES polls (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes ADD CONSTRAINT FK_373A070EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes ADD CONSTRAINT FK_373A070EA7C41D6F FOREIGN KEY (option_id) REFERENCES poll_options (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_votes ADD CONSTRAINT FK_373A070E3C947C0F FOREIGN KEY (poll_id) REFERENCES polls (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll DROP FOREIGN KEY FK_84BCFA451A9A7125
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_option DROP FOREIGN KEY FK_B68343EB3C947C0F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote DROP FOREIGN KEY FK_ED568EBE3C947C0F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote DROP FOREIGN KEY FK_ED568EBEA7C41D6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE poll_vote DROP FOREIGN KEY FK_ED568EBEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poll
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poll_option
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE poll_vote
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chat CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE communaute CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FCF8DA6E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AB55E24FCF8DA6E6 ON participation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE id_participant id_participant INT NOT NULL
        SQL);
    }
}
