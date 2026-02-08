CREATE DATABASE IF NOT EXISTS garage_elite;
USE garage_elite;

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    firebase_uid VARCHAR(150),
    password VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),
    role VARCHAR(50) DEFAULT "admin",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    immatriculation VARCHAR(50),
    description TEXT,
    statut_id INT NOT NULL,
    date_depot TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    duree_secondes INT,
    admin_id INT NOT NULL,
    CONSTRAINT fk_type_intervention_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

CREATE TABLE reparations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voiture_id INT NOT NULL,
    type_intervention_id INT NOT NULL,
    statut_id INT NOT NULL,
    debut TIMESTAMP NULL,
    fin TIMESTAMP NULL,
    progression INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reparation_voiture FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE,
    CONSTRAINT fk_reparation_type FOREIGN KEY (type_intervention_id) REFERENCES type_interventions(id) ON DELETE CASCADE,
    CONSTRAINT fk_reparation_statut FOREIGN KEY (statut_id) REFERENCES statut_reparations(id) ON DELETE CASCADE
);

CREATE TABLE statut_reparations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reparation_id INT NOT NULL,
    montant_total DECIMAL(10,2),
    statut_id INT NOT NULL,
    date_paiement TIMESTAMP NULL,
    methode VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_paiement_reparation FOREIGN KEY (reparation_id) REFERENCES reparations(id) ON DELETE CASCADE,
    CONSTRAINT fk_paiement_statut FOREIGN KEY (statut_id) REFERENCES statut_paiements(id) ON DELETE CASCADE
);

CREATE TABLE statut_paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
);

-- Données de test
INSERT INTO clients (nom, prenom, email, password) VALUES
("Demo", "User", "demo@garage-elite.com", "000");

INSERT INTO admins (login, password_hash) VALUES
("admin", "111");
