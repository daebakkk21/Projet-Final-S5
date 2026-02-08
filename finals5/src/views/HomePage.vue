<template>
  <ion-page>
    <ion-header :translucent="true">
      <!-- <ion-toolbar>
        <ion-title>AutoCare Pro</ion-title>
      </ion-toolbar> -->
    </ion-header>

    <ion-content :fullscreen="true" class="ion-padding">
      <!-- Section principale -->
      <div class="main-content">
        <!-- Logo et titre -->
        <div class="logo-section">
          <div class="logo-circle">
            <ion-icon :icon="carOutline" size="large"></ion-icon>
          </div>
          <h1 class="app-title">Garage Elite</h1>
          <p class="app-subtitle">Votre garage de confiance</p>
        </div>

        <!-- Messages d'accueil -->
        <div class="welcome-message">
          <h2>Bienvenue</h2>
          <p>Prenez soin de votre véhicule avec nos experts</p>
        </div>

        <!-- Cartes de présentation -->
        <div class="features-section">
          <div class="feature-item">
            <ion-icon :icon="buildOutline" class="feature-icon"></ion-icon>
            <div class="feature-text">
              <h3>Réparation automobile experte</h3>
              <p>Des mécaniciens qualifiés pour votre véhicule</p>
            </div>
          </div>

          <div class="feature-item">
            <ion-icon :icon="speedometerOutline" class="feature-icon"></ion-icon>
            <div class="feature-text">
              <h3>Rapidité et efficacité</h3>
              <p>Diagnostics précis et interventions rapides</p>
            </div>
          </div>

          <div class="feature-item">
            <ion-icon :icon="cardOutline" class="feature-icon"></ion-icon>
            <div class="feature-text">
              <h3>Transparence des prix</h3>
              <p>Devis détaillés validés avant intervention</p>
            </div>
          </div>
        </div>

        <!-- Boutons d'action -->
        <div class="actions-section">
          <div class="action-buttons">
            <ion-button 
              expand="block" 
              color="success" 
              @click="allerInscription"
              class="primary-btn"
            >
              <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
              Créer un compte
            </ion-button>
            
            <ion-button 
              expand="block" 
              color="medium"
              @click="login"
              class="secondary-btn"
            >
              <ion-icon :icon="logInOutline" slot="start"></ion-icon>
              Se connecter
            </ion-button>
          </div>

          <!-- Indicateurs de confiance -->
          <div class="trust-indicators">
            <div class="indicator">
              <ion-icon :icon="shieldOutline" color="success"></ion-icon>
              <span>Sécurisé</span>
            </div>
            <div class="indicator">
              <ion-icon :icon="starOutline" color="warning"></ion-icon>
              <span>4.8/5</span>
            </div>
            <div class="indicator">
              <ion-icon :icon="peopleOutline" color="tertiary"></ion-icon>
              <span>5000+</span>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import {
  IonPage,
  IonHeader,
  IonContent,
  IonToolbar,
  IonTitle,
  IonButton,
  IonIcon
} from '@ionic/vue';
import { useRouter } from 'vue-router';
import {
  carOutline,
  buildOutline,
  speedometerOutline,
  cardOutline,
  personAddOutline,
  logInOutline,
  shieldOutline,
  starOutline,
  peopleOutline
} from 'ionicons/icons';

const router = useRouter();

const allerInscription = () => {
  router.push('/register');
};

const login = () => {
  router.push('/login');
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
}

/* Styles généraux */
ion-page {
  background-color: var(--primary-bg);
  color: var(--primary-text);
}

ion-header {
  background-color: var(--secondary-bg);
}

ion-toolbar {
  --background: var(--secondary-bg);
  --color: var(--primary-text);
  --min-height: 56px;
}

ion-content {
  --background: var(--primary-bg);
  --color: var(--primary-text);
  --padding-start: 20px;
  --padding-end: 20px;
  --padding-top: 16px;
  --padding-bottom: 20px;
}

/* Section principale */
.main-content {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px); /* Hauteur totale moins le header */
  max-width: 500px;
  margin: 0 auto;
  padding: 0 8px;
}

/* Logo section */
.logo-section {
  text-align: center;
  padding: 20px 0 30px;
}

.logo-circle {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, var(--accent-color), #1DB954);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  box-shadow: 0 4px 16px rgba(35, 206, 107, 0.25);
}

.logo-circle ion-icon {
  color: white;
  font-size: 32px;
}

.app-title {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 4px;
  color: white;
}

.app-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
  font-weight: 400;
}

/* Message de bienvenue */
.welcome-message {
  text-align: center;
  margin-bottom: 30px;
  padding: 0 8px;
}

.welcome-message h2 {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px;
  color: white;
}

.welcome-message p {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
  line-height: 1.4;
}

/* Section des fonctionnalités */
.features-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 40px;
  padding: 0 8px;
}

.feature-item {
  background: var(--card-bg);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: flex-start;
  gap: 16px;
  border: 1px solid var(--border-color);
}

.feature-icon {
  font-size: 28px;
  color: var(--accent-color);
  flex-shrink: 0;
  margin-top: 2px;
}

.feature-text h3 {
  font-size: 17px;
  font-weight: 600;
  margin: 0 0 6px;
  color: white;
  line-height: 1.3;
}

.feature-text p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  line-height: 1.4;
}

/* Section des actions */
.actions-section {
  margin-top: auto; /* Pousse vers le bas */
  padding: 0 8px 20px;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.primary-btn {
  --background: var(--accent-color);
  --background-activated: #1DB954;
  --border-radius: 10px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  margin: 0;
}

.secondary-btn {
  --background: #374151;
  --background-activated: #4B5563;
  --border-radius: 10px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  margin: 0;
}

/* Indicateurs de confiance */
.trust-indicators {
  display: flex;
  justify-content: center;
  gap: 24px;
  padding-top: 16px;
  border-top: 1px solid var(--border-color);
}

.indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.indicator ion-icon {
  font-size: 20px;
}

.indicator span {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

/* Responsive pour très petits écrans */
@media (max-height: 650px) {
  .logo-section {
    padding: 10px 0 20px;
  }
  
  .features-section {
    margin-bottom: 30px;
  }
  
  .feature-item {
    padding: 16px;
  }
  
  .action-buttons {
    margin-bottom: 20px;
  }
}

/* Pour les écrans un peu plus larges (tablettes) */
@media (min-width: 768px) {
  .main-content {
    max-width: 400px;
    padding-top: 40px;
  }
}

/* Safe area support pour iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
  }
  
  .main-content {
    padding-left: max(8px, env(safe-area-inset-left));
    padding-right: max(8px, env(safe-area-inset-right));
  }
}

/* Ajustements orientation paysage */
@media (orientation: landscape) and (max-height: 500px) {
  .main-content {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
  }
  
  .logo-section,
  .welcome-message,
  .features-section,
  .actions-section {
    flex: 1;
    min-width: 45%;
    max-width: 45%;
    margin-bottom: 20px;
  }
  
  .features-section {
    order: 3;
    max-width: 100%;
  }
  
  .actions-section {
    order: 4;
    max-width: 100%;
  }
}
</style>