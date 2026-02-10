<<<<<<< HEAD
<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button defaultHref="/home" text="Retour"></ion-back-button>
        </ion-buttons>
        <ion-title>Connexion</ion-title>
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
          <div class="buttons-container">
            <ion-button 
              expand="block" 
              @click="login" 
              :disabled="isLoading"
              color="success"
              class="primary-btn"
            >
              <span v-if="!isLoading">Se connecter</span>
              <ion-spinner v-else name="crescent"></ion-spinner>
            </ion-button>

            <ion-button 
              fill="clear" 
              routerLink="/register" 
              routerDirection="forward" 
              @click="allerInscription"
              class="secondary-link"
            >
              <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
              Créer un compte
            </ion-button>

            <ion-button 
              fill="clear" 
              routerLink="/home" 
              routerDirection="back"
              class="home-link"
            >
              <ion-icon :icon="homeOutline" slot="start"></ion-icon>
              Retour à l'accueil
            </ion-button>
          </div>
        </div>

        <!-- Aide -->
        <div class="help-section">
          <ion-text color="medium">
            <small>Besoin d'aide ? Contactez-nous au support@autocare.fr</small>
          </ion-text>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { signInWithEmailAndPassword } from "firebase/auth";
import { auth } from "@/firebase";
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
  carSportOutline
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

const login = async () => {
  error.value = "";
  isLoading.value = true;

  // Force active element blur to ensure ion-input commits latest value
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
    const userCredential = await signInWithEmailAndPassword(auth, effectiveEmail, effectivePassword);
    await router.replace({ name: 'Acceuil' });
  } catch (e: any) {
    console.error("Login error:", e);
    if (e.code === 'auth/invalid-email') {
      error.value = 'Email invalide.';
    } else if (e.code === 'auth/user-not-found') {
      error.value = 'Aucun compte trouvé pour cet email.';
    } else if (e.code === 'auth/wrong-password') {
      error.value = 'Mot de passe incorrect.';
    } else if (e.code === 'auth/too-many-requests') {
      error.value = 'Trop de tentatives. Réessayer plus tard.';
    } else {
      error.value = e.message || 'Erreur lors de la connexion.';
    }
  } finally {
    isLoading.value = false;
  }
};

const allerInscription = async () => {
  try {
    await router.push({ name: 'Register' });
  } catch (err) {
    console.error('Navigation to /register failed:', err);
    router.push('/register').catch(e => console.error('Fallback push failed:', e));
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
  --input-bg: #1E2633;
  --border-color: #2D3748;
  --error-color: #FF6B6B;
  --success-color: #23CE6B;
}

/* Conteneur principal */
.login-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 400px;
  margin: 0 auto;
  padding: 0 8px;
}

/* En-tête */
.login-header {
  text-align: center;
  padding: 40px 0 30px;
}

.logo-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, var(--accent-color), #1DB954);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.logo-icon ion-icon {
  color: white;
  font-size: 28px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px;
  color: white;
}

.welcome-subtitle {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
  line-height: 1.4;
}

/* Section formulaire */
.form-section {
  flex: 1;
  padding: 0 8px;
}

.input-group {
  margin-bottom: 24px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 8px;
}

.custom-input {
  --background: var(--input-bg);
  --border-radius: 12px;
  --padding-start: 16px;
  --padding-end: 16px;
  --padding-top: 16px;
  --padding-bottom: 16px;
  --placeholder-color: rgba(255, 255, 255, 0.4);
  --color: white;
  border: 1px solid var(--border-color);
  font-size: 16px;
}

.custom-input.has-error {
  border-color: var(--error-color);
}

.custom-input ion-icon[slot="start"] {
  color: var(--accent-color);
  margin-right: 12px;
  font-size: 20px;
}

/* Message d'erreur */
.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.2);
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 24px;
  animation: fadeIn 0.3s ease;
}

.error-message ion-icon {
  color: var(--error-color);
  font-size: 20px;
  flex-shrink: 0;
}

.error-message span {
  color: var(--error-color);
  font-size: 14px;
  line-height: 1.4;
}

/* Conteneur boutons (identique à HomePage) */
.buttons-container {
  width: 100%;
  max-width: 400px;
  margin: 40px auto 0 auto;
  padding: 0 16px;
  box-sizing: border-box;
  text-align: center;
}

/* Bouton principal (identique à HomePage) */
.primary-btn {
  --background: var(--accent-color);
  --background-activated: #1DB954;
  --background-hover: #1DB954;
  --color: var(--primary-text);
  --border-radius: 12px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  font-weight: 600;
  font-size: 16px;
  margin: 0 auto;
  width: 100%;
  max-width: 280px;
  box-shadow: 0 4px 12px rgba(35, 206, 107, 0.3);
  display: block;
}

.primary-btn:disabled {
  opacity: 0.5;
}

/* Boutons secondaires */
.secondary-link {
  --color: var(--accent-color);
  font-weight: 600;
  font-size: 15px;
  margin-top: 16px;
  text-transform: none;
}

.secondary-link ion-icon {
  font-size: 18px;
}

.home-link {
  --color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
  margin-top: 8px;
  text-transform: none;
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
}

/* Responsive */
@media (max-height: 650px) {
  .login-header {
    padding: 20px 0 20px;
  }
  
  .logo-icon {
    width: 56px;
    height: 56px;
    margin-bottom: 16px;
  }
  
  .welcome-title {
    font-size: 22px;
  }
}

/* Support safe area iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
  }
  
  .login-container {
    padding-left: max(8px, env(safe-area-inset-left));
    padding-right: max(8px, env(safe-area-inset-right));
  }
  
  .buttons-container {
    padding-left: max(16px, env(safe-area-inset-left));
    padding-right: max(16px, env(safe-area-inset-right));
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

/* Ajustement pour les champs focus */
.custom-input:focus-within {
  border-color: var(--accent-color);
}
=======
<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button defaultHref="/home" text="Retour"></ion-back-button>
        </ion-buttons>
        <ion-title>Connexion</ion-title>
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
          <div class="buttons-container">
            <ion-button 
              expand="block" 
              @click="login" 
              :disabled="isLoading"
              color="success"
              class="primary-btn"
            >
              <span v-if="!isLoading">Se connecter</span>
              <ion-spinner v-else name="crescent"></ion-spinner>
            </ion-button>

            <ion-button 
              fill="clear" 
              routerLink="/register" 
              routerDirection="forward" 
              @click="allerInscription"
              class="secondary-link"
            >
              <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
              Créer un compte
            </ion-button>

            <ion-button 
              fill="clear" 
              routerLink="/home" 
              routerDirection="back"
              class="home-link"
            >
              <ion-icon :icon="homeOutline" slot="start"></ion-icon>
              Retour à l'accueil
            </ion-button>
          </div>
        </div>

        <!-- Aide -->
        <div class="help-section">
          <ion-text color="medium">
            <small>Besoin d'aide ? Contactez-nous au support@autocare.fr</small>
          </ion-text>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { signInWithEmailAndPassword } from "firebase/auth";
import { auth } from "@/firebase";
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
  carSportOutline
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

const login = async () => {
  error.value = "";
  isLoading.value = true;

  // Force active element blur to ensure ion-input commits latest value
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
    const userCredential = await signInWithEmailAndPassword(auth, effectiveEmail, effectivePassword);
    await router.replace({ name: 'Acceuil' });
  } catch (e: any) {
    console.error("Login error:", e);
    if (e.code === 'auth/invalid-email') {
      error.value = 'Email invalide.';
    } else if (e.code === 'auth/user-not-found') {
      error.value = 'Aucun compte trouvé pour cet email.';
    } else if (e.code === 'auth/wrong-password') {
      error.value = 'Mot de passe incorrect.';
    } else if (e.code === 'auth/too-many-requests') {
      error.value = 'Trop de tentatives. Réessayer plus tard.';
    } else {
      error.value = e.message || 'Erreur lors de la connexion.';
    }
  } finally {
    isLoading.value = false;
  }
};

const allerInscription = async () => {
  try {
    await router.push({ name: 'Register' });
  } catch (err) {
    console.error('Navigation to /register failed:', err);
    router.push('/register').catch(e => console.error('Fallback push failed:', e));
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
  --input-bg: #1E2633;
  --border-color: #2D3748;
  --error-color: #FF6B6B;
  --success-color: #23CE6B;
}

/* Conteneur principal */
.login-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 400px;
  margin: 0 auto;
  padding: 0 8px;
}

/* En-tête */
.login-header {
  text-align: center;
  padding: 40px 0 30px;
}

.logo-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, var(--accent-color), #1DB954);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.logo-icon ion-icon {
  color: white;
  font-size: 28px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px;
  color: white;
}

.welcome-subtitle {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
  line-height: 1.4;
}

/* Section formulaire */
.form-section {
  flex: 1;
  padding: 0 8px;
}

.input-group {
  margin-bottom: 24px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 8px;
}

.custom-input {
  --background: var(--input-bg);
  --border-radius: 12px;
  --padding-start: 16px;
  --padding-end: 16px;
  --padding-top: 16px;
  --padding-bottom: 16px;
  --placeholder-color: rgba(255, 255, 255, 0.4);
  --color: white;
  border: 1px solid var(--border-color);
  font-size: 16px;
}

.custom-input.has-error {
  border-color: var(--error-color);
}

.custom-input ion-icon[slot="start"] {
  color: var(--accent-color);
  margin-right: 12px;
  font-size: 20px;
}

/* Message d'erreur */
.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.2);
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 24px;
  animation: fadeIn 0.3s ease;
}

.error-message ion-icon {
  color: var(--error-color);
  font-size: 20px;
  flex-shrink: 0;
}

.error-message span {
  color: var(--error-color);
  font-size: 14px;
  line-height: 1.4;
}

/* Conteneur boutons (identique à HomePage) */
.buttons-container {
  width: 100%;
  max-width: 400px;
  margin: 40px auto 0 auto;
  padding: 0 16px;
  box-sizing: border-box;
  text-align: center;
}

/* Bouton principal (identique à HomePage) */
.primary-btn {
  --background: var(--accent-color);
  --background-activated: #1DB954;
  --background-hover: #1DB954;
  --color: var(--primary-text);
  --border-radius: 12px;
  --padding-top: 18px;
  --padding-bottom: 18px;
  font-weight: 600;
  font-size: 16px;
  margin: 0 auto;
  width: 100%;
  max-width: 280px;
  box-shadow: 0 4px 12px rgba(35, 206, 107, 0.3);
  display: block;
}

.primary-btn:disabled {
  opacity: 0.5;
}

/* Boutons secondaires */
.secondary-link {
  --color: var(--accent-color);
  font-weight: 600;
  font-size: 15px;
  margin-top: 16px;
  text-transform: none;
}

.secondary-link ion-icon {
  font-size: 18px;
}

.home-link {
  --color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
  margin-top: 8px;
  text-transform: none;
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
}

/* Responsive */
@media (max-height: 650px) {
  .login-header {
    padding: 20px 0 20px;
  }
  
  .logo-icon {
    width: 56px;
    height: 56px;
    margin-bottom: 16px;
  }
  
  .welcome-title {
    font-size: 22px;
  }
}

/* Support safe area iPhone */
@supports (padding: max(0px)) {
  ion-content {
    --padding-start: max(20px, env(safe-area-inset-left));
    --padding-end: max(20px, env(safe-area-inset-right));
    --padding-bottom: max(20px, env(safe-area-inset-bottom));
  }
  
  .login-container {
    padding-left: max(8px, env(safe-area-inset-left));
    padding-right: max(8px, env(safe-area-inset-right));
  }
  
  .buttons-container {
    padding-left: max(16px, env(safe-area-inset-left));
    padding-right: max(16px, env(safe-area-inset-right));
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

/* Ajustement pour les champs focus */
.custom-input:focus-within {
  border-color: var(--accent-color);
}
>>>>>>> 934ad8d6 (Add files via upload)
</style>