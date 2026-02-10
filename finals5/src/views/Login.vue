<template>
  <ion-page>
    <ion-header>
      <ion-toolbar class="custom-toolbar">
        <ion-buttons slot="start">
          <ion-back-button defaultHref="/home" text="Retour" class="custom-back-button"></ion-back-button>
        </ion-buttons>
        <ion-title class="page-title">Connexion</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="login-container">
        <!-- En-tête -->
        <div class="login-header">
          <div class="logo-icon">
            <ion-icon :icon="carSportOutline"></ion-icon>
          </div>
          <h1 class="welcome-title">Bon retour !</h1>
          <p class="welcome-subtitle">Connectez-vous à votre compte Garage Elite</p>
        </div>

        <!-- Formulaire -->
        <div class="form-section">
          <div class="input-group">
            <ion-label class="input-label">Adresse email</ion-label>
            <ion-input 
              ref="emailInput" 
              v-model:value="email" 
              type="email" 
              @ionInput="onEmailInput"
              @input="onEmailInput" 
              placeholder="exemple@email.com"
              class="custom-input"
              :class="{ 'has-error': error && error.includes('email') }"
            >
              <ion-icon :icon="mailOutline" slot="start"></ion-icon>
            </ion-input>
          </div>

          <div class="input-group">
            <ion-label class="input-label">Mot de passe</ion-label>
            <ion-input 
              ref="passwordInput" 
              v-model:value="password" 
              type="password" 
              @ionInput="onPasswordInput"
              @input="onPasswordInput" 
              placeholder="Votre mot de passe"
              class="custom-input"
              :class="{ 'has-error': error && error.includes('password') }"
            >
              <ion-icon :icon="lockClosedOutline" slot="start"></ion-icon>
            </ion-input>
          </div>

          <!-- Message d'erreur -->
          <div v-if="error" class="error-message">
            <ion-icon :icon="alertCircleOutline"></ion-icon>
            <span>{{ error }}</span>
          </div>

          <!-- Boutons -->
          <div class="actions-section">
            <div class="action-buttons">
              <ion-button 
                expand="block" 
                @click="login" 
                :disabled="isLoading"
                class="primary-btn"
              >
                <span v-if="!isLoading">
                  <ion-icon :icon="logInOutline" slot="start"></ion-icon>
                  Se connecter
                </span>
                <ion-spinner v-else name="crescent"></ion-spinner>
              </ion-button>

              <ion-button 
                expand="block"
                @click="goToRegister"
                class="secondary-btn"
              >
                <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
                Créer un compte
              </ion-button>

              <button 
                @click="goToHome"
                class="home-link"
              >
                <ion-icon :icon="homeOutline"></ion-icon>
                Retour à l'accueil
              </button>
            </div>
          </div>
        </div>

        <!-- Aide -->
        <div class="help-section">
          <ion-text color="medium">
            <small>Besoin d'aide ? Contactez-nous au support@garageelite.fr</small>
          </ion-text>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { db } from '../firebase'
// import { db } from "@/firebase";
import { ref as dbRef, get } from "firebase/database";
import { useRouter } from "vue-router";
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonButtons,
  IonBackButton,
  IonTitle,
  IonContent,
  IonInput,
  IonText,
  IonButton,
  IonSpinner,
  IonLabel,
  IonIcon
} from '@ionic/vue';
import {
  mailOutline,
  lockClosedOutline,
  personAddOutline,
  homeOutline,
  alertCircleOutline,
  carSportOutline,
  logInOutline
} from 'ionicons/icons';

const email = ref("");
const password = ref("");
const isLoading = ref(false);
const error = ref("");
const emailInput = ref<HTMLElement | null>(null);
const passwordInput = ref<HTMLElement | null>(null);
const router = useRouter();

const readNativeValue = (el: any) => {
  if (!el) return null;
  try {
    const shadow = (el as HTMLElement).shadowRoot;
    const native = (shadow && shadow.querySelector('input')) || (el.querySelector && el.querySelector('input')) || null;
    return native ? native.value : null;
  } catch (err) {
    return null;
  }
};

const onEmailInput = (ev: any) => {
  const val = ev?.detail?.value ?? ev?.target?.value ?? "";
  const native = readNativeValue(emailInput.value) ?? "";
  email.value = val || native;
};

const onPasswordInput = (ev: any) => {
  const val = ev?.detail?.value ?? ev?.target?.value ?? "";
  const native = readNativeValue(passwordInput.value) ?? "";
  password.value = val || native;
};

const goToRegister = () => {
  router.push('/register');
};

const goToHome = () => {
  router.push('/home');
};

const login = async () => {
  error.value = "";
  isLoading.value = true;

  if (typeof document !== 'undefined' && document.activeElement instanceof HTMLElement) {
    document.activeElement.blur();
  }

  const effectiveEmail = (email.value || readNativeValue(emailInput.value) || "").trim();
  const effectivePassword = password.value || readNativeValue(passwordInput.value) || "";

  if (!effectiveEmail || !effectivePassword) {
    error.value = "Veuillez saisir l'email et le mot de passe.";
    isLoading.value = false;
    return;
  }

  try {
    const snapshot = await get(dbRef(db, "clients"));

    if (!snapshot.exists()) {
      error.value = "Aucun client trouvé";
      isLoading.value = false;
      return;
    }

    let connectedClient: any = null;

    snapshot.forEach((child) => {
      const clientId = child.key as string;
      const client = child.val();

      if (client && client.email === effectiveEmail && effectivePassword === clientId) {
        connectedClient = {
          id: clientId,
          ...client,
        };
      }
    });

    if (connectedClient) {
      localStorage.setItem("client", JSON.stringify(connectedClient));
      console.log('Login: connectedClient', connectedClient);
      // rediriger vers la page d'accueil nommée 'Acceuil'
      router.replace({ name: 'Acceuil' }).catch(() => {});
    } else {
      error.value = "Email ou mot de passe incorrect";
    }
  } catch (e: any) {
    console.error("Login (RTDB) error:", e);
    error.value = e?.message || "Erreur lors de la connexion.";
  } finally {
    isLoading.value = false;
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
  --primary-bg: var(--secondary);
  --card-bg: var(--secondary-lighter);
  --input-bg: var(--secondary-light);
  --primary-text: var(--accent);
  --secondary-text: var(--accent-dark);
  --border-color: rgba(255, 255, 255, 0.1);
  --shadow-color: rgba(0, 0, 0, 0.3);
  --error-color: #ff3333;
  --success-color: #00cc00;
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
  --padding-top: 20px;
  --padding-bottom: 20px;
}

/* Toolbar personnalisé */
.custom-toolbar {
  --background: var(--primary-white);
  --border-color: var(--border-color);
  --color: var(--primary-black);
  border-bottom: 1px solid var(--border-color);
}

.custom-back-button {
  --color: var(--brand-orange);
  --color-hover: var(--brand-orange-dark);
  font-weight: 500;
  font-size: 16px;
}

.page-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--primary-black);
}

/* Conteneur principal */
.login-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 440px;
  margin: 0 auto;
}

/* En-tête */
.login-header {
  text-align: center;
  padding: 24px 0 32px;
}

.logo-icon {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light));
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 8px 24px rgba(232, 80, 2, 0.25);
}

.logo-icon ion-icon {
  color: var(--primary-white);
  font-size: 36px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.welcome-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0 0 8px;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

.welcome-subtitle {
  font-size: 16px;
  color: var(--gray-medium);
  margin: 0;
  font-weight: 500;
  line-height: 1.4;
}

/* Section formulaire */
.form-section {
  flex: 1;
}

.input-group {
  margin-bottom: 24px;
}

.input-label {
  display: block;
  font-size: 15px;
  font-weight: 600;
  color: var(--gray-dark);
  margin-bottom: 10px;
}

.custom-input {
  --background: var(--input-bg);
  --border-radius: 12px;
  --padding-start: 16px;
  --padding-end: 16px;
  --padding-top: 16px;
  --padding-bottom: 16px;
  --placeholder-color: var(--gray-light);
  --color: var(--primary-black);
  border: 1.5px solid var(--border-color);
  font-size: 16px;
  transition: all 0.2s ease;
  background: var(--input-bg);
  box-shadow: 0 2px 8px var(--shadow-color);
}

.custom-input.has-error {
  border-color: var(--error-color);
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
}

.custom-input:focus-within {
  border-color: var(--brand-orange);
  box-shadow: 0 4px 12px rgba(232, 80, 2, 0.2);
}

.custom-input ion-icon[slot="start"] {
  color: var(--brand-orange);
  margin-right: 12px;
  font-size: 20px;
}

/* Message d'erreur */
.error-message {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(220, 38, 38, 0.08);
  border: 1px solid rgba(220, 38, 38, 0.2);
  border-radius: 10px;
  padding: 12px 14px;
  margin-top: 20px;
  margin-bottom: 24px;
  animation: fadeIn 0.3s ease;
  font-size: 14px;
  font-weight: 500;
  color: var(--error-color);
}

.error-message ion-icon {
  font-size: 18px;
  flex-shrink: 0;
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

/* CORRECTION : BOUTON PRINCIPAL - Orange uni (Se connecter) */
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

/* CORRECTION : BOUTON SECONDAIRE - Dégradé orange/noir (Créer un compte) */
.secondary-btn {
  --background: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light));
  --background-activated: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark));
  --background-focused: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light));
  --background-hover: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light));
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
  background: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light)) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.secondary-btn:active::part(native) {
  background: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark)) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Styles des icônes dans les boutons */
.primary-btn ion-icon,
.secondary-btn ion-icon {
  color: var(--primary-white) !important;
  margin-right: 8px;
}

/* Lien Retour à l'accueil */
.home-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  max-width: 340px;
  height: 40px;
  margin: 8px auto 0;
  background: transparent;
  border: none;
  color: var(--gray-medium);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.2s ease;
}

.home-link:active {
  color: var(--brand-orange);
}

.home-link ion-icon {
  font-size: 16px;
}

/* Section aide */
.help-section {
  text-align: center;
  padding: 20px 0 30px;
  border-top: 1px solid var(--border-color);
  margin-top: 20px;
}

.help-section small {
  font-size: 12px;
  color: var(--gray-medium);
}

/* Responsive */
@media (max-height: 650px) {
  .login-header {
    padding: 16px 0 24px;
  }
  
  .logo-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
  }
  
  .logo-icon ion-icon {
    font-size: 32px;
  }
  
  .welcome-title {
    font-size: 24px;
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

@media (min-width: 768px) {
  .login-container {
    max-width: 400px;
    padding-top: 20px;
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
  
  .home-link {
    max-width: 300px;
  }
  
  ion-content {
    --padding-start: 16px;
    --padding-end: 16px;
  }
}

/* Safe area support pour iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
    --padding-top: max(20px, env(safe-area-inset-top));
  }
}

/* Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>