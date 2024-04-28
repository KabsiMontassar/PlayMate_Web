<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423215015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE SalesOrderNumber');
        $this->addSql('CREATE TABLE commande (id INT IDENTITY NOT NULL, idproduit INT, idmembre INT, DateCommande NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX commande_ibfk_1 ON commande (idproduit)');
        $this->addSql('CREATE INDEX fk_avis_ods ON commande (idmembre)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF6A1BE49 FOREIGN KEY (idproduit) REFERENCES product (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D92095685 FOREIGN KEY (idmembre) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE avis ALTER COLUMN terrain_id INT');
        $this->addSql('ALTER TABLE avis ALTER COLUMN idAvis INT IDENTITY NOT NULL');
        $this->addSql('EXEC sp_rename N\'avis.idx_8f91abf08a2d8b41\', N\'fk_avis_terrain\', N\'INDEX\'');
        $this->addSql('ALTER TABLE blacklist ALTER COLUMN idBlackList INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE blacklist ALTER COLUMN idReservation INT');
        $this->addSql('ALTER TABLE blacklist ADD CONSTRAINT FK_3B175385295B62D FOREIGN KEY (idReservation) REFERENCES reservation (idReservation)');
        $this->addSql('CREATE INDEX idReservation ON blacklist (idReservation)');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN id INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN nom NVARCHAR(255)');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT DF_497DD634_6C6E55B5 DEFAULT \'NULL\' FOR nom');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN description VARCHAR(MAX)');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT DF_497DD634_6DE44026 DEFAULT \'NULL\' FOR description');
        $this->addSql('ALTER TABLE historique ALTER COLUMN idHistorique INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE historique ALTER COLUMN idReservation INT NOT NULL');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC295B62D FOREIGN KEY (idReservation) REFERENCES reservation (idReservation)');
        $this->addSql('CREATE INDEX IDX_EDBFD5EC295B62D ON historique (idReservation)');
        $this->addSql('ALTER TABLE membreparequipe DROP CONSTRAINT FK_membreparequipe_utilisateur');
        $this->addSql('ALTER TABLE membreparequipe DROP CONSTRAINT FK_membreparequipe_equipe');
        $this->addSql('ALTER TABLE membreparequipe ALTER COLUMN idEquipe INT');
        $this->addSql('ALTER TABLE membreparequipe ALTER COLUMN idMembre INT');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT FK_7F3500B995A553B3 FOREIGN KEY (idMembre) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT FK_7F3500B94758128F FOREIGN KEY (idEquipe) REFERENCES equipe (idEquipe)');
        $this->addSql('EXEC sp_rename N\'membreparequipe.idx_7f3500b995a553b3\', N\'fk_abaa\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'membreparequipe.idx_7f3500b94758128f\', N\'idEquipe\', N\'INDEX\'');
        $this->addSql('ALTER TABLE notification ALTER COLUMN id INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE notification ALTER COLUMN iduser INT');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA5E5C27E9 FOREIGN KEY (iduser) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX fk_aaa ON notification (iduser)');
        $this->addSql('ALTER TABLE participation ALTER COLUMN idmembre INT');
        $this->addSql('ALTER TABLE participation ALTER COLUMN idTournoi INT');
        $this->addSql('ALTER TABLE participation ALTER COLUMN Status BIT NOT NULL');
        $this->addSql('ALTER TABLE participation ALTER COLUMN datec DATE NOT NULL');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F816B22D FOREIGN KEY (idTournoi) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F92095685 FOREIGN KEY (idmembre) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX idTournoi ON participation (idTournoi)');
        $this->addSql('CREATE INDEX fk_adsds ON participation (idmembre)');
        $this->addSql('ALTER TABLE payment ALTER COLUMN idPayment INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER COLUMN datepayment DATE NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER COLUMN Payed BIT');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT DF_6D28840D_74DEC1CC DEFAULT NULL FOR Payed');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D95A553B3 FOREIGN KEY (idMembre) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D295B62D FOREIGN KEY (idReservation) REFERENCES reservation (idReservation)');
        $this->addSql('CREATE INDEX idReservation ON payment (idReservation)');
        $this->addSql('CREATE INDEX fk_payment_membre ON payment (idMembre)');
        $this->addSql('ALTER TABLE product ALTER COLUMN id INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE product ALTER COLUMN idfournisseur INT');
        $this->addSql('ALTER TABLE product ALTER COLUMN nom NVARCHAR(255)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT DF_D34A04AD_6C6E55B5 DEFAULT \'NULL\' FOR nom');
        $this->addSql('ALTER TABLE product ALTER COLUMN description VARCHAR(MAX)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT DF_D34A04AD_6DE44026 DEFAULT \'NULL\' FOR description');
        $this->addSql('ALTER TABLE product ALTER COLUMN prix NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT DF_D34A04AD_F7EFEA5E DEFAULT \'NULL\' FOR prix');
        $this->addSql('ALTER TABLE product ALTER COLUMN image NVARCHAR(255)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT DF_D34A04AD_C53D045F DEFAULT \'NULL\' FOR image');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF05FBA9D FOREIGN KEY (idfournisseur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD497DD634 FOREIGN KEY (categorie) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX product_ibfk_1 ON product (categorie)');
        $this->addSql('CREATE INDEX fk_prr ON product (idfournisseur)');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN idReservation INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN isConfirm BIT NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN idTerrain INT');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D8CF3843 FOREIGN KEY (idTerrain) REFERENCES terrain (id)');
        $this->addSql('CREATE INDEX idTerrain ON reservation (idTerrain)');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN id INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN idprop INT');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN gradin BIT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN vestiaire BIT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN status BIT NOT NULL');
        $this->addSql('ALTER TABLE terrain ADD CONSTRAINT FK_C87653B17CA1344A FOREIGN KEY (idprop) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX fk_avis_prop ON terrain (idprop)');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN id INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN idOrganisateur INT');
        $this->addSql('ALTER TABLE tournoi DROP CONSTRAINT DF_18AFD9DF_B09C8CBB');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN visite INT NOT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF1549C9B5 FOREIGN KEY (idOrganisateur) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX fk_avis_organisateur ON tournoi (idOrganisateur)');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Address NVARCHAR(255)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT DF_1D1C63B3_C2F3561D DEFAULT \'NULL\' FOR Address');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Role NVARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT DF_1D1C63B3_F75B2554 DEFAULT \'NULL\' FOR Role');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT DF_1D1C63B3_4FC2B5B');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Image NVARCHAR(255)');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Status BIT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX Email ON utilisateur (Email) WHERE Email IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('CREATE SCHEMA SalesLT');
        $this->addSql('CREATE SEQUENCE SalesOrderNumber START WITH 1 INCREMENT BY 1 MINVALUE 1');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DF6A1BE49');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67D92095685');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE avis ALTER COLUMN terrain_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ALTER COLUMN idAvis INT NOT NULL');
        $this->addSql('EXEC sp_rename N\'avis.fk_avis_terrain\', N\'IDX_8F91ABF08A2D8B41\', N\'INDEX\'');
        $this->addSql('ALTER TABLE blacklist DROP CONSTRAINT FK_3B175385295B62D');
        $this->addSql('DROP INDEX idReservation ON blacklist');
        $this->addSql('ALTER TABLE blacklist ALTER COLUMN idBlackList INT NOT NULL');
        $this->addSql('ALTER TABLE blacklist ALTER COLUMN idReservation INT NOT NULL');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN id INT NOT NULL');
        $this->addSql('ALTER TABLE categorie DROP CONSTRAINT DF_497DD634_6C6E55B5');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN nom NVARCHAR(255)');
        $this->addSql('ALTER TABLE categorie DROP CONSTRAINT DF_497DD634_6DE44026');
        $this->addSql('ALTER TABLE categorie ALTER COLUMN description VARCHAR(MAX)');
        $this->addSql('ALTER TABLE historique DROP CONSTRAINT FK_EDBFD5EC295B62D');
        $this->addSql('DROP INDEX IDX_EDBFD5EC295B62D ON historique');
        $this->addSql('ALTER TABLE historique ALTER COLUMN idHistorique INT NOT NULL');
        $this->addSql('ALTER TABLE historique ALTER COLUMN idReservation INT');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA5E5C27E9');
        $this->addSql('DROP INDEX fk_aaa ON notification');
        $this->addSql('ALTER TABLE notification ALTER COLUMN id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ALTER COLUMN iduser INT NOT NULL');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D95A553B3');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D295B62D');
        $this->addSql('DROP INDEX idReservation ON payment');
        $this->addSql('DROP INDEX fk_payment_membre ON payment');
        $this->addSql('ALTER TABLE payment ALTER COLUMN idPayment INT NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER COLUMN datePayment NVARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT DF_6D28840D_74DEC1CC');
        $this->addSql('ALTER TABLE payment ALTER COLUMN Payed SMALLINT');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADF05FBA9D');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD497DD634');
        $this->addSql('DROP INDEX product_ibfk_1 ON product');
        $this->addSql('DROP INDEX fk_prr ON product');
        $this->addSql('ALTER TABLE product ALTER COLUMN id INT NOT NULL');
        $this->addSql('ALTER TABLE product ALTER COLUMN idfournisseur INT NOT NULL');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT DF_D34A04AD_6C6E55B5');
        $this->addSql('ALTER TABLE product ALTER COLUMN nom NVARCHAR(255)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT DF_D34A04AD_6DE44026');
        $this->addSql('ALTER TABLE product ALTER COLUMN description VARCHAR(MAX)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT DF_D34A04AD_F7EFEA5E');
        $this->addSql('ALTER TABLE product ALTER COLUMN prix NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT DF_D34A04AD_C53D045F');
        $this->addSql('ALTER TABLE product ALTER COLUMN image NVARCHAR(255)');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955D8CF3843');
        $this->addSql('DROP INDEX idTerrain ON reservation');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN idReservation INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN isConfirm SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER COLUMN idTerrain INT NOT NULL');
        $this->addSql('ALTER TABLE terrain DROP CONSTRAINT FK_C87653B17CA1344A');
        $this->addSql('DROP INDEX fk_avis_prop ON terrain');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN id INT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN idprop INT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN gradin SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN vestiaire SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE terrain ALTER COLUMN status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE tournoi DROP CONSTRAINT FK_18AFD9DF1549C9B5');
        $this->addSql('DROP INDEX fk_avis_organisateur ON tournoi');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN id INT NOT NULL');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN visite INT NOT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT DF_18AFD9DF_B09C8CBB DEFAULT 0 FOR visite');
        $this->addSql('ALTER TABLE tournoi ALTER COLUMN idOrganisateur INT NOT NULL');
        $this->addSql('DROP INDEX Email ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT DF_1D1C63B3_C2F3561D');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Address NVARCHAR(255)');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT DF_1D1C63B3_F75B2554');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Role NVARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Image NVARCHAR(255)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT DF_1D1C63B3_4FC2B5B DEFAULT \'default.jpg\' FOR Image');
        $this->addSql('ALTER TABLE utilisateur ALTER COLUMN Status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT FK_AB55E24F816B22D');
        $this->addSql('ALTER TABLE participation DROP CONSTRAINT FK_AB55E24F92095685');
        $this->addSql('DROP INDEX idTournoi ON participation');
        $this->addSql('DROP INDEX fk_adsds ON participation');
        $this->addSql('ALTER TABLE participation ALTER COLUMN idmembre INT NOT NULL');
        $this->addSql('ALTER TABLE participation ALTER COLUMN Status SMALLINT');
        $this->addSql('ALTER TABLE participation ALTER COLUMN datec DATE');
        $this->addSql('ALTER TABLE participation ALTER COLUMN idTournoi INT NOT NULL');
        $this->addSql('ALTER TABLE membreparequipe DROP CONSTRAINT FK_7F3500B995A553B3');
        $this->addSql('ALTER TABLE membreparequipe DROP CONSTRAINT FK_7F3500B94758128F');
        $this->addSql('ALTER TABLE membreparequipe ALTER COLUMN idMembre INT NOT NULL');
        $this->addSql('ALTER TABLE membreparequipe ALTER COLUMN idEquipe INT NOT NULL');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT FK_membreparequipe_utilisateur FOREIGN KEY (idMembre) REFERENCES utilisateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT FK_membreparequipe_equipe FOREIGN KEY (idEquipe) REFERENCES equipe (idEquipe) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('EXEC sp_rename N\'membreparequipe.fk_abaa\', N\'IDX_7F3500B995A553B3\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'membreparequipe.idequipe\', N\'IDX_7F3500B94758128F\', N\'INDEX\'');
    }
}
