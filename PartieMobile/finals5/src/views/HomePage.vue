<template>
  <ion-page>
    <ion-header :translucent="true">
      <!-- Header optionnel -->
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
            <div class="feature-icon-container">
              <ion-icon :icon="buildOutline" class="feature-icon"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Réparation automobile experte</h3>
              <p>Des mécaniciens qualifiés pour votre véhicule</p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon-container">
              <ion-icon :icon="speedometerOutline" class="feature-icon"></ion-icon>
            </div>
            <div class="feature-text">
              <h3>Rapidité et efficacité</h3>
              <p>Diagnostics précis et interventions rapides</p>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon-container">
              <ion-icon :icon="cardOutline" class="feature-icon"></ion-icon>
            </div>
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
              @click="allerInscription"
              class="primary-btn"
            >
              <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
              Créer un compte
            </ion-button>
            
            <ion-button 
              expand="block" 
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
              <div class="indicator-icon">
                <ion-icon :icon="shieldOutline"></ion-icon>
              </div>
              <span>Sécurisé</span>
            </div>
            <div class="indicator">
              <div class="indicator-icon">
                <ion-icon :icon="starOutline"></ion-icon>
              </div>
              <span>4.8/5</span>
            </div>
            <div class="indicator">
              <div class="indicator-icon">
                <ion-icon :icon="peopleOutline"></ion-icon>
              </div>
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
  console.log('HomePage: allerInscription clicked');
  router.push({ name: 'Register' }).catch(() => {});
};

const login = () => {
  console.log('HomePage: login clicked');
  router.push({ name: 'Login' }).catch(() => {});
};
</script>

<style scoped>
/* Variables de thème basées sur la palette orange/noir */
:root {
  --brand-orange: #E85002;
  --brand-orange-rgb: 232, 80, 2;
  --primary-black: #000000;
  --primary-white: #F9F9F9;
  --gray-medium: #646464;
  --gray-light: #A7A7A7;
  --gray-dark: #333333;
  --gradient-1: #000000;
  --gradient-2: #C10801;
  --gradient-3: #F16001;
  --gradient-4: #D9C3AB;
  
  /* Variables dérivées */
  --primary-bg: var(--primary-white);
  --card-bg: #FFFFFF;
  --primary-text: var(--primary-black);
  --secondary-text: var(--gray-dark);
  --border-color: rgba(0, 0, 0, 0.08);
  --shadow-color: rgba(0, 0, 0, 0.08);
}

/* Styles généraux */
ion-page {
  background-color: var(--primary-bg);
  color: var(--primary-text);
}

ion-content {
  --background: var(--primary-bg);
  --color: var(--primary-text);
  --padding-start: 20px;
  --padding-end: 20px;
  --padding-top: 24px;
  --padding-bottom: 24px;
}

/* Section principale */
.main-content {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 440px;
  margin: 0 auto;
}

/* Logo section */
.logo-section {
  text-align: center;
  padding: 16px 0 28px;
}

.logo-circle {
  width: 68px;
  height: 68px;
  background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  box-shadow: 0 6px 20px rgba(232, 80, 2, 0.25);
}

.logo-circle ion-icon {
  color: var(--primary-white);
  font-size: 30px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.app-title {
  font-size: 26px;
  font-weight: 800;
  margin: 0 0 6px;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

.app-subtitle {
  font-size: 15px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 500;
}

/* Message de bienvenue */
.welcome-message {
  text-align: center;
  margin-bottom: 32px;
  padding: 0 12px;
}

.welcome-message h2 {
  font-size: 22px;
  font-weight: 700;
  margin: 0 0 10px;
  color: var(--primary-black);
  background: linear-gradient(90deg, var(--gradient-1), var(--gradient-2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.welcome-message p {
  font-size: 15px;
  color: var(--gray-dark);
  margin: 0;
  line-height: 1.5;
  font-weight: 400;
}

/* Section des fonctionnalités */
.features-section {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-bottom: 36px;
}

.feature-item {
  background: var(--card-bg);
  border-radius: 14px;
  padding: 18px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--border-color);
  box-shadow: 0 4px 12px var(--shadow-color);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.feature-item:active {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.feature-icon-container {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.1), rgba(193, 8, 1, 0.1));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.feature-icon {
  font-size: 22px;
  color: var(--brand-orange);
}

.feature-text {
  flex: 1;
}

.feature-text h3 {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 6px;
  color: var(--primary-black);
  line-height: 1.3;
}

.feature-text p {
  font-size: 13.5px;
  color: var(--gray-medium);
  margin: 0;
  line-height: 1.4;
  font-weight: 400;
}

/* Section des actions */
.actions-section {
  margin-top: auto;
  padding-bottom: 20px;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 28px;
}

/* Bouton principal - Orange uni (Créer un compte) */
.primary-btn {
  --background: var(--brand-orange);
  --background-activated: #D14802; /* Version plus foncée pour le press */
  --background-focused: var(--brand-orange);
  --background-hover: var(--brand-orange);
  --border-radius: 12px;
  --padding-top: 14px;
  --padding-bottom: 14px;
  --box-shadow: 0 4px 12px rgba(232, 80, 2, 0.25);
  height: 46px;
  font-weight: 600;
  font-size: 15px;
  margin: 0;
  text-transform: none;
  letter-spacing: 0.3px;
  --color: var(--primary-white);
  max-width: 340px;
  margin-left: auto;
  margin-right: auto;
}

.primary-btn::part(native) {
  background: var(--brand-orange) !important;
  box-shadow: 0 4px 12px rgba(232, 80, 2, 0.25) !important;
}

.primary-btn:active::part(native) {
  background: #D14802 !important;
  box-shadow: 0 2px 8px rgba(232, 80, 2, 0.35) !important;
}

/* Bouton secondaire - Dégradé orange/noir (Se connecter) */
.secondary-btn {
  --background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
  --background-activated: linear-gradient(135deg, var(--gradient-1), var(--gradient-2));
  --background-focused: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
  --background-hover: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
  --border-radius: 12px;
  --padding-top: 14px;
  --padding-bottom: 14px;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  height: 46px;
  font-weight: 600;
  font-size: 15px;
  margin: 0;
  text-transform: none;
  letter-spacing: 0.3px;
  --color: var(--primary-white);
  max-width: 340px;
  margin-left: auto;
  margin-right: auto;
  border: none;
}

.secondary-btn::part(native) {
  background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3)) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.secondary-btn:active::part(native) {
  background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2)) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
}

/* Styles des icônes dans les boutons */
.primary-btn ion-icon,
.secondary-btn ion-icon {
  color: var(--primary-white) !important;
  margin-right: 8px;
}

/* Indicateurs de confiance */
.trust-indicators {
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 20px 0 0;
  border-top: 1px solid var(--border-color);
  max-width: 340px;
  margin: 0 auto;
}

.indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.indicator-icon {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.08), rgba(193, 8, 1, 0.08));
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.indicator-icon ion-icon {
  font-size: 18px;
  color: var(--brand-orange);
}

.indicator span {
  font-size: 11.5px;
  color: var(--gray-medium);
  font-weight: 600;
  letter-spacing: 0.3px;
}

/* Responsive pour très petits écrans */
@media (max-height: 650px) {
  .logo-section {
    padding: 12px 0 20px;
  }
  
  .features-section {
    margin-bottom: 28px;
  }
  
  .feature-item {
    padding: 16px;
  }
  
  .action-buttons {
    margin-bottom: 24px;
  }
  
  .primary-btn,
  .secondary-btn {
    height: 44px;
    --padding-top: 12px;
    --padding-bottom: 12px;
    max-width: 320px;
  }
}

/* Pour les écrans un peu plus larges (tablettes) */
@media (min-width: 768px) {
  .main-content {
    max-width: 400px;
    padding-top: 32px;
  }
  
  .logo-circle {
    width: 72px;
    height: 72px;
    border-radius: 20px;
  }
}

/* Safe area support pour iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(24px, env(safe-area-inset-bottom));
    --padding-top: max(24px, env(safe-area-inset-top));
  }
}

/* Ajustements orientation paysage */
@media (orientation: landscape) and (max-height: 500px) {
  .main-content {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    padding-top: 20px;
    max-width: 100%;
  }
  
  .logo-section {
    flex: 0 0 30%;
    text-align: left;
    padding: 0;
  }
  
  .welcome-message {
    flex: 0 0 65%;
    text-align: left;
    padding: 0;
    margin-bottom: 20px;
  }
  
  .features-section {
    order: 3;
    flex: 0 0 100%;
    flex-direction: row;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  
  .feature-item {
    flex: 1;
    flex-direction: column;
    text-align: center;
    padding: 16px 12px;
  }
  
  .feature-icon-container {
    margin-bottom: 10px;
  }
  
  .actions-section {
    order: 4;
    flex: 0 0 100%;
    margin-top: 20px;
  }
  
  .action-buttons {
    flex-direction: row;
    gap: 12px;
    max-width: 500px;
    margin: 0 auto 24px;
  }
  
  .primary-btn,
  .secondary-btn {
    flex: 1;
    max-width: none;
    height: 44px;
  }
  
  .trust-indicators {
    max-width: 400px;
  }
}

/* Pour les très petits écrans (iPhone SE, etc.) */
@media (max-width: 350px) {
  .primary-btn,
  .secondary-btn {
    max-width: 300px;
    height: 44px;
    font-size: 14.5px;
  }
  
  .trust-indicators {
    max-width: 300px;
  }
  
  .feature-item {
    padding: 16px 14px;
  }
}

/* Animation subtile pour les éléments */
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

.logo-section {
  animation: fadeInUp 0.5s ease-out;
}

.welcome-message {
  animation: fadeInUp 0.5s ease-out 0.1s both;
}

.features-section .feature-item:nth-child(1) {
  animation: fadeInUp 0.5s ease-out 0.2s both;
}

.features-section .feature-item:nth-child(2) {
  animation: fadeInUp 0.5s ease-out 0.3s both;
}

.features-section .feature-item:nth-child(3) {
  animation: fadeInUp 0.5s ease-out 0.4s both;
}

.actions-section {
  animation: fadeInUp 0.5s ease-out 0.5s both;
}
</style>