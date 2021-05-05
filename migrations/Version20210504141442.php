<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504141442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, quack_id INT NOT NULL, duck_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_9474526CD3950CA9 (quack_id), INDEX IDX_9474526C12CF4A47 (duck_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CD3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C12CF4A47 FOREIGN KEY (duck_id) REFERENCES duck (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
    }
}
