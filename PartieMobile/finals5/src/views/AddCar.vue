<template>
  <ion-page>
    <ion-header :translucent="true">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/acceuil"></ion-back-button>
        </ion-buttons>
        <ion-title>Ajouter une voiture</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="ion-padding">
      <div class="form-container">
        <!-- En-tête -->
        <div class="form-header">
          <h1 class="form-title">Déposer une voiture</h1>
          <p class="form-subtitle">Décrivez la panne et sélectionnez le type d'intervention</p>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submitForm" class="form-section">
          <!-- Immatriculation -->
          <div class="form-group">
            <label for="immatriculation" class="form-label">Immatriculation</label>
            <input
              id="immatriculation"
              v-model="form.immatriculation"
              type="text"
              placeholder="Ex: AB-123-CD"
              class="form-input"
              required
            />
          </div>

          <!-- Description du problème -->
          <div class="form-group">
            <label for="description" class="form-label">Description du problème</label>
            <p class="form-hint">Décrivez en détail la panne ou le problème</p>
            <textarea
              id="description"
              v-model="form.description"
              placeholder="Ex: Pneu avant gauche crevé, problème de démarrage..."
              class="form-textarea"
              rows="5"
              required
            ></textarea>
          </div>

          <!-- Type d'intervention -->
          <div class="form-group">
            <label for="typeIntervention" class="form-label">Type d'intervention</label>
            <select v-model="form.typeIntervention" id="typeIntervention" class="form-select" required>
              <option value="">Sélectionner un type...</option>
              <option v-for="(intervention, id) in typeInterventions" :key="id" :value="id">
                {{ intervention.nom }} ({{ intervention.prix }}€)
              </option>
            </select>
          </div>

          <!-- Bouton Envoyer -->
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <ion-icon v-if="!isSubmitting" :icon="sendOutline" class="btn-icon"></ion-icon>
            <ion-spinner v-else name="dots"></ion-spinner>
            <span v-if="!isSubmitting">Envoyer au garage</span>
          </button>
        </form>

        <!-- Message informatif -->
        <div class="info-box">
          <ion-icon :icon="informationCircleOutline" class="info-icon"></ion-icon>
          <p>Vos informations seront traitées rapidement par nos techniciens. Vous recevrez une estimation dans les 24h.</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { db } from "../firebase"
import { ref as dbRef, get, set } from "firebase/database";
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonButtons,
  IonBackButton,
  IonContent,
  IonIcon,
  IonSpinner
} from '@ionic/vue';
import {
  sendOutline,
  informationCircleOutline
} from 'ionicons/icons';

const router = useRouter();
const isSubmitting = ref(false);
const typeInterventions = ref<Record<string, any>>({});

const form = ref({
  immatriculation: '',
  description: '',
  typeIntervention: ''
});

// Charger les types d'interventions au montage
onMounted(async () => {
  try {
    const snapshot = await get(dbRef(db, 'type_interventions'));
    if (snapshot.exists()) {
      typeInterventions.value = snapshot.val();
    }
  } catch (e) {
    console.error('Erreur lors du chargement des types d\'interventions:', e);
  }
});

const submitForm = async () => {
  // Récupérer le client depuis localStorage
  const clientStr = localStorage.getItem('client');
  if (!clientStr) {
    alert('Vous devez être connecté pour ajouter une voiture.');
    await router.push('/login');
    return;
  }

  let clientId: string;
  try {
    const client = JSON.parse(clientStr);
    clientId = client.id;
  } catch {
    alert('Erreur: données de session invalides.');
    return;
  }

  // Validation simple
  if (!form.value.immatriculation || !form.value.description || !form.value.typeIntervention) {
    alert('Veuillez remplir tous les champs obligatoires');
    return;
  }

  isSubmitting.value = true;

  try {
    const now = new Date().toISOString();
    
    // Générer les IDs
    const voitureId = `voiture_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 8)}`;
    const reparationId = `reparation_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 8)}`;

    // Créer les données de la voiture
    const voitureData = {
      client_uid: clientId,
      created_at: now,
      date_depot: now,
      description: form.value.description.trim(),
      immatriculation: form.value.immatriculation.trim(),
      statut_id: 'enAttente'
    };

    // Créer les données de la réparation
    const reparationData = {
      created_at: now,
      statut_id: 'planifiee',
      type_intervention_id: form.value.typeIntervention,
      voiture_id: voitureId
    };

    // Écrire dans Firebase (voiture et réparation)
    await Promise.all([
      set(dbRef(db, `voitures/${voitureId}`), voitureData),
      set(dbRef(db, `reparations/${reparationId}`), reparationData)
    ]);

    console.log('Voiture et réparation créées avec succès:', voitureId, reparationId);

    // Message de succès
    alert(
      `✅ Votre demande de réparation a été enregistrée !\\n\\nImmatriculation: ${form.value.immatriculation}\\nStatut: En attente`
    );

    // Retour à l'accueil
    await router.replace({ name: 'Acceuil' });
  } catch (error) {
    console.error('Erreur lors de l\'envoi:', error);
    alert('Une erreur est survenue. Veuillez réessayer.');
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Variables de thème */
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
  --success: #00cc00;
  --warning: #ff9900;
  --danger: #ff3333;
  --info: #0099ff;
  
  /* Ombres */
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.5);
  --shadow-red: 0 4px 20px rgba(255, 0, 0, 0.3);
  
  /* Bordures */
  --border-radius: 12px;
  --border-radius-lg: 20px;
  --border-radius-full: 50px;
  
  /* Compatibilité */
  --brand-orange: var(--primary);
  --brand-orange-dark: var(--primary-dark);
  --primary-black: var(--secondary);
  --primary-white: var(--accent);
  --gray-medium: #a7a7a7;
  --gray-light: #c0c0c0;
  --gray-dark: #1a1a1a;
  --primary-bg: var(--secondary);
  --card-bg: var(--secondary-lighter);
  --border-color: rgba(255, 255, 255, 0.1);
  --shadow-color: rgba(0, 0, 0, 0.3);
  --error-light: rgba(255, 51, 51, 0.1);
  --success-light: rgba(0, 204, 0, 0.1);
}

ion-page {
  background-color: var(--primary-bg);
}

ion-content {
  --background: var(--primary-bg);
  --padding-start: 20px;
  --padding-end: 20px;
  --padding-top: 24px;
  --padding-bottom: 24px;
}

ion-header {
  background: var(--card-bg);
  border-bottom: 1.5px solid var(--border-color);
}

ion-toolbar {
  --background: var(--card-bg);
  --color: var(--primary-black);
}

ion-title {
  font-weight: 700;
  font-size: 18px;
}

/* Conteneur du formulaire */
.form-container {
  max-width: 440px;
  margin: 0 auto;
}

/* En-tête du formulaire */
.form-header {
  text-align: center;
  padding: 0 0 28px;
  border-bottom: 2px solid var(--border-color);
  margin-bottom: 28px;
}

.form-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0 0 8px;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

.form-subtitle {
  font-size: 15px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 500;
}

/* Section du formulaire */
.form-section {
  margin-bottom: 28px;
}

/* Groupes de formulaire */
.form-group {
  margin-bottom: 24px;
  display: flex;
  flex-direction: column;
}

.form-group:last-of-type {
  margin-bottom: 28px;
}

/* Labels */
.form-label {
  font-size: 15px;
  font-weight: 700;
  color: var(--primary-black);
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.optional {
  font-size: 13px;
  font-weight: 500;
  color: var(--gray-light);
  font-style: italic;
}

.form-hint {
  font-size: 13px;
  color: var(--gray-medium);
  margin: 0 0 8px;
  font-weight: 400;
}

/* Inputs */
.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid var(--border-color);
  border-radius: 10px;
  font-size: 15px;
  font-family: inherit;
  color: var(--primary-black);
  background: var(--card-bg);
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: var(--gray-light);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: var(--brand-orange);
  background: var(--card-bg);
  box-shadow: 0 0 0 3px rgba(232, 80, 2, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.form-select {
  cursor: pointer;
  color: var(--gray-dark);
}

.form-select option {
  color: var(--primary-black);
  background: var(--card-bg);
}

/* Bouton d'envoi */
.submit-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
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
  padding: 0;
  text-transform: none;
  letter-spacing: 0.3px;
}

.submit-btn:not(:disabled):active {
  background: var(--brand-orange-dark);
  transform: translateY(1px);
  box-shadow: 0 2px 8px rgba(232, 80, 2, 0.35);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-icon {
  font-size: 20px;
}

/* Boîte d'information */
.info-box {
  background: var(--success-light);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  gap: 12px;
  align-items: flex-start;
  border-left: 4px solid #059669;
}

.info-icon {
  font-size: 24px;
  color: #059669;
  flex-shrink: 0;
  margin-top: 2px;
}

.info-box p {
  font-size: 14px;
  color: var(--gray-dark);
  margin: 0;
  line-height: 1.5;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 350px) {
  ion-content {
    --padding-start: 16px;
    --padding-end: 16px;
  }

  .form-title {
    font-size: 24px;
  }

  .form-subtitle {
    font-size: 14px;
  }

  .submit-btn {
    height: 44px;
    font-size: 15px;
  }

  .form-input,
  .form-textarea,
  .form-select {
    font-size: 16px; /* Empêche le zoom sur iOS */
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

.form-header {
  animation: fadeInUp 0.4s ease-out;
}

.form-group {
  animation: fadeInUp 0.4s ease-out backwards;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }

.submit-btn {
  animation: fadeInUp 0.4s ease-out 0.6s both;
}

.info-box {
  animation: fadeInUp 0.4s ease-out 0.7s both;
}
</style>
