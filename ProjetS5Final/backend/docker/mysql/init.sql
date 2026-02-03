CREATE DATABASE IF NOT EXISTS garage_elite;
USE garage_elite;

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(150) NOT NULL UNIQUE,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
);

CREATE TABLE voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    immatriculation VARCHAR(50),
    description TEXT,
    statut_id INT NOT NULL,
    date_depot TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_voiture_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    CONSTRAINT fk_voiture_statut FOREIGN KEY (statut_id) REFERENCES statut_voitures(id) ON DELETE CASCADE
);

CREATE TABLE statut_voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

CREATE TABLE type_interventions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prix DECIMAL(10,2),
    duree_secondes INT
);

CREATE TABLE reparations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voiture_id INT NOT NULL,
    type_intervention_id INT NOT NULL,
    statut_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reparation_voiture FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE,
    CONSTRAINT fk_reparation_type FOREIGN KEY (type_intervention_id) REFERENCES type_interventions(id) ON DELETE CASCADE,
    CONSTRAINT fk_reparation_statut FOREIGN KEY (statut_id) REFERENCES statut_reparations(id) ON DELETE CASCADE
);

CREATE TABLE statut_reparations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reparation_id INT NOT NULL,
    montant_total DECIMAL(10,2),
    statut_id INT NOT NULL,
    date_paiement TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_paiement_reparation FOREIGN KEY (reparation_id) REFERENCES reparations(id) ON DELETE CASCADE,
    CONSTRAINT fk_paiement_statut FOREIGN KEY (statut_id) REFERENCES statut_paiements(id) ON DELETE CASCADE
);

CREATE TABLE statut_paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

-- Données de test
INSERT INTO clients (firebase_uid, nom, prenom, email) VALUES
("demo_user_123", "Demo", "User", "demo@garage-elite.com");

INSERT INTO admins (nom, prenom, email, mdp) VALUES
("Admin", "User", "admin@garage-elite.com", "$2y$10$abc123def456");

INSERT INTO statut_voitures (nom, description) VALUES
("En attente", "La voiture est en attente de réparation"),
("En réparation", "La voiture est actuellement en réparation"),
("Prête", "La voiture est prête à être récupérée");

INSERT INTO statut_reparations (nom, description) VALUES
("Planifiée", "La réparation est planifiée"),
("En cours", "La réparation est en cours"),
("Terminée", "La réparation est terminée");

INSERT INTO statut_paiements (nom, description) VALUES
("En attente", "Le paiement est en attente"),
("Complété", "Le paiement a été effectué"),
("Remboursé", "Le paiement a été remboursé");
