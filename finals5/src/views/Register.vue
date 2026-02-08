<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button defaultHref="/home" text="Retour"></ion-back-button>
        </ion-buttons>
        <ion-title>Inscription</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="register-container">
        <!-- En-tête -->
        <div class="register-header">
          <div class="logo-icon">
            <ion-icon :icon="personCircleOutline"></ion-icon>
          </div>
          <h1 class="register-title">Rejoignez-nous</h1>
          <p class="register-subtitle">Créez votre compte Garage Elite</p>
        </div>

        <!-- Formulaire d'inscription -->
        <div class="form-section">
          <!-- Champ Email -->
          <div class="input-group">
            <ion-label class="input-label">Adresse email</ion-label>
            <ion-input
              ref="emailInput"
              type="email"
              :value="email"
              @ionInput="onEmailInput"
              @input="onEmailInput"
              @change="onEmailInput"
              placeholder="exemple@email.com"
              class="custom-input"
              :class="{ 'has-error': email && !isEmailValid, 'has-success': email && isEmailValid }"
            >
              <ion-icon :icon="mailOutline" slot="start"></ion-icon>
            </ion-input>
            
            <div v-if="email && !isEmailValid" class="error-message">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>Veuillez saisir une adresse email valide</span>
            </div>
            
            <div v-if="email && isEmailValid" class="success-message">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Format d'email valide</span>
            </div>
          </div>

          <!-- Champ Mot de passe -->
          <div class="input-group">
            <ion-label class="input-label">Mot de passe</ion-label>
            <ion-input
              ref="passwordInput"
              type="password"
              :value="password"
              @ionInput="onPasswordInput"
              @input="onPasswordInput"
              @change="onPasswordInput"
              placeholder="Minimum 6 caractères"
              class="custom-input"
              :class="{ 'has-error': password && password.length < 6, 'has-success': password && password.length >= 6 }"
            >
              <ion-icon :icon="lockClosedOutline" slot="start"></ion-icon>
            </ion-input>
            
            <div class="password-hint">
              <div class="hint-item" :class="{ 'valid': password.length >= 6 }">
                <ion-icon :icon="password.length >= 6 ? checkmarkCircleOutline : ellipseOutline"></ion-icon>
                <span>Au moins 6 caractères</span>
              </div>
            </div>
            
            <div v-if="password && password.length < 6" class="error-message">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>Le mot de passe doit contenir au moins 6 caractères</span>
            </div>
            
            <div v-if="password && password.length >= 6" class="success-message">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Mot de passe valide</span>
            </div>
          </div>

          <!-- Conditions d'utilisation -->
          <div class="terms-section">
            <ion-checkbox v-model="acceptTerms" :checked="acceptTerms"></ion-checkbox>
            <ion-label class="terms-label">
              J'accepte les 
              <a href="#" class="terms-link" @click.prevent="showTerms">conditions d'utilisation</a>
              et la 
              <a href="#" class="terms-link" @click.prevent="showPrivacy">politique de confidentialité</a>
            </ion-label>
          </div>

          <!-- Boutons -->
          <div class="buttons-container">
            <ion-button 
              expand="block" 
              @click="register" 
              :disabled="!isFormValid || !acceptTerms"
              color="success"
              class="primary-btn"
            >
              <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
              Créer mon compte
            </ion-button>

            <ion-button 
              fill="clear" 
              routerLink="/login" 
              routerDirection="forward"
              class="secondary-link"
            >
              <ion-icon :icon="logInOutline" slot="start"></ion-icon>
              Se connecter
            </ion-button>
          </div>

          <!-- Avantages -->
          <div class="benefits-section">
            <h3 class="benefits-title">Avantages d'un compte Garage Elite</h3>
            <div class="benefits-list">
              <div class="benefit-item">
                <ion-icon :icon="calendarOutline" color="success"></ion-icon>
                <span>Prendre rendez-vous en ligne</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="documentTextOutline" color="success"></ion-icon>
                <span>Historique des réparations</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="notificationsOutline" color="success"></ion-icon>
                <span>Alertes d'entretien</span>
              </div>
              <div class="benefit-item">
                <ion-icon :icon="starOutline" color="success"></ion-icon>
                <span>Points fidélité</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { createUserWithEmailAndPassword } from "firebase/auth";
import { auth } from "@/firebase";
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
  IonLabel,
  IonIcon,
  IonCheckbox
} from '@ionic/vue';
import {
  mailOutline,
  lockClosedOutline,
  personAddOutline,
  logInOutline,
  alertCircleOutline,
  checkmarkCircleOutline,
  ellipseOutline,
  personCircleOutline,
  calendarOutline,
  documentTextOutline,
  notificationsOutline,
  starOutline
} from 'ionicons/icons';

const email = ref("");
const password = ref("");
const acceptTerms = ref(false);

const isValidEmail = (e: string) => /^\S+@\S+\.\S+$/.test(e);

const isEmailValid = computed(() => isValidEmail((email.value || "").trim()));
const isFormValid = computed(() => isEmailValid.value && password.value && password.value.length >= 6);

const emailInput = ref<HTMLElement | null>(null);
const passwordInput = ref<HTMLElement | null>(null);

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
  // prefer event value, fallback to native input read if available
  email.value = val || native;
};

const onPasswordInput = (ev: any) => {
  const val = ev?.detail?.value ?? ev?.target?.value ?? "";
  const native = readNativeValue(passwordInput.value) ?? "";
  password.value = val || native;
};

const showTerms = () => {
  alert("Conditions d'utilisation\n\nEn créant un compte, vous acceptez nos conditions d'utilisation...");
};

const showPrivacy = () => {
  alert("Politique de confidentialité\n\nNous protégeons vos données personnelles...");
};

const register = async () => {
  // Force active element blur to ensure ion-input commits latest value
  if (typeof document !== 'undefined' && document.activeElement instanceof HTMLElement) {
    document.activeElement.blur();
  }

  const emailStr = (email.value || "").trim();
  console.log("EMAIL SENT TO FIREBASE 👉", emailStr);

  // Inline validation shown in the UI; keep guard here for safety
  if (!emailStr || !isEmailValid.value) {
    console.warn('Register prevented: invalid email according to client check:', emailStr);
    return;
  }

  if (!password.value || password.value.length < 6) {
    console.warn('Register prevented: short password');
    return;
  }

  if (!acceptTerms.value) {
    alert("Veuillez accepter les conditions d'utilisation pour continuer.");
    return;
  }

  // Normalize to NFC to avoid unicode quirks
  const safeEmail = emailStr.normalize ? emailStr.normalize('NFC') : emailStr;

  try {
    await createUserWithEmailAndPassword(auth, safeEmail, password.value);
    alert("🎉 Compte créé avec succès !\n\nVous pouvez maintenant vous connecter avec vos identifiants.");
  } catch (e: any) {
    console.error('Firebase error code/message:', e.code, e.message);
    if (e.code === "auth/invalid-email") {
      alert("❌ Email invalide.\nVeuillez vérifier le format de l'email et réessayer.");
    } else if (e.code === "auth/email-already-in-use") {
      alert("⚠️ Cet email est déjà utilisé.\nEssayez de vous connecter ou utilisez un autre email.");
    } else if (e.code === "auth/weak-password") {
      alert("🔒 Mot de passe trop faible.\nVeuillez choisir un mot de passe plus fort.");
    } else {
      alert(`❌ Erreur : ${e.message}`);
    }
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
  --warning-color: #FFA726;
}

/* Conteneur principal */
.register-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 400px;
  margin: 0 auto;
  padding: 0 8px;
}

/* En-tête */
.register-header {
  text-align: center;
  padding: 30px 0 25px;
}

.logo-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #4F46E5, #7C3AED);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.logo-icon ion-icon {
  color: white;
  font-size: 32px;
}

.register-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 6px;
  color: white;
}

.register-subtitle {
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
  margin-bottom: 20px;
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
  transition: border-color 0.2s ease;
}

.custom-input.has-error {
  border-color: var(--error-color);
}

.custom-input.has-success {
  border-color: var(--success-color);
}

.custom-input ion-icon[slot="start"] {
  color: var(--accent-color);
  margin-right: 12px;
  font-size: 20px;
}

/* Messages d'erreur et succès */
.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.2);
  border-radius: 8px;
  padding: 10px 12px;
  margin-top: 8px;
  animation: fadeIn 0.3s ease;
}

.success-message {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(35, 206, 107, 0.1);
  border: 1px solid rgba(35, 206, 107, 0.2);
  border-radius: 8px;
  padding: 10px 12px;
  margin-top: 8px;
  animation: fadeIn 0.3s ease;
}

.error-message ion-icon,
.success-message ion-icon {
  font-size: 18px;
  flex-shrink: 0;
}

.error-message ion-icon {
  color: var(--error-color);
}

.success-message ion-icon {
  color: var(--success-color);
}

.error-message span,
.success-message span {
  font-size: 13px;
  line-height: 1.4;
}

.error-message span {
  color: var(--error-color);
}

.success-message span {
  color: var(--success-color);
}

/* Indications mot de passe */
.password-hint {
  margin-top: 8px;
}

.hint-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.hint-item.valid span {
  color: var(--success-color);
}

.hint-item:not(.valid) span {
  color: rgba(255, 255, 255, 0.5);
}

.hint-item ion-icon {
  font-size: 16px;
}

.hint-item.valid ion-icon {
  color: var(--success-color);
}

.hint-item:not(.valid) ion-icon {
  color: rgba(255, 255, 255, 0.3);
}

.hint-item span {
  font-size: 13px;
}

/* Conditions d'utilisation */
.terms-section {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin: 24px 0 28px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
  border: 1px solid var(--border-color);
}

ion-checkbox {
  --background: var(--input-bg);
  --background-checked: var(--accent-color);
  --border-color: var(--border-color);
  --border-color-checked: var(--accent-color);
  --checkmark-color: white;
  margin-top: 2px;
}

.terms-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.5;
}

.terms-link {
  color: var(--accent-color);
  text-decoration: none;
  font-weight: 500;
}

.terms-link:hover {
  text-decoration: underline;
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

/* Section avantages */
.benefits-section {
  background: rgba(35, 206, 107, 0.05);
  border-radius: 16px;
  padding: 20px;
  margin-top: 20px;
  border: 1px solid rgba(35, 206, 107, 0.1);
}

.benefits-title {
  font-size: 16px;
  font-weight: 600;
  color: white;
  margin: 0 0 16px;
  text-align: center;
}

.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.benefit-item ion-icon {
  font-size: 20px;
  flex-shrink: 0;
}

.benefit-item span {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.8);
}

/* Responsive */
@media (max-height: 700px) {
  .register-header {
    padding: 20px 0 20px;
  }
  
  .logo-icon {
    width: 56px;
    height: 56px;
    margin-bottom: 12px;
  }
  
  .register-title {
    font-size: 22px;
  }
  
  .benefits-section {
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
  
  .register-container {
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
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus states */
.custom-input:focus-within {
  border-color: var(--accent-color);
}

/* Style pour les liens désactivés */
a {
  cursor: pointer;
}
</style>