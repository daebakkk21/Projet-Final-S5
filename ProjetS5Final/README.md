# 🚗 GARAGE ELITE - Guide de Démarrage

## 📋 Description du Projet
Garage Elite est une plateforme complète de gestion automobile avec:
- **Backend**: PHP/Laravel
- **Frontend**: React
- **Base de données**: MySQL
- **Containerisation**: Docker & Docker Compose
- **Interface web**: phpMyAdmin

---

## 🚀 Démarrage du Projet

### 1️⃣ Lancer tous les conteneurs
```powershell
docker-compose up -d
```

Cela démarre:
- MySQL (port 3307)
- Backend Laravel (port 8000)
- Frontend React (port 3000)
- phpMyAdmin (port 8080)

### 2️⃣ Arrêter les conteneurs
```powershell
docker-compose down
```

### 3️⃣ Redémarrer les conteneurs
```powershell
docker-compose restart
```

### 4️⃣ Voir l'état des conteneurs
```powershell
docker ps
```

---

## 🗄️ Gestion de la Base de Données MySQL

### Accès à MySQL Interactive

**Méthode 1: Avec la base garage_elite déjà sélectionnée**
```powershell
docker exec -it garage_mysql mysql -u root -proot garage_elite
```

**Méthode 2: Sans base sélectionnée**
```powershell
docker exec -it garage_mysql mysql -u root -proot
```
Puis une fois connecté:
```sql
USE garage_elite;
```

### Commandes Utiles dans MySQL

```sql
-- Voir toutes les tables
SHOW TABLES;

-- Voir la structure d'une table
DESCRIBE clients;
DESCRIBE voitures;
DESCRIBE admins;

-- Voir les données
SELECT * FROM clients;
SELECT * FROM statut_voitures;
SELECT * FROM statut_reparations;
SELECT * FROM statut_paiements;

-- Insérer des données de test
INSERT INTO clients (firebase_uid, nom, prenom, email) VALUES 
('user_001', 'Dupont', 'Jean', 'jean.dupont@example.com');

INSERT INTO admins (nom, prenom, email, mdp) VALUES 
('Martin', 'Admin', 'admin@garage-elite.com', 'password123');

-- Quitter MySQL
exit
```

### Réinitialiser la Base de Données Complètement
```powershell
# Arrêter les conteneurs
docker-compose down

# Supprimer le volume MySQL
docker volume rm projets5final_mysql_data

# Redémarrer (la base sera recréée à partir de init.sql)
docker-compose up -d
```

---

## 🌐 Accès aux Applications

### Frontend React
- **URL**: http://localhost:3000
- Pages disponibles:
  - Accueil: `/`
  - Connexion: `/login`
  - Inscription: `/register`

### Backend Laravel
- **URL**: http://localhost:8000
- API disponible sur `/api`

### phpMyAdmin (Interface Graphique MySQL)
- **URL**: http://localhost:8080
- **Identifiants**:
  - User: `root`
  - Password: `root`
  - Database: `garage_elite`

---

## 📁 Structure du Projet

```
ProjetS5Final/
├── backend/                 # API Laravel
│   ├── src/
│   ├── docker/
│   └── Dockerfile
├── frontend/               # Application React
│   ├── src/
│   ├── public/
│   └── Dockerfile
├── docker/
│   └── mysql/
│       └── init.sql       # Initialisation de la BDD
├── docker-compose.yml     # Configuration Docker
├── firebase.txt           # Configuration Firebase
└── README.md             # Ce fichier
```

---

## 🗂️ Tables de la Base de Données

### Tables Disponibles:
- **clients** - Informations des clients
- **admins** - Comptes administrateurs
- **voitures** - Véhicules en réparation
- **statut_voitures** - États possibles des voitures
- **type_interventions** - Types de réparations disponibles
- **reparations** - Historique des réparations
- **statut_reparations** - États des réparations
- **paiements** - Transactions de paiement
- **statut_paiements** - États des paiements

---

## 🔧 Dépannage

### MySQL ne démarre pas
```powershell
# Vérifier les logs
docker logs garage_mysql

# Réinitialiser complètement
docker-compose down
docker volume rm projets5final_mysql_data
docker-compose up -d
```

### Port déjà utilisé
Si le port 3307 est occupé, modifier dans `docker-compose.yml`:
```yaml
ports:
  - "3307:3306"  # Changer le premier numéro
```

### Frontend ne charge pas
```powershell
# Reconstruire l'image
docker-compose build frontend
docker-compose up -d
```

---

## 📞 Identifiants par Défaut

| Service | User | Password | Base/Host |
|---------|------|----------|-----------|
| MySQL | root | root | localhost:3307 |
| phpMyAdmin | root | root | garage_elite |
| Laravel API | - | - | localhost:8000 |

---

## 🔐 Sécurité (À Faire en Production)

⚠️ **NE PAS** utiliser en production:
- Password "root" pour MySQL
- Variables d'environnement en dur dans docker-compose.yml
- APP_DEBUG = true

Utiliser `.env` files et secrets Docker

---

## 📝 Logs et Debugging

### Voir les logs d'un conteneur
```powershell
docker logs garage_mysql
docker logs garage_backend
docker logs garage_frontend
```

### Logs en temps réel
```powershell
docker logs -f garage_mysql
```

### Accéder au bash du conteneur
```powershell
docker exec -it garage_backend bash
docker exec -it garage_frontend sh
```

---

## 🎯 Prochaines Étapes

1. ✅ Vérifier que tous les conteneurs tournent
2. ✅ Accéder à http://localhost:3000
3. ✅ Tester la BDD via phpMyAdmin (http://localhost:8080)
4. ✅ Ajouter des données de test
5. ⏳ Intégrer Firebase pour l'authentification
6. ⏳ Développer les endpoints API Laravel
7. ⏳ Brancher le frontend sur les API

---

**Dernière mise à jour**: 3 Février 2026  
**Statut**: ✅ En Développement
