<template>
  <ion-page>
    <ion-header :translucent="true">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/acceuil"></ion-back-button>
        </ion-buttons>
        <ion-title>Paiement</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <!-- Chargement -->
      <div v-if="isLoading" class="loading-container">
        <ion-spinner name="dots"></ion-spinner>
        <p>Chargement des détails...</p>
      </div>

      <!-- Contenu principal -->
      <template v-else-if="reparation && car">
        <div class="payment-container ion-padding">
          <!-- En-tête -->
          <div class="header-section">
            <h1 class="page-title">Détails du paiement</h1>
          </div>

          <!-- Carte de la voiture -->
          <ion-card class="car-info-card">
            <ion-card-header>
              <ion-card-title>Voiture</ion-card-title>
            </ion-card-header>
            <ion-card-content>
              <div class="info-row">
                <span class="label">Immatriculation:</span>
                <span class="value">{{ car.immatriculation }}</span>
              </div>
              <div class="info-row">
                <span class="label">Description:</span>
                <span class="value">{{ car.description }}</span>
              </div>
              <div class="info-row">
                <span class="label">Date de dépôt:</span>
                <span class="value">{{ formatDate(car.date_depot) }}</span>
              </div>
            </ion-card-content>
          </ion-card>

          <!-- Détails de la réparation -->
          <ion-card class="repair-info-card">
            <ion-card-header>
              <ion-card-title>Réparation</ion-card-title>
            </ion-card-header>
            <ion-card-content>
              <div class="info-row">
                <span class="label">Type d'intervention:</span>
                <span class="value">{{ typeIntervention?.nom || 'N/A' }}</span>
              </div>
              <div class="info-row">
                <span class="label">Prix unitaire:</span>
                <span class="value">{{ typeIntervention?.prix || 0 }}€</span>
              </div>
              <div class="info-row">
                <span class="label">Date de création:</span>
                <span class="value">{{ formatDate(reparation.created_at) }}</span>
              </div>
              <div class="info-row">
                <span class="label">Statut:</span>
                <ion-badge 
                  :color="getStatusColor(reparation.statut_id)"
                  class="status-badge"
                >
                  {{ getStatusLabel(reparation.statut_id) }}
                </ion-badge>
              </div>
            </ion-card-content>
          </ion-card>

          <!-- Résumé du paiement -->
          <ion-card class="payment-summary-card">
            <ion-card-header>
              <ion-card-title>Résumé du paiement</ion-card-title>
            </ion-card-header>
            <ion-card-content>
              <div class="info-row payment-row">
                <span class="label">Montant:</span>
                <span class="value montant">{{ paymentData?.montant_total || typeIntervention?.prix || 0 }}€</span>
              </div>
              <div class="info-row">
                <span class="label">Statut du paiement:</span>
                <ion-badge 
                  :color="getPaymentStatusColor(reparation?.paid ? 'complété' : (paymentData?.statut_id || 'enAttente'))"
                  class="payment-status-badge"
                >
                  {{ getPaymentStatusLabel(reparation?.paid ? 'complété' : (paymentData?.statut_id || 'enAttente')) }}
                </ion-badge>
              </div>
              <div v-if="paymentData?.created_at" class="info-row">
                <span class="label">Date du paiement:</span>
                <span class="value">{{ formatDate(paymentData.created_at) }}</span>
              </div>
            </ion-card-content>
          </ion-card>

          <!-- Boutons d'action - Avant paiement -->
          <div class="actions-section" v-if="!reparation?.paid">
            <ion-button 
              expand="block" 
              @click="processPayment"
              :disabled="isProcessing"
              class="pay-action-btn"
            >
              <ion-spinner v-if="isProcessing" name="dots" class="spinner"></ion-spinner>
              <span v-else>
                <ion-icon :icon="cardOutline" slot="start"></ion-icon>
                Effectuer le paiement
              </span>
            </ion-button>
          </div>

          <!-- Message paiement complété + bouton récupérer -->
          <div v-else class="success-message">
            <ion-icon :icon="checkmarkCircleOutline" size="large"></ion-icon>
            <p>Paiement complété avec succès!</p>
            
            <!-- Bouton récupérer si payé mais pas encore récupéré -->
            <ion-button 
              v-if="!reparation?.recovered"
              expand="block" 
              @click="recoverCar"
              :disabled="isRecovering"
              class="recover-action-btn"
            >
              <ion-spinner v-if="isRecovering" name="dots" class="spinner"></ion-spinner>
              <span v-else>
                <ion-icon :icon="carOutline" slot="start"></ion-icon>
                Récupérer la voiture
              </span>
            </ion-button>

            <!-- Message récupération complétée -->
            <div v-else class="recovery-success">
              <ion-icon :icon="checkmarkCircleOutline" size="large"></ion-icon>
              <p>Voiture récupérée avec succès!</p>
            </div>
          </div>
        </div>
      </template>

      <!-- Erreur -->
      <div v-else class="error-container">
        <p>Impossible de charger les détails du paiement</p>
        <ion-button @click="$router.back()">Retour</ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { db } from '../firebase';
import { ref as dbRef, get, update } from 'firebase/database';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonButtons,
  IonBackButton,
  IonContent,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardContent,
  IonButton,
  IonBadge,
  IonIcon,
  IonSpinner
} from '@ionic/vue';
import {
  cardOutline,
  checkmarkCircleOutline,
  carOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const isLoading = ref(true);
const isProcessing = ref(false);
const isRecovering = ref(false);

interface Car {
  id: string;
  immatriculation: string;
  description: string;
  date_depot: string;
  statut_id: string;
}

interface Reparation {
  id: string;
  created_at: string;
  statut_id: string;
  type_intervention_id: string;
  voiture_id: string;
  paid?: boolean;
  in_garage?: boolean;
  recovered?: boolean;
  recovered_at?: string;
  end_time?: string;
}

interface TypeIntervention {
  nom: string;
  prix: number;
  duree_secondes: number;
}

const car = ref<Car | null>(null);
const reparation = ref<Reparation | null>(null);
const typeIntervention = ref<TypeIntervention | null>(null);
const paymentData = ref<any>(null);

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

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    planifiee: 'Planifiée',
    enCours: 'En cours',
    terminee: 'Terminée'
  };
  return labels[status] || status;
};

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    planifiee: 'primary',
    enCours: 'warning',
    terminee: 'success'
  };
  return colors[status] || 'medium';
};

const getPaymentStatusLabel = (status?: string): string => {
  const labels: Record<string, string> = {
    enAttente: 'En attente',
    complété: 'Payé',
    remboursé: 'Remboursé'
  };
  return labels[status || ''] || 'N/A';
};

const getPaymentStatusColor = (status?: string): string => {
  const colors: Record<string, string> = {
    enAttente: 'warning',
    complété: 'success',
    remboursé: 'medium'
  };
  return colors[status || ''] || 'medium';
};

onMounted(async () => {
  try {
    const reparationId = route.params.reparationId as string;
    const carId = route.params.carId as string;

    if (!reparationId || !carId) {
      throw new Error('Paramètres invalides');
    }

    // Charger la voiture
    const carSnapshot = await get(dbRef(db, `voitures/${carId}`));
    if (carSnapshot.exists()) {
      car.value = {
        id: carId,
        ...carSnapshot.val()
      };
    }

    // Charger la réparation
    const reparationSnapshot = await get(dbRef(db, `reparations/${reparationId}`));
    if (reparationSnapshot.exists()) {
      const repData = reparationSnapshot.val();
      reparation.value = {
        id: reparationId,
        ...repData
      };

      // Charger le type d'intervention
      const typeSnapshot = await get(
        dbRef(db, `type_interventions/${repData.type_intervention_id}`)
      );
      if (typeSnapshot.exists()) {
        typeIntervention.value = typeSnapshot.val();
      }
    }

    // Charger le paiement associé
    const paymentsSnapshot = await get(dbRef(db, 'paiements'));
    if (paymentsSnapshot.exists()) {
      const allPayments = paymentsSnapshot.val();
      const payment = Object.entries(allPayments).find(
        ([_, pay]: [string, any]) => pay.reparation_id === reparationId
      );
      if (payment) {
        paymentData.value = {
          id: payment[0],
          ...payment[1]
        };
      }
    }
  } catch (error) {
    console.error('Erreur lors du chargement:', error);
  } finally {
    isLoading.value = false;
  }
});

const processPayment = async () => {
  try {
    isProcessing.value = true;

    if (!reparation.value?.id) {
      throw new Error('Réparation non trouvée');
    }

    // Simuler le traitement du paiement (2 secondes)
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Mettre à jour la réparation: NE PAS changer le statut ni in_garage,
    // seulement marquer comme payé pour que le back-office sache que
    // le client a payé. Nous n'assignons pas la réparation comme "terminée".
    await update(dbRef(db, `reparations/${reparation.value.id}`), {
      paid: true
    });

    // Créer ou mettre à jour le paiement avec le montant du type d'intervention
    if (!paymentData.value?.id && typeIntervention.value) {
      // Créer un nouveau paiement
      const newPaymentId = `paiement_${reparation.value.id}_${Date.now()}`;
      const createdAt = new Date().toISOString();
      await update(dbRef(db, `paiements/${newPaymentId}`), {
        reparation_id: reparation.value.id,
        montant_total: typeIntervention.value.prix,
        statut_id: 'complété',
        created_at: createdAt
      });
      paymentData.value = {
        id: newPaymentId,
        reparation_id: reparation.value.id,
        montant_total: typeIntervention.value.prix,
        statut_id: 'complété',
        created_at: createdAt
      };
    } else if (paymentData.value?.id) {
      // Mettre à jour le paiement existant
      await update(dbRef(db, `paiements/${paymentData.value.id}`), {
        statut_id: 'complété'
      });
      paymentData.value.statut_id = 'complété';
    }

    // Mettre à jour localement la réparation (paid flag seulement)
    if (reparation.value) {
      reparation.value.paid = true;
      // Ne pas modifier reparation.value.statut_id ni reparation.value.in_garage
    }

    console.log('✅ Paiement effectué (simulation) — uniquement flag `paid` mis à jour.');
  } catch (error) {
    console.error('Erreur lors du paiement:', error);
    alert('Erreur lors du paiement');
  } finally {
    isProcessing.value = false;
  }
};

const recoverCar = async () => {
  try {
    isRecovering.value = true;

    if (!reparation.value?.id) {
      throw new Error('Réparation non trouvée');
    }

    // Simuler le traitement de la récupération (2 secondes)
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Mettre à jour la réparation: in_garage: false, recovered: true
    const updates: any = {
      in_garage: false,
      recovered: true,
      recovered_at: new Date().toISOString()
    };

    await update(dbRef(db, `reparations/${reparation.value.id}`), updates);

    // Copier la réparation dans les archives
    const reparationInArchives = {
      ...reparation.value,
      ...updates
    };

    await update(dbRef(db, `archives/reparations/${reparation.value.id}`), reparationInArchives);

    // Optionnel: supprimer de reparations? (À confirmer avec l'utilisateur)
    // Pour l'instant on la laisse pour traçabilité

    // Mettre à jour localement
    if (reparation.value) {
      reparation.value.in_garage = false;
      reparation.value.recovered = true;
      reparation.value.recovered_at = new Date().toISOString();
    }

    console.log('✅ Voiture récupérée avec succès!');
  } catch (error) {
    console.error('Erreur lors de la récupération:', error);
    alert('Erreur lors de la récupération');
  } finally {
    isRecovering.value = false;
  }
};
</script>

<style scoped>
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
  --primary-white: var(--accent);
  --primary-black: var(--secondary);
  --brand-green: var(--success);
  --status-success: var(--success);
  --gray-medium: #a7a7a7;
  --gray-light: #c0c0c0;
  --border-color: rgba(255, 255, 255, 0.1);
  --shadow-color: rgba(0, 0, 0, 0.3);
}

ion-page {
  background-color: var(--primary-white);
  color: var(--primary-black);
}

ion-content {
  --background: var(--primary-white);
  --color: var(--primary-black);
}

.payment-container {
  margin: 0 auto;
  max-width: 440px;
}

.header-section {
  padding: 20px 0;
  margin-bottom: 20px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

ion-card {
  margin: 0 0 16px;
  border-radius: 14px;
  box-shadow: 0 2px 8px var(--shadow-color);
}

ion-card-header {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border-color);
}

ion-card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--primary-black);
}

ion-card-content {
  padding: 16px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid var(--border-color);
}

.info-row:last-child {
  border-bottom: none;
}

.info-row.payment-row {
  padding: 12px 0;
  font-size: 18px;
  font-weight: 700;
  border-bottom: 2px solid var(--border-color);
}

.label {
  font-size: 13px;
  color: var(--gray-medium);
  font-weight: 600;
}

.value {
  font-size: 14px;
  color: var(--primary-black);
  font-weight: 600;
}

.montant {
  font-size: 20px;
  color: var(--primary);
  font-weight: 800;
}

.status-badge,
.payment-status-badge {
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}

.actions-section {
  margin: 24px 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.pay-action-btn {
  --background: linear-gradient(135deg, var(--primary-light), var(--primary));
  --background-activated: var(--primary-dark);
  --box-shadow: var(--shadow-orange);
  height: 48px;
  font-weight: 700;
  font-size: 15px;
  border-radius: 12px;
  margin: 0;
  color: var(--accent);
}

.recover-action-btn {
  --background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  --background-activated: var(--primary-dark);
  --box-shadow: var(--shadow-orange);
  height: 48px;
  font-weight: 700;
  font-size: 15px;
  border-radius: 12px;
  margin: 20px 0 0 0;
  color: var(--accent);
}

.spinner {
  margin-right: 8px;
}

.success-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
}

.success-message ion-icon {
  font-size: 64px;
  color: var(--primary);
  margin-bottom: 16px;
}

.success-message p {
  font-size: 16px;
  font-weight: 600;
  color: var(--primary-black);
}

.recovery-success {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 30px 20px;
  text-align: center;
  margin-top: 20px;
  background-color: rgba(232, 80, 2, 0.1);
  border-radius: 12px;
  border: 2px solid var(--primary);
}

.recovery-success ion-icon {
  font-size: 48px;
  color: var(--primary);
  margin-bottom: 12px;
}

.recovery-success p {
  font-size: 14px;
  font-weight: 600;
  color: var(--primary-black);
  margin: 0;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  gap: 16px;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  gap: 16px;
  padding: 20px;
  text-align: center;
}
</style>
