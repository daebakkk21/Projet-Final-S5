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
                  <div class="car-footer">
                    <p class="car-date">Depuis le {{ formatDate(car.date_depot) }}</p>
                    <button @click="payRepair(car.id)" class="pay-icon-btn" title="Payer">
                      <ion-icon :icon="cardOutline"></ion-icon>
                    </button>
                  </div>
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
                  <div class="car-footer">
                    <p class="car-date">En cours depuis le {{ formatDate(car.date_depot) }}</p>
                    <button @click="payRepair(car.id)" class="pay-icon-btn" title="Payer">
                      <ion-icon :icon="cardOutline"></ion-icon>
                    </button>
                  </div>
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
                  <div class="car-footer">
                    <p class="car-date">Prête depuis le {{ formatDate(car.date_depot) }}</p>
                    <button @click="payRepair(car.id)" class="pay-icon-btn" title="Payer">
                      <ion-icon :icon="cardOutline"></ion-icon>
                    </button>
                  </div>
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
import { db } from "../firebase"
import { ref as dbRef, get, onValue } from "firebase/database";

import { 
  IonPage, 
  IonContent,
  IonIcon,
  IonSpinner
} from '@ionic/vue';
import {
  logOutOutline,
  carOutline,
  addOutline,
  cardOutline
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
  reparationId?: string;
  reparationStatus?: string;
  montantTotal?: number;
  derivedStatus?: string;
}>>([]);

// Données de base (client ID, voitures, réparations)
const clientId = ref<string>('');
const allVoitures = ref<Record<string, any>>({});
const allReparations = ref<Record<string, any>>({});
const allPayments = ref<Record<string, any>>({});

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

// Fonction pour recalculer les voitures basée sur les réparations actuelles
const updateCars = () => {
  if (!clientId.value) return;

  const updatedCars = Object.entries(allVoitures.value)
    .filter(([_, voiture]: [string, any]) => voiture.client_uid === clientId.value)
    .map(([id, voiture]: [string, any]) => {
      // Récupérer toutes les réparations pour cette voiture
      const reparationsForCar = Object.entries(allReparations.value)
        .filter(([_, rep]: [string, any]) => rep.voiture_id === id)
        .map(([repId, rep]: [string, any]) => ({ id: repId, ...rep }));

      // Choisir la réparation la plus récente
      let latestRep: any = undefined;
      if (reparationsForCar.length > 0) {
        latestRep = reparationsForCar.sort((a: any, b: any) => {
          const aTime = new Date(a.created_at || a.start_time || 0).getTime();
          const bTime = new Date(b.created_at || b.start_time || 0).getTime();
          return bTime - aTime;
        })[0];
      }

      const reparationId = latestRep ? latestRep.id : undefined;
      const reparationStatus = latestRep ? latestRep.statut_id : undefined;

      // Chercher le paiement pour cette réparation
      let montantTotal = 0;
      let paid = false;
      if (reparationId) {
        const payment = Object.entries(allPayments.value).find(
          ([_, pay]: [string, any]) => pay.reparation_id === reparationId
        );
        if (payment) {
          montantTotal = (payment[1] as any).montant_total || 0;
          // Consider payment as completed if statut_id === 'complété' or paid flag present
          paid = (payment[1] as any).statut_id === 'complété' || !!(payment[1] as any).paid;
        }
        // Also consider the repair's own paid flag
        if (latestRep && latestRep.paid) {
          paid = true;
        }
      }

      // Dériver le statut affiché
      let derivedStatus = voiture.statut_id;
      if (reparationStatus === 'enCours') {
        derivedStatus = 'enReparation';
      } else if (reparationStatus === 'terminee') {
        derivedStatus = 'prete';
      } else if (reparationStatus === 'planifiee') {
        derivedStatus = 'enAttente';
      } else if (voiture.statut_id === 'prete') {
        derivedStatus = 'prete';
      }

      return {
        id,
        immatriculation: voiture.immatriculation,
        description: voiture.description,
        statut_id: voiture.statut_id,
        date_depot: voiture.date_depot,
        reparationId,
        reparationStatus,
        montantTotal,
        paid,
        derivedStatus
      };
    });

  cars.value = updatedCars;
};

// Charger les voitures du client au montage et ajouter les listeners
onMounted(async () => {
  try {
    // Récupérer le client depuis localStorage
    const clientStr = localStorage.getItem('client');
    if (!clientStr) {
      await router.push('/login');
      return;
    }

    try {
      const client = JSON.parse(clientStr);
      clientId.value = client.id;
    } catch {
      console.error('Erreur: données de session invalides');
      return;
    }

    // Charger les données initiales
    const voituresSnapshot = await get(dbRef(db, 'voitures'));
    const reparationsSnapshot = await get(dbRef(db, 'reparations'));
    const archivedReparationsSnapshot = await get(dbRef(db, 'archives/reparations'));
    const paymentsSnapshot = await get(dbRef(db, 'paiements'));

    if (voituresSnapshot.exists()) {
      allVoitures.value = voituresSnapshot.val();
    }
    if (reparationsSnapshot.exists()) {
      allReparations.value = reparationsSnapshot.val();
    }
    // Fusionner les réparations archivées avec les réparations actives
    if (archivedReparationsSnapshot.exists()) {
      const archived = archivedReparationsSnapshot.val();
      allReparations.value = { ...allReparations.value, ...archived };
    }
    if (paymentsSnapshot.exists()) {
      allPayments.value = paymentsSnapshot.val();
    }

    // Calcul initial des voitures
    updateCars();

    // Ajouter un listener en temps réel sur les réparations
    onValue(dbRef(db, 'reparations'), (snapshot) => {
      if (snapshot.exists()) {
        allReparations.value = snapshot.val();
        // Recharger aussi les archives
        get(dbRef(db, 'archives/reparations')).then(archivedSnapshot => {
          if (archivedSnapshot.exists()) {
            allReparations.value = { ...allReparations.value, ...archivedSnapshot.val() };
          }
          updateCars();
        });
      } else {
        // Si aucune réparation active, charger juste les archives
        get(dbRef(db, 'archives/reparations')).then(archivedSnapshot => {
          if (archivedSnapshot.exists()) {
            allReparations.value = archivedSnapshot.val();
          }
          updateCars();
        });
      }
    });

    // Ajouter un listener sur les archives pour les mises à jour en temps réel
    onValue(dbRef(db, 'archives/reparations'), (snapshot) => {
      if (snapshot.exists()) {
        const activeReps = Object.entries(allReparations.value || {})
          .filter(([key]) => !(allReparations.value[key]?.recovered === true))
          .reduce((acc, [key, val]) => ({ ...acc, [key]: val }), {});
        
        allReparations.value = { ...activeReps, ...snapshot.val() };
        console.log('🔄 Archives mises à jour:', snapshot.val());
        updateCars();
      }
    });

    console.log('✅ Voitures chargées et listeners configurés');
  } catch (e) {
    console.error('Erreur lors du chargement des voitures:', e);
  } finally {
    isLoading.value = false;
  }
});

// Computed properties pour filtrer par statut (utilisent maintenant `derivedStatus`)
const carsPending = computed(() =>
  cars.value.filter(car => car.derivedStatus === 'enAttente')
);

const carsRepairing = computed(() =>
  cars.value.filter(car => car.derivedStatus === 'enReparation')
);

const carsReady = computed(() =>
  cars.value.filter(car => car.derivedStatus === 'prete')
);

const addCar = () => {
  router.push({ name: 'AddCar' });
};

const payRepair = (carId: string) => {
  const car = cars.value.find(c => c.id === carId);
  if (!car) {
    alert('Voiture non trouvée');
    return;
  }
  
  if (car.reparationId) {
    // Si la voiture a une réparation, aller à la page de paiement
    router.push({
      name: 'Payment',
      params: {
        reparationId: car.reparationId,
        carId: carId
      }
    });
  } else {
    // Sinon, afficher un message
    alert('Aucune réparation associée pour cette voiture. Une réparation doit être lancée en premier.');
  }
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
/* Variables de thème - Luxe Rouge & Noir */
:root {
  /* Palette Orange & Noir */
  --primary: #E85002; /* Orange */
  --primary-dark: #D14802;
  --primary-light: #F16001;
  --secondary: #000000; /* Noir */
  --secondary-light: #1a1a1a;
  --secondary-lighter: #2a2a2a;
  --accent: #ffffff; /* Blanc pur */
  --accent-dark: #f0f0f0;
  --accent-light: #ffffff;
  --gold: #D9C3AB; /* Beige pour accents */
  --gold-dark: #C9A876;
  
  /* Utilitaires */
  --success: #10b981;
  --warning: #fbbf24;
  --danger: #ef4444;
  --info: #3b82f6;
  
  /* Ombres */
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
  --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.25);
  --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.3);
  --shadow-orange: 0 4px 20px rgba(232, 80, 2, 0.3);
  
  /* Bordures */
  --border-radius: 12px;
  --border-radius-lg: 20px;
  --border-radius-full: 50px;
  
  /* Compatibilité */
  --primary-bg: var(--secondary);
  --card-bg: var(--secondary-lighter);
  --border-color: rgba(255, 255, 255, 0.1);
  --shadow-color: rgba(0, 0, 0, 0.3);
  
  --status-pending: var(--warning);
  --status-repairing: var(--primary);
  --status-ready: var(--success);
}

ion-page {
  background-color: var(--secondary);
  color: var(--accent);
  font-family: 'Montserrat', sans-serif;
}

ion-content {
  --background: var(--secondary);
  --color: var(--accent);
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
  color: var(--accent);
  letter-spacing: -0.5px;
  text-transform: uppercase;
  text-shadow: 0 2px 10px rgba(255, 0, 0, 0.2);
}

.header-subtitle {
  font-size: 16px;
  color: var(--gold);
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

/* Pied de carte avec date et bouton paiement */
.car-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  gap: 12px;
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

/* Petit bouton icone pour paiement */
.pay-icon-btn {
  width: 36px;
  height: 36px;
  min-width: 36px;
  min-height: 36px;
  padding: 0;
  background: linear-gradient(135deg, #059669, #047857);
  color: var(--primary-white);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.pay-icon-btn ion-icon {
  font-size: 18px;
}

.pay-icon-btn:active {
  background: linear-gradient(135deg, #047857, #065f46);
  transform: scale(0.95);
  box-shadow: 0 1px 3px rgba(5, 150, 105, 0.2);
}

/* Ancien style à ne pas utiliser mais garder pour compatibilité */
.pay-btn {
  width: 100%;
  height: 40px;
  background: linear-gradient(135deg, #059669, #047857);
  color: var(--primary-white);
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-top: 12px;
}

.pay-btn:active {
  background: linear-gradient(135deg, #047857, #065f46);
  transform: translateY(1px);
  box-shadow: 0 1px 4px rgba(5, 150, 105, 0.2);
}

.pay-btn .btn-icon {
  font-size: 16px;
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