<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210401114250 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, livre_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_D9BEC0C437D925CB (livre_id), INDEX IDX_D9BEC0C4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, isbn_code VARCHAR(255) NOT NULL, synopsis LONGTEXT DEFAULT NULL, auteur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, livre_id INT NOT NULL, value INT NOT NULL, INDEX IDX_11BA68CA76ED395 (user_id), INDEX IDX_11BA68C37D925CB (livre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_livres (user_id INT NOT NULL, livres_id INT NOT NULL, INDEX IDX_F7239C99A76ED395 (user_id), INDEX IDX_F7239C99EBF07F38 (livres_id), PRIMARY KEY(user_id, livres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C437D925CB FOREIGN KEY (livre_id) REFERENCES livres (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C37D925CB FOREIGN KEY (livre_id) REFERENCES livres (id)');
        $this->addSql('ALTER TABLE user_livres ADD CONSTRAINT FK_F7239C99A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_livres ADD CONSTRAINT FK_F7239C99EBF07F38 FOREIGN KEY (livres_id) REFERENCES livres (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C437D925CB');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C37D925CB');
        $this->addSql('ALTER TABLE user_livres DROP FOREIGN KEY FK_F7239C99EBF07F38');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4A76ED395');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CA76ED395');
        $this->addSql('ALTER TABLE user_livres DROP FOREIGN KEY FK_F7239C99A76ED395');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE livres');
        $this->addSql('DROP TABLE notes');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_livres');
    }
}
