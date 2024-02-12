<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201081703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE valoracion (id INT AUTO_INCREMENT NOT NULL, reserva_id INT DEFAULT NULL, puntuacionguia INT NOT NULL, puntuacionruta INT NOT NULL, comentario VARCHAR(255) NOT NULL, INDEX IDX_6D3DE0F4D67139E8 (reserva_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE valoracion ADD CONSTRAINT FK_6D3DE0F4D67139E8 FOREIGN KEY (reserva_id) REFERENCES reserva (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE valoracion DROP FOREIGN KEY FK_6D3DE0F4D67139E8');
        $this->addSql('DROP TABLE valoracion');
    }
}
