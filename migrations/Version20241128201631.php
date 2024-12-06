<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128201631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignede_commande ADD id_commande_id INT DEFAULT NULL, ADD id_panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lignede_commande ADD CONSTRAINT FK_9A8072B49AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE lignede_commande ADD CONSTRAINT FK_9A8072B477482E5B FOREIGN KEY (id_panier_id) REFERENCES panier (id)');
        $this->addSql('CREATE INDEX IDX_9A8072B49AF8E3A3 ON lignede_commande (id_commande_id)');
        $this->addSql('CREATE INDEX IDX_9A8072B477482E5B ON lignede_commande (id_panier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignede_commande DROP FOREIGN KEY FK_9A8072B49AF8E3A3');
        $this->addSql('ALTER TABLE lignede_commande DROP FOREIGN KEY FK_9A8072B477482E5B');
        $this->addSql('DROP INDEX IDX_9A8072B49AF8E3A3 ON lignede_commande');
        $this->addSql('DROP INDEX IDX_9A8072B477482E5B ON lignede_commande');
        $this->addSql('ALTER TABLE lignede_commande DROP id_commande_id, DROP id_panier_id');
    }
}
