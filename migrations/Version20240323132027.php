<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323132027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terrains (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blacklist DROP FOREIGN KEY blacklist_ibfk_1');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_2');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1');
        $this->addSql('ALTER TABLE fournisseur DROP FOREIGN KEY fournisseur_ibfk_1');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY historique_ibfk_1');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY membre_ibfk_1');
        $this->addSql('ALTER TABLE membreparequipe DROP FOREIGN KEY fk_equipe_membre_equipe');
        $this->addSql('ALTER TABLE membreparequipe DROP FOREIGN KEY fk_equipe_membre_membre');
        $this->addSql('ALTER TABLE organisateur DROP FOREIGN KEY organisateur_ibfk_1');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY participation_ibfk_1');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY participation_ibfk_2');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY fk_payment_membre');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY payment_ibfk_2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY product_ibfk_3');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY product_ibfk_1');
        $this->addSql('ALTER TABLE proprietaire_de_terrain DROP FOREIGN KEY proprietaire_de_terrain_ibfk_1');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY fk_publication_id');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY fk_reponse_id');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY reservation_ibfk_1');
        $this->addSql('ALTER TABLE terrain DROP FOREIGN KEY terrain_ibfk_1');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY tournoi_ibfk_1');
        $this->addSql('DROP TABLE blacklist');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE historique');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE membreparequipe');
        $this->addSql('DROP TABLE organisateur');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE proprietaire_de_terrain');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE terrain');
        $this->addSql('DROP TABLE tournoi');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE avis MODIFY idAvis INT NOT NULL');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY fk_avis_terrain');
        $this->addSql('DROP INDEX fk_avis_terrain ON avis');
        $this->addSql('DROP INDEX `primary` ON avis');
        $this->addSql('ALTER TABLE avis DROP terrain_id, DROP commentaire, DROP note, CHANGE idAvis id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blacklist (idBlackList INT AUTO_INCREMENT NOT NULL, idReservation INT NOT NULL, duree INT NOT NULL, cause VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX idReservation (idReservation), PRIMARY KEY(idBlackList)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, idmembre INT NOT NULL, idproduit INT NOT NULL, DateCommande VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX commande_ibfk_1 (idproduit), INDEX commande_ibfk_2 (idmembre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE equipe (idEquipe INT AUTO_INCREMENT NOT NULL, nomEquipe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nbreJoueur INT DEFAULT NULL, PRIMARY KEY(idEquipe)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fournisseur (Fournisseur_id INT NOT NULL, Nom_Sociéte VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(Fournisseur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE historique (idHistorique INT AUTO_INCREMENT NOT NULL, dateReservation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, heureReservation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, idReservation INT DEFAULT NULL, INDEX idReservation (idReservation), PRIMARY KEY(idHistorique)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE membre (JoueurId INT NOT NULL, PRIMARY KEY(JoueurId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE membreparequipe (id INT AUTO_INCREMENT NOT NULL, idEquipe INT NOT NULL, idMembre INT NOT NULL, INDEX idMembre (idMembre), INDEX idEquipe (idEquipe), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE organisateur (Organisateur_id INT NOT NULL, Nom_Organisation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(Organisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, idmembre INT NOT NULL, idTournoi INT NOT NULL, Status TINYINT(1) NOT NULL, datec VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, NomEquipe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX idequipe (idmembre), INDEX idTournoi (idTournoi), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE payment (idPayment INT AUTO_INCREMENT NOT NULL, idMembre INT DEFAULT NULL, idReservation INT DEFAULT NULL, datePayment VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, horairePayment VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Payed TINYINT(1) DEFAULT NULL, INDEX fk_payment_membre (idMembre), INDEX idReservation (idReservation), PRIMARY KEY(idPayment)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, idfournisseur INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, prix NUMERIC(10, 2) DEFAULT \'NULL\', image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, categorie INT DEFAULT NULL, INDEX product_ibfk_3 (idfournisseur), INDEX product_ibfk_1 (categorie), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE proprietaire_de_terrain (Proprietaire_de_terrain_id INT NOT NULL, PRIMARY KEY(Proprietaire_de_terrain_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, iduser INT NOT NULL, Sujet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Contenue VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_publication_id (iduser), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, idpublication INT NOT NULL, Commentaire VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_reponse_id (idpublication), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation (idReservation INT AUTO_INCREMENT NOT NULL, isConfirm TINYINT(1) NOT NULL, dateReservation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, heureReservation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, idTerrain INT NOT NULL, nomEquipe1 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nomEquipe2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, INDEX idTerrain (idTerrain), PRIMARY KEY(idReservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE terrain (id INT AUTO_INCREMENT NOT NULL, idprop INT DEFAULT NULL, address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, gradin TINYINT(1) NOT NULL, vestiaire TINYINT(1) NOT NULL, status TINYINT(1) NOT NULL, nomTerrain VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, duree INT NOT NULL, gouvernorat VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, video VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX terrain_ibfk_1 (idprop), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tournoi (id INT AUTO_INCREMENT NOT NULL, NbmaxEquipe INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, affiche VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, datedebut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, datefin VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, idOrganisateur INT NOT NULL, INDEX idOrganisateur (idOrganisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, Email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Age INT NOT NULL, Phone INT NOT NULL, Address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, Role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, Status TINYINT(1) NOT NULL, DatedeCreation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, VerificationCode VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, isVerified TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE blacklist ADD CONSTRAINT blacklist_ibfk_1 FOREIGN KEY (idReservation) REFERENCES reservation (idReservation)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_2 FOREIGN KEY (idmembre) REFERENCES membre (JoueurId)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (idproduit) REFERENCES product (id)');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT fournisseur_ibfk_1 FOREIGN KEY (Fournisseur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT historique_ibfk_1 FOREIGN KEY (idReservation) REFERENCES reservation (idReservation)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT membre_ibfk_1 FOREIGN KEY (JoueurId) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT fk_equipe_membre_equipe FOREIGN KEY (idEquipe) REFERENCES equipe (idEquipe)');
        $this->addSql('ALTER TABLE membreparequipe ADD CONSTRAINT fk_equipe_membre_membre FOREIGN KEY (idMembre) REFERENCES membre (JoueurId)');
        $this->addSql('ALTER TABLE organisateur ADD CONSTRAINT organisateur_ibfk_1 FOREIGN KEY (Organisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT participation_ibfk_1 FOREIGN KEY (idTournoi) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT participation_ibfk_2 FOREIGN KEY (idmembre) REFERENCES membre (JoueurId)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_payment_membre FOREIGN KEY (idMembre) REFERENCES membre (JoueurId)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT payment_ibfk_2 FOREIGN KEY (idReservation) REFERENCES reservation (idReservation) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product_ibfk_3 FOREIGN KEY (idfournisseur) REFERENCES fournisseur (Fournisseur_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product_ibfk_1 FOREIGN KEY (categorie) REFERENCES categorie (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE proprietaire_de_terrain ADD CONSTRAINT proprietaire_de_terrain_ibfk_1 FOREIGN KEY (Proprietaire_de_terrain_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT fk_publication_id FOREIGN KEY (iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_reponse_id FOREIGN KEY (idpublication) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT reservation_ibfk_1 FOREIGN KEY (idTerrain) REFERENCES terrain (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE terrain ADD CONSTRAINT terrain_ibfk_1 FOREIGN KEY (idprop) REFERENCES proprietaire_de_terrain (Proprietaire_de_terrain_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT tournoi_ibfk_1 FOREIGN KEY (idOrganisateur) REFERENCES organisateur (Organisateur_id)');
        $this->addSql('DROP TABLE terrains');
        $this->addSql('ALTER TABLE avis MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON avis');
        $this->addSql('ALTER TABLE avis ADD terrain_id INT NOT NULL, ADD commentaire VARCHAR(255) NOT NULL, ADD note INT NOT NULL, CHANGE id idAvis INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT fk_avis_terrain FOREIGN KEY (terrain_id) REFERENCES terrain (id)');
        $this->addSql('CREATE INDEX fk_avis_terrain ON avis (terrain_id)');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (idAvis)');
    }
}
