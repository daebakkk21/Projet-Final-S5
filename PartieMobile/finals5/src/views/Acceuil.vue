<template>
  <ion-page>
    <ion-content class="ion-padding">
      <div class="dashboard-container">
        <!-- En-tête -->
        <div class="header-section">
          <h1 class="page-title">Accueil</h1>
          <p class="header-subtitle">Tableau de bord</p>
        </div>

        <!-- Message de bienvenue -->
        <div class="welcome-message">
          <p>Bienvenue sur Garage Elite. Gérez vos véhicules et suivez vos réparations en temps réel.</p>
        </div>

        <!-- Charge en cours -->
        <div v-if="isLoading" class="loading-state">
          <ion-spinner name="dots"></ion-spinner>
          <p>Chargement de vos voitures...</p>
        </div>

        <template v-else>
          <!-- Bouton Ajouter une voiture -->
          <div class="add-car-section">
            <button @click="addCar" class="add-car-btn">
              <ion-icon :icon="addOutline" class="btn-icon"></ion-icon>
              <span>Ajouter une voiture</span>
            </button>
          </div>

          <!-- Liste des voitures par statut -->
          <div class="cars-list-section">
            <!-- En attente -->
            <div class="status-group">
              <h2 class="status-title">
                <span class="status-badge pending">En attente</span>
              </h2>
              <div v-if="carsPending.length > 0" class="cars-group">
                <div v-for="car in carsPending" :key="car.id" class="car-card pending-card">
                  <div class="car-header">
                    <div class="car-icon">
                      <ion-icon :icon="carOutline"></ion-icon>
                    </div>
                    <div class="car-info">
                      <h3 class="car-name">{{ car.immatriculation }}</h3>
                      <p class="car-description">{{ car.description }}</p>
                    </div>
                  </div>
                  <p class="car-date">Depuis le {{ formatDate(car.date_depot) }}</p>
                </div>
              </div>
              <p v-else class="empty-state">Aucune voiture en attente</p>
            </div>

            <!-- En réparation -->
            <div class="status-group">
              <h2 class="status-title">
                <span class="status-badge repairing">En reparation</span>
              </h2>
              <div v-if="carsRepairing.length > 0" class="cars-group">
                <div v-for="car in carsRepairing" :key="car.id" class="car-card repairing-card">
                  <div class="car-header">
                    <div class="car-icon">
                      <ion-icon :icon="carOutline"></ion-icon>
                    </div>
                    <div class="car-info">
                      <h3 class="car-name">{{ car.immatriculation }}</h3>
                      <p class="car-description">{{ car.description }}</p>
                    </div>
                  </div>
                  <p class="car-date">En cours depuis le {{ formatDate(car.date_depot) }}</p>
                </div>
              </div>
              <p v-else class="empty-state">Aucune voiture en réparation</p>
            </div>

            <!-- Prête -->
            <div class="status-group">
              <h2 class="status-title">
                <span class="status-badge ready">Prête</span>
              </h2>
              <div v-if="carsReady.length > 0" class="cars-group">
                <div v-for="car in carsReady" :key="car.id" class="car-card ready-card">
                  <div class="car-header">
                    <div class="car-icon">
                      <ion-icon :icon="carOutline"></ion-icon>
                    </div>
                    <div class="car-info">
                      <h3 class="car-name">{{ car.immatriculation }}</h3>
                      <p class="car-description">{{ car.description }}</p>
                    </div>
                  </div>
                  <p class="car-date">Prête depuis le {{ formatDate(car.date_depot) }}</p>
                </div>
              </div>
              <p v-else class="empty-state">Aucune voiture prête</p>
            </div>
          </div>
        </template>

        <!-- Bouton de déconnexion -->
        <div class="logout-section">
          <button 
            @click="logout" 
            :disabled="isLoggingOut"
            class="logout-btn"
          >
            <ion-icon :icon="logOutOutline" class="btn-icon"></ion-icon>
            <span v-if="!isLoggingOut">Se déconnecter</span>
            <ion-spinner v-else name="dots"></ion-spinner>
          </button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { db } from "@/firebase";
import { ref as dbRef, get } from "firebase/database";
import { 
  IonPage, 
  IonContent,
  IonIcon,
  IonSpinner
} from '@ionic/vue';
import {
  logOutOutline,
  carOutline,
  addOutline
} from 'ionicons/icons';

const router = useRouter();
const isLoggingOut = ref(false);

// Voitures chargées depuis Firebase
const cars = ref<Array<{
  id: string;
  immatriculation: string;
  description: string;
  statut_id: string;
  date_depot: string;
}>>([]);

const isLoading = ref(true);

// Formater la date
const formatDate = (dateStr: string): string => {
  try {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  } catch {
    return dateStr;
  }
};

// Charger les voitures du client au montage
onMounted(async () => {
  try {
    // Récupérer le client depuis localStorage
    const clientStr = localStorage.getItem('client');
    if (!clientStr) {
      await router.push('/login');
      return;
    }

    let clientId: string;
    try {
      const client = JSON.parse(clientStr);
      clientId = client.id;
    } catch {
      console.error('Erreur: données de session invalides');
      return;
    }

    // Charger les voitures depuis Firebase
    const snapshot = await get(dbRef(db, 'voitures'));
    if (snapshot.exists()) {
      const allVoitures = snapshot.val();
      
      // Filtrer par client_uid
      const clientCars = Object.entries(allVoitures)
        .filter(([_, voiture]: [string, any]) => voiture.client_uid === clientId)
        .map(([id, voiture]: [string, any]) => ({
          id,
          immatriculation: voiture.immatriculation,
          description: voiture.description,
          statut_id: voiture.statut_id,
          date_depot: voiture.date_depot
        }));

      cars.value = clientCars;
      console.log('Voitures chargées:', clientCars);
    }
  } catch (e) {
    console.error('Erreur lors du chargement des voitures:', e);
  } finally {
    isLoading.value = false;
  }
});

// Computed properties pour filtrer par statut (en fonction des vrais statuts Firebase)
const carsPending = computed(() => 
  cars.value.filter(car => car.statut_id === 'enAttente')
);

const carsRepairing = computed(() => 
  cars.value.filter(car => car.statut_id === 'enReparation')
);

const carsReady = computed(() => 
  cars.value.filter(car => car.statut_id === 'prete')
);

const addCar = () => {
  router.push({ name: 'AddCar' });
};

const logout = async () => {
  isLoggingOut.value = true;
  try {
    // Auth is handled locally via Realtime DB + localStorage
    localStorage.removeItem('client');
    await router.replace({ name: 'Login' });
  } catch (e) {
    console.error('Sign out failed:', e);
  } finally {
    isLoggingOut.value = false;
  }
};
</script>

<style scoped>
/* Variables de thème */
:root {
  --brand-orange: #E85002;
  --brand-orange-dark: #C10801;
  --brand-orange-light: #F16001;
  --primary-black: #000000;
  --primary-white: #F9F9F9;
  --gray-medium: #646464;
  --gray-light: #A7A7A7;
  --gray-dark: #333333;
  
  --primary-bg: var(--primary-white);
  --card-bg: #FFFFFF;
  --border-color: rgba(0, 0, 0, 0.12);
  --shadow-color: rgba(0, 0, 0, 0.08);
  
  --status-pending: #FFA500;
  --status-repairing: #E85002;
  --status-ready: #059669;
}

ion-page {
  background-color: var(--primary-bg);
  color: var(--primary-black);
}

ion-content {
  --background: var(--primary-bg);
  --color: var(--primary-black);
  --padding-start: 20px;
  --padding-end: 20px;
  --padding-top: 24px;
  --padding-bottom: 24px;
}

/* Conteneur principal */
.dashboard-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 440px;
  margin: 0 auto;
}

/* En-tête */
.header-section {
  padding: 0 0 24px;
  border-bottom: 2px solid var(--border-color);
  margin-bottom: 16px;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  margin: 0 0 4px;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

.header-subtitle {
  font-size: 16px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 500;
}

/* Message de bienvenue */
.welcome-message {
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.08), rgba(193, 8, 1, 0.08));
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
  border-left: 4px solid var(--brand-orange);
}

.welcome-message p {
  font-size: 15px;
  color: var(--gray-dark);
  margin: 0;
  line-height: 1.5;
  font-weight: 500;
}

/* État de chargement */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 40px 20px;
  text-align: center;
}

.loading-state p {
  font-size: 14px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 500;
}

/* Section ajouter une voiture */
.add-car-section {
  display: flex;
  justify-content: center;
  margin-bottom: 28px;
}

.add-car-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  max-width: 340px;
  height: 48px;
  background: var(--brand-orange);
  color: var(--primary-white);
  border-radius: 12px;
  font-weight: 600;
  font-size: 16px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(232, 80, 2, 0.25);
}

.add-car-btn:active {
  background: #D14802;
  transform: translateY(1px);
  box-shadow: 0 2px 8px rgba(232, 80, 2, 0.35);
}

.btn-icon {
  font-size: 20px;
}

/* Section liste des voitures */
.cars-list-section {
  flex: 1;
  margin-bottom: 28px;
}

/* Groupe de statut */
.status-group {
  margin-bottom: 28px;
}

.status-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 14px;
  color: var(--primary-black);
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-badge {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--primary-white);
}

.status-badge.pending {
  background: var(--status-pending);
}

.status-badge.repairing {
  background: var(--status-repairing);
}

.status-badge.ready {
  background: var(--status-ready);
}

/* Groupe de cartes de voitures */
.cars-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Carte de voiture */
.car-card {
  background: var(--card-bg);
  border-radius: 14px;
  padding: 16px;
  border: 1.5px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-color);
  transition: all 0.2s ease;
}

.car-card:active {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px var(--shadow-color);
}

.car-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.car-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.1), rgba(193, 8, 1, 0.1));
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.car-icon ion-icon {
  font-size: 22px;
  color: var(--brand-orange);
}

.car-info {
  flex: 1;
}

.car-name {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 4px;
  color: var(--primary-black);
  font-family: 'Courier New', monospace;
  letter-spacing: 1px;
}

.car-description {
  font-size: 13px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 400;
  max-height: 32px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}

.car-date {
  font-size: 13px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 400;
}

.pending-card {
  border-left: 4px solid var(--status-pending);
}

.repairing-card {
  border-left: 4px solid var(--status-repairing);
}

.ready-card {
  border-left: 4px solid var(--status-ready);
}

/* État vide */
.empty-state {
  text-align: center;
  font-size: 14px;
  color: var(--gray-light);
  padding: 20px;
  margin: 0;
  font-weight: 500;
}

/* Section déconnexion */
.logout-section {
  padding-top: 20px;
  border-top: 1.5px solid var(--border-color);
  display: flex;
  justify-content: center;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  max-width: 340px;
  height: 46px;
  background: var(--brand-orange);
  color: var(--primary-white);
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(232, 80, 2, 0.25);
  padding: 0 24px;
  gap: 8px;
}

.logout-btn:not(:disabled):active {
  background: #D14802;
  transform: translateY(1px);
  box-shadow: 0 2px 8px rgba(232, 80, 2, 0.35);
}

.logout-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 350px) {
  .page-title {
    font-size: 28px;
  }
  
  ion-content {
    --padding-start: 16px;
    --padding-end: 16px;
  }
  
  .add-car-btn,
  .logout-btn {
    max-width: 300px;
    font-size: 14.5px;
  }
}

@media (min-width: 768px) {
  .dashboard-container {
    max-width: 400px;
  }
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.status-group {
  animation: fadeInUp 0.4s ease-out forwards;
}

.status-group:nth-child(1) { animation-delay: 0.1s; }
.status-group:nth-child(2) { animation-delay: 0.2s; }
.status-group:nth-child(3) { animation-delay: 0.3s; }

.car-card {
  animation: fadeInUp 0.4s ease-out backwards;
}
</style>