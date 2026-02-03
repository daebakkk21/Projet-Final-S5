<<<<<<< HEAD
<template>
  <ion-page>
    <!-- <ion-header>
      <ion-toolbar>
        <ion-title>Mon Espace</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="logout" :disabled="isLoggingOut">
            <ion-icon :icon="logOutOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header> -->

    <ion-content class="ion-padding">
      <div class="welcome-container">
        <!-- Section de bienvenue -->
        <div class="welcome-section">
          <div class="user-avatar">
            <ion-icon :icon="personCircleOutline"></ion-icon>
          </div>
          <h1 class="welcome-title">Bienvenue sur Garage Elite</h1>
          <p class="welcome-subtitle">Votre espace de gestion automobile</p>
        </div>

        <!-- Statistiques rapides -->
        <!-- <div class="stats-section">
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(35, 206, 107, 0.1);">
              <ion-icon :icon="carSportOutline" color="success"></ion-icon>
            </div>
            <div class="stat-info">
              <h3>Véhicule actuel</h3>
              <p>Renault Clio</p>
            </div>
          </div> -->
          
          <!-- <div class="stat-card">
            <div class="stat-icon" style="background: rgba(66, 153, 225, 0.1);">
              <ion-icon :icon="calendarOutline" color="primary"></ion-icon>
            </div>
            <div class="stat-info">
              <h3>Prochain RDV</h3>
              <p>15 Novembre</p>
            </div>
          </div>
        </div> -->

        <!-- Services rapides -->
        <div class="services-section">
          <h2 class="section-title">Services disponibles</h2>
          <p class="section-subtitle">Gérez votre véhicule en quelques clics</p>

          <div class="services-grid">
            <div class="service-card" @click="navigateTo('appointment')">
              <div class="service-icon">
                <ion-icon :icon="calendarNumberOutline" color="success"></ion-icon>
              </div>
              <h3>Prendre RDV</h3>
              <p>Planifiez une réparation ou entretien</p>
            </div>

            <div class="service-card" @click="navigateTo('history')">
              <div class="service-icon">
                <ion-icon :icon="timeOutline" color="warning"></ion-icon>
              </div>
              <h3>Historique</h3>
              <p>Consultez vos interventions passées</p>
            </div>

            <div class="service-card" @click="navigateTo('documents')">
              <div class="service-icon">
                <ion-icon :icon="documentTextOutline" color="tertiary"></ion-icon>
              </div>
              <h3>Documents</h3>
              <p>Factures et garanties en ligne</p>
            </div>

            <div class="service-card" @click="navigateTo('emergency')">
              <div class="service-icon">
                <ion-icon :icon="warningOutline" color="danger"></ion-icon>
              </div>
              <h3>Urgence</h3>
              <p>Assistance 24h/24</p>
            </div>
          </div>
        </div>

        <!-- Dernières interventions
        <div class="recent-section">
          <div class="section-header">
            <h2 class="section-title">Dernières interventions</h2>
            <ion-button fill="clear" size="small">Voir tout</ion-button>
          </div>

          <div class="intervention-list">
            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Vidange complète</h4>
                <p>12 Octobre 2024 • 150€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>

            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Changement pneus</h4>
                <p>5 Septembre 2024 • 320€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>

            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Contrôle technique</h4>
                <p>20 Août 2024 • 75€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>
          </div>
        </div> -->

        <!-- Conseils d'entretien -->
        <div class="tips-section">
          <h2 class="section-title">Conseils du moment</h2>
          
          <div class="tip-card">
            <ion-icon :icon="snowOutline" color="primary" slot="start"></ion-icon>
            <div class="tip-content">
              <h4>Préparer l'hiver</h4>
              <p>Vérifiez vos pneus et liquides avant les températures froides</p>
            </div>
          </div>

          <div class="tip-card">
            <ion-icon :icon="speedometerOutline" color="warning" slot="start"></ion-icon>
            <div class="tip-content">
              <h4>Économiser du carburant</h4>
              <p>Maintenez une pression correcte des pneus pour réduire la consommation</p>
            </div>
          </div>
        </div>

        <!-- Bouton de déconnexion principal -->
        <div class="logout-section">
          <ion-button 
            expand="block" 
            color="medium"
            @click="logout" 
            :disabled="isLoggingOut"
            class="logout-btn"
          >
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            <span v-if="!isLoggingOut">Se déconnecter</span>
            <ion-spinner v-else name="dots"></ion-spinner>
          </ion-button>
          
          <p class="logout-note">Dernière connexion : Aujourd'hui, 14:30</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { auth } from "@/firebase";
import { signOut } from "firebase/auth";
import { useRouter } from "vue-router";
import { 
  IonPage, 
  IonHeader, 
  IonContent, 
  IonToolbar, 
  IonTitle, 
  IonButton,
  IonSpinner,
  IonIcon,
  IonButtons,
  IonBadge
} from '@ionic/vue';
import {
  personCircleOutline,
  carSportOutline,
  calendarOutline,
  calendarNumberOutline,
  timeOutline,
  documentTextOutline,
  warningOutline,
  logOutOutline,
  snowOutline,
  speedometerOutline
} from 'ionicons/icons';

const router = useRouter();
const isLoggingOut = ref(false);

const logout = async () => {
  isLoggingOut.value = true;
  try {
    await signOut(auth);
    await router.replace({ name: 'Login' });
  } catch (e) {
    console.error('Sign out failed:', e);
  } finally {
    isLoggingOut.value = false;
  }
};

const navigateTo = (page: string) => {
  // Pour l'instant, on montre une alerte
  // À terme, cela naviguera vers les différentes pages
  switch(page) {
    case 'appointment':
      alert("Fonctionnalité 'Prise de RDV' bientôt disponible !");
      break;
    case 'history':
      alert("Fonctionnalité 'Historique' bientôt disponible !");
      break;
    case 'documents':
      alert("Fonctionnalité 'Documents' bientôt disponible !");
      break;
    case 'emergency':
      alert("Assistance technique : 01 23 45 67 89");
      break;
  }
};
</script>

<style scoped>
/* Variables de thème */
:root {
  --primary-bg: #11161E;
  --accent-color: #23CE6B;
  --primary-text: #FFFFFF;
  --secondary-bg: #19212C;
  --card-bg: #1A222E;
  --border-color: #2D3748;
  --success-color: #23CE6B;
  --warning-color: #FFA726;
  --danger-color: #FF6B6B;
  --info-color: #4299E1;
}

/* Conteneur principal */
.welcome-container {
  max-width: 500px;
  margin: 0 auto;
  padding-bottom: 40px;
}

/* Section de bienvenue */
.welcome-section {
  text-align: center;
  padding: 20px 0 30px;
}

.user-avatar {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #4F46E5, #7C3AED);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.user-avatar ion-icon {
  color: white;
  font-size: 40px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px;
  color: white;
}

.welcome-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

/* Section statistiques */
.stats-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid var(--border-color);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon ion-icon {
  font-size: 24px;
}

.stat-info h3 {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 4px;
  font-weight: 500;
}

.stat-info p {
  font-size: 16px;
  color: white;
  margin: 0;
  font-weight: 600;
}

/* Section services */
.services-section {
  margin-bottom: 30px;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: white;
  margin: 0 0 8px;
}

.section-subtitle {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 20px;
}

.services-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.service-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  border: 1px solid var(--border-color);
  transition: transform 0.2s ease, border-color 0.2s ease;
  cursor: pointer;
}

.service-card:active {
  transform: scale(0.98);
  border-color: var(--accent-color);
}

.service-icon {
  width: 56px;
  height: 56px;
  background: rgba(35, 206, 107, 0.1);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.service-card:nth-child(2) .service-icon {
  background: rgba(255, 167, 38, 0.1);
}

.service-card:nth-child(3) .service-icon {
  background: rgba(148, 100, 255, 0.1);
}

.service-card:nth-child(4) .service-icon {
  background: rgba(255, 107, 107, 0.1);
}

.service-icon ion-icon {
  font-size: 28px;
}

.service-card h3 {
  font-size: 16px;
  color: white;
  margin: 0 0 6px;
  font-weight: 600;
}

.service-card p {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.4;
}

/* Section récentes */
.recent-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.intervention-list {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.intervention-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid var(--border-color);
}

.intervention-item:last-child {
  border-bottom: none;
}

.intervention-info h4 {
  font-size: 15px;
  color: white;
  margin: 0 0 4px;
  font-weight: 600;
}

.intervention-info p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
}

/* Section conseils */
.tips-section {
  margin-bottom: 30px;
}

.tip-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
  border: 1px solid var(--border-color);
}

.tip-card:last-child {
  margin-bottom: 0;
}

.tip-card ion-icon {
  font-size: 24px;
  margin-top: 2px;
  flex-shrink: 0;
}

.tip-content h4 {
  font-size: 15px;
  color: white;
  margin: 0 0 6px;
  font-weight: 600;
}

.tip-content p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.4;
}

/* Section déconnexion */
.logout-section {
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.logout-btn {
  --background: #374151;
  --background-activated: #4B5563;
  --border-radius: 12px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  margin-bottom: 12px;
}

.logout-note {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  text-align: center;
  margin: 0;
}

/* Responsive */
@media (max-height: 700px) {
  .welcome-section {
    padding: 10px 0 20px;
  }
  
  .user-avatar {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
  }
  
  .welcome-title {
    font-size: 22px;
  }
  
  .services-grid {
    gap: 10px;
  }
  
  .service-card {
    padding: 16px;
  }
}

/* Support safe area iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
  }
  
  .welcome-container {
    padding-left: max(8px, env(safe-area-inset-left));
    padding-right: max(8px, env(safe-area-inset-right));
  }
}
=======
<template>
  <ion-page>
    <!-- <ion-header>
      <ion-toolbar>
        <ion-title>Mon Espace</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="logout" :disabled="isLoggingOut">
            <ion-icon :icon="logOutOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header> -->

    <ion-content class="ion-padding">
      <div class="welcome-container">
        <!-- Section de bienvenue -->
        <div class="welcome-section">
          <div class="user-avatar">
            <ion-icon :icon="personCircleOutline"></ion-icon>
          </div>
          <h1 class="welcome-title">Bienvenue sur Garage Elite</h1>
          <p class="welcome-subtitle">Votre espace de gestion automobile</p>
        </div>

        <!-- Statistiques rapides -->
        <!-- <div class="stats-section">
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(35, 206, 107, 0.1);">
              <ion-icon :icon="carSportOutline" color="success"></ion-icon>
            </div>
            <div class="stat-info">
              <h3>Véhicule actuel</h3>
              <p>Renault Clio</p>
            </div>
          </div> -->
          
          <!-- <div class="stat-card">
            <div class="stat-icon" style="background: rgba(66, 153, 225, 0.1);">
              <ion-icon :icon="calendarOutline" color="primary"></ion-icon>
            </div>
            <div class="stat-info">
              <h3>Prochain RDV</h3>
              <p>15 Novembre</p>
            </div>
          </div>
        </div> -->

        <!-- Services rapides -->
        <div class="services-section">
          <h2 class="section-title">Services disponibles</h2>
          <p class="section-subtitle">Gérez votre véhicule en quelques clics</p>

          <div class="services-grid">
            <div class="service-card" @click="navigateTo('appointment')">
              <div class="service-icon">
                <ion-icon :icon="calendarNumberOutline" color="success"></ion-icon>
              </div>
              <h3>Prendre RDV</h3>
              <p>Planifiez une réparation ou entretien</p>
            </div>

            <div class="service-card" @click="navigateTo('history')">
              <div class="service-icon">
                <ion-icon :icon="timeOutline" color="warning"></ion-icon>
              </div>
              <h3>Historique</h3>
              <p>Consultez vos interventions passées</p>
            </div>

            <div class="service-card" @click="navigateTo('documents')">
              <div class="service-icon">
                <ion-icon :icon="documentTextOutline" color="tertiary"></ion-icon>
              </div>
              <h3>Documents</h3>
              <p>Factures et garanties en ligne</p>
            </div>

            <div class="service-card" @click="navigateTo('emergency')">
              <div class="service-icon">
                <ion-icon :icon="warningOutline" color="danger"></ion-icon>
              </div>
              <h3>Urgence</h3>
              <p>Assistance 24h/24</p>
            </div>
          </div>
        </div>

        <!-- Dernières interventions
        <div class="recent-section">
          <div class="section-header">
            <h2 class="section-title">Dernières interventions</h2>
            <ion-button fill="clear" size="small">Voir tout</ion-button>
          </div>

          <div class="intervention-list">
            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Vidange complète</h4>
                <p>12 Octobre 2024 • 150€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>

            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Changement pneus</h4>
                <p>5 Septembre 2024 • 320€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>

            <div class="intervention-item">
              <div class="intervention-info">
                <h4>Contrôle technique</h4>
                <p>20 Août 2024 • 75€</p>
              </div>
              <ion-badge color="success">Terminé</ion-badge>
            </div>
          </div>
        </div> -->

        <!-- Conseils d'entretien -->
        <div class="tips-section">
          <h2 class="section-title">Conseils du moment</h2>
          
          <div class="tip-card">
            <ion-icon :icon="snowOutline" color="primary" slot="start"></ion-icon>
            <div class="tip-content">
              <h4>Préparer l'hiver</h4>
              <p>Vérifiez vos pneus et liquides avant les températures froides</p>
            </div>
          </div>

          <div class="tip-card">
            <ion-icon :icon="speedometerOutline" color="warning" slot="start"></ion-icon>
            <div class="tip-content">
              <h4>Économiser du carburant</h4>
              <p>Maintenez une pression correcte des pneus pour réduire la consommation</p>
            </div>
          </div>
        </div>

        <!-- Bouton de déconnexion principal -->
        <div class="logout-section">
          <ion-button 
            expand="block" 
            color="medium"
            @click="logout" 
            :disabled="isLoggingOut"
            class="logout-btn"
          >
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            <span v-if="!isLoggingOut">Se déconnecter</span>
            <ion-spinner v-else name="dots"></ion-spinner>
          </ion-button>
          
          <p class="logout-note">Dernière connexion : Aujourd'hui, 14:30</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { auth } from "@/firebase";
import { signOut } from "firebase/auth";
import { useRouter } from "vue-router";
import { 
  IonPage, 
  IonHeader, 
  IonContent, 
  IonToolbar, 
  IonTitle, 
  IonButton,
  IonSpinner,
  IonIcon,
  IonButtons,
  IonBadge
} from '@ionic/vue';
import {
  personCircleOutline,
  carSportOutline,
  calendarOutline,
  calendarNumberOutline,
  timeOutline,
  documentTextOutline,
  warningOutline,
  logOutOutline,
  snowOutline,
  speedometerOutline
} from 'ionicons/icons';

const router = useRouter();
const isLoggingOut = ref(false);

const logout = async () => {
  isLoggingOut.value = true;
  try {
    await signOut(auth);
    await router.replace({ name: 'Login' });
  } catch (e) {
    console.error('Sign out failed:', e);
  } finally {
    isLoggingOut.value = false;
  }
};

const navigateTo = (page: string) => {
  // Pour l'instant, on montre une alerte
  // À terme, cela naviguera vers les différentes pages
  switch(page) {
    case 'appointment':
      alert("Fonctionnalité 'Prise de RDV' bientôt disponible !");
      break;
    case 'history':
      alert("Fonctionnalité 'Historique' bientôt disponible !");
      break;
    case 'documents':
      alert("Fonctionnalité 'Documents' bientôt disponible !");
      break;
    case 'emergency':
      alert("Assistance technique : 01 23 45 67 89");
      break;
  }
};
</script>

<style scoped>
/* Variables de thème */
:root {
  --primary-bg: #11161E;
  --accent-color: #23CE6B;
  --primary-text: #FFFFFF;
  --secondary-bg: #19212C;
  --card-bg: #1A222E;
  --border-color: #2D3748;
  --success-color: #23CE6B;
  --warning-color: #FFA726;
  --danger-color: #FF6B6B;
  --info-color: #4299E1;
}

/* Conteneur principal */
.welcome-container {
  max-width: 500px;
  margin: 0 auto;
  padding-bottom: 40px;
}

/* Section de bienvenue */
.welcome-section {
  text-align: center;
  padding: 20px 0 30px;
}

.user-avatar {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #4F46E5, #7C3AED);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.user-avatar ion-icon {
  color: white;
  font-size: 40px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px;
  color: white;
}

.welcome-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

/* Section statistiques */
.stats-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid var(--border-color);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon ion-icon {
  font-size: 24px;
}

.stat-info h3 {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 4px;
  font-weight: 500;
}

.stat-info p {
  font-size: 16px;
  color: white;
  margin: 0;
  font-weight: 600;
}

/* Section services */
.services-section {
  margin-bottom: 30px;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: white;
  margin: 0 0 8px;
}

.section-subtitle {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 20px;
}

.services-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.service-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  border: 1px solid var(--border-color);
  transition: transform 0.2s ease, border-color 0.2s ease;
  cursor: pointer;
}

.service-card:active {
  transform: scale(0.98);
  border-color: var(--accent-color);
}

.service-icon {
  width: 56px;
  height: 56px;
  background: rgba(35, 206, 107, 0.1);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.service-card:nth-child(2) .service-icon {
  background: rgba(255, 167, 38, 0.1);
}

.service-card:nth-child(3) .service-icon {
  background: rgba(148, 100, 255, 0.1);
}

.service-card:nth-child(4) .service-icon {
  background: rgba(255, 107, 107, 0.1);
}

.service-icon ion-icon {
  font-size: 28px;
}

.service-card h3 {
  font-size: 16px;
  color: white;
  margin: 0 0 6px;
  font-weight: 600;
}

.service-card p {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.4;
}

/* Section récentes */
.recent-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.intervention-list {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.intervention-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid var(--border-color);
}

.intervention-item:last-child {
  border-bottom: none;
}

.intervention-info h4 {
  font-size: 15px;
  color: white;
  margin: 0 0 4px;
  font-weight: 600;
}

.intervention-info p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
}

/* Section conseils */
.tips-section {
  margin-bottom: 30px;
}

.tip-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
  border: 1px solid var(--border-color);
}

.tip-card:last-child {
  margin-bottom: 0;
}

.tip-card ion-icon {
  font-size: 24px;
  margin-top: 2px;
  flex-shrink: 0;
}

.tip-content h4 {
  font-size: 15px;
  color: white;
  margin: 0 0 6px;
  font-weight: 600;
}

.tip-content p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.4;
}

/* Section déconnexion */
.logout-section {
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.logout-btn {
  --background: #374151;
  --background-activated: #4B5563;
  --border-radius: 12px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  margin-bottom: 12px;
}

.logout-note {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  text-align: center;
  margin: 0;
}

/* Responsive */
@media (max-height: 700px) {
  .welcome-section {
    padding: 10px 0 20px;
  }
  
  .user-avatar {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
  }
  
  .welcome-title {
    font-size: 22px;
  }
  
  .services-grid {
    gap: 10px;
  }
  
  .service-card {
    padding: 16px;
  }
}

/* Support safe area iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
  }
  
  .welcome-container {
    padding-left: max(8px, env(safe-area-inset-left));
    padding-right: max(8px, env(safe-area-inset-right));
  }
}
>>>>>>> 934ad8d6 (Add files via upload)
</style>