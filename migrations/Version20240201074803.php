<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201074803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tour (id INT AUTO_INCREMENT NOT NULL, guia_id INT NOT NULL, ruta_id INT DEFAULT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, cancelado TINYINT(1) DEFAULT NULL, INDEX IDX_6AD1F96962AA81F (guia_id), INDEX IDX_6AD1F969ABBC4845 (ruta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F96962AA81F FOREIGN KEY (guia_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F969ABBC4845 FOREIGN KEY (ruta_id) REFERENCES ruta (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour DROP FOREIGN KEY FK_6AD1F96962AA81F');
        $this->addSql('ALTER TABLE tour DROP FOREIGN KEY FK_6AD1F969ABBC4845');
        $this->addSql('DROP TABLE tour');
    }
}
