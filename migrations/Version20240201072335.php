<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201072335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ruta_item (ruta_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_837FEAD6ABBC4845 (ruta_id), INDEX IDX_837FEAD6126F525E (item_id), PRIMARY KEY(ruta_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ruta_item ADD CONSTRAINT FK_837FEAD6ABBC4845 FOREIGN KEY (ruta_id) REFERENCES ruta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ruta_item ADD CONSTRAINT FK_837FEAD6126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ruta_item DROP FOREIGN KEY FK_837FEAD6ABBC4845');
        $this->addSql('ALTER TABLE ruta_item DROP FOREIGN KEY FK_837FEAD6126F525E');
        $this->addSql('DROP TABLE ruta_item');
    }
}
