<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201080632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reserva (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, tour_id INT NOT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, INDEX IDX_188D2E3BDB38439E (usuario_id), INDEX IDX_188D2E3B15ED8D43 (tour_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reserva ADD CONSTRAINT FK_188D2E3BDB38439E FOREIGN KEY (usuario_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reserva ADD CONSTRAINT FK_188D2E3B15ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reserva DROP FOREIGN KEY FK_188D2E3BDB38439E');
        $this->addSql('ALTER TABLE reserva DROP FOREIGN KEY FK_188D2E3B15ED8D43');
        $this->addSql('DROP TABLE reserva');
    }
}
