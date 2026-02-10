<template>
  <ion-page>
    <ion-header>
      <ion-toolbar class="custom-toolbar">
        <ion-buttons slot="start">
          <ion-back-button defaultHref="/home" text="Retour" class="custom-back-button"></ion-back-button>
        </ion-buttons>
        <ion-title class="page-title">Inscription</ion-title>
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

          <!-- Champ Nom -->
          <div class="input-group">
            <ion-label class="input-label">Nom</ion-label>
            <ion-input
              ref="nomInput"
              type="text"
              :value="nom"
              @ionInput="onNomInput"
              @input="onNomInput"
              @change="onNomInput"
              placeholder="Votre nom"
              class="custom-input"
              :class="{ 'has-success': nom && nom.trim().length > 0 }"
            >
              <ion-icon :icon="personCircleOutline" slot="start"></ion-icon>
            </ion-input>
            
            <div v-if="nom && nom.trim().length > 0" class="success-message">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Nom renseigné</span>
            </div>
          </div>

          <!-- Champ Prenom -->
          <div class="input-group">
            <ion-label class="input-label">Prénom</ion-label>
            <ion-input
              ref="prenomInput"
              type="text"
              :value="prenom"
              @ionInput="onPrenomInput"
              @input="onPrenomInput"
              @change="onPrenomInput"
              placeholder="Votre prénom"
              class="custom-input"
              :class="{ 'has-success': prenom && prenom.trim().length > 0 }"
            >
              <ion-icon :icon="personCircleOutline" slot="start"></ion-icon>
            </ion-input>
            
            <div v-if="prenom && prenom.trim().length > 0" class="success-message">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
              <span>Prénom renseigné</span>
            </div>
          </div>

          <!-- Champ Mot de passe (sera utilisé comme ID du client) -->
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
            <ion-checkbox v-model="acceptTerms" :checked="acceptTerms" class="terms-checkbox"></ion-checkbox>
            <ion-label class="terms-label">
              J'accepte les 
              <a href="#" class="terms-link" @click.prevent="showTerms">conditions d'utilisation</a>
              et la 
              <a href="#" class="terms-link" @click.prevent="showPrivacy">politique de confidentialité</a>
            </ion-label>
          </div>

          <!-- Boutons -->
          <div class="actions-section">
            <div class="action-buttons">
              <ion-button 
                expand="block" 
                @click="register" 
                :disabled="!isFormValid || !acceptTerms"
                class="primary-btn"
              >
                <ion-icon :icon="personAddOutline" slot="start"></ion-icon>
                Créer mon compte
              </ion-button>
              
              <ion-button 
                expand="block" 
                @click="goToLogin"
                class="secondary-btn"
              >
                <ion-icon :icon="logInOutline" slot="start"></ion-icon>
                Se connecter
              </ion-button>
            </div>
          </div>

          <!-- Avantages -->
          <div class="benefits-section">
            <h3 class="benefits-title">Avantages d'un compte Garage Elite</h3>
            <div class="benefits-list">
              <div class="benefit-item">
                <div class="benefit-icon">
                  <ion-icon :icon="calendarOutline"></ion-icon>
                </div>
                <span>Prendre rendez-vous en ligne</span>
              </div>
              <div class="benefit-item">
                <div class="benefit-icon">
                  <ion-icon :icon="documentTextOutline"></ion-icon>
                </div>
                <span>Historique des réparations</span>
              </div>
              <div class="benefit-item">
                <div class="benefit-icon">
                  <ion-icon :icon="notificationsOutline"></ion-icon>
                </div>
                <span>Alertes d'entretien</span>
              </div>
              <div class="benefit-item">
                <div class="benefit-icon">
                  <ion-icon :icon="starOutline"></ion-icon>
                </div>
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
import { useRouter } from "vue-router";
import { db } from "@/firebase";
import { ref as dbRef, set } from "firebase/database";
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonButtons,
  IonBackButton,
  IonTitle,
  IonContent,
  IonInput,
  IonLabel,
  IonIcon,
  IonCheckbox,
  IonButton
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

const router = useRouter();
const email = ref("");
const nom = ref("");
const prenom = ref("");
const password = ref("");
const acceptTerms = ref(false);

const isValidEmail = (e: string) => /^\S+@\S+\.\S+$/.test(e);

const isEmailValid = computed(() => isValidEmail((email.value || "").trim()));
const isNomValid = computed(() => (nom.value || "").trim().length > 0);
const isPrenomValid = computed(() => (prenom.value || "").trim().length > 0);
const isFormValid = computed(() => isEmailValid.value && isNomValid.value && isPrenomValid.value && password.value && password.value.length >= 6);

const emailInput = ref<HTMLElement | null>(null);
const nomInput = ref<HTMLElement | null>(null);
const prenomInput = ref<HTMLElement | null>(null);
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
  email.value = val || native;
};

const onNomInput = (ev: any) => {
  const val = ev?.detail?.value ?? ev?.target?.value ?? "";
  const native = readNativeValue(nomInput.value) ?? "";
  nom.value = val || native;
};

const onPrenomInput = (ev: any) => {
  const val = ev?.detail?.value ?? ev?.target?.value ?? "";
  const native = readNativeValue(prenomInput.value) ?? "";
  prenom.value = val || native;
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

const goToLogin = () => {
  router.push('/login');
};

const register = async () => {
  if (typeof document !== 'undefined' && document.activeElement instanceof HTMLElement) {
    document.activeElement.blur();
  }

  const emailStr = (email.value || "").trim();

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

  const safeEmail = emailStr.normalize ? emailStr.normalize('NFC') : emailStr;
  const nomStr = (nom.value || "").trim();
  const prenomStr = (prenom.value || "").trim();
  const clientId = password.value; // Le mot de passe EST l'ID du client

  if (!nomStr || !prenomStr) {
    alert("Veuillez saisir votre nom et prénom.");
    return;
  }

  try {
    const clientData = {
      email: safeEmail,
      nom: nomStr,
      prenom: prenomStr,
      created_at: new Date().toISOString(),
    };

    await set(dbRef(db, `clients/${clientId}`), clientData);

    // Connecter automatiquement l'utilisateur après inscription
    const connectedClient = {
      id: clientId,
      ...clientData,
    };
    localStorage.setItem('client', JSON.stringify(connectedClient));

    alert(`🎉 Compte créé avec succès !\n\nBienvenue sur Garage Elite, ${prenomStr} !`);
    router.replace({ name: 'Acceuil' });
  } catch (e: any) {
    console.error('RTDB write error:', e);
    alert(`❌ Erreur lors de la création du compte : ${e?.message || e}`);
  }
};
</script>

<style scoped>
/* Variables de thème basées sur la palette orange/noir */
:root {
  --brand-orange: #E85002;
  --brand-orange-dark: #C10801;
  --brand-orange-light: #F16001;
  --primary-black: #000000;
  --primary-white: #F9F9F9;
  --gray-medium: #646464;
  --gray-light: #A7A7A7;
  --gray-dark: #333333;
  
  /* Variables dérivées */
  --primary-bg: var(--primary-white);
  --card-bg: #FFFFFF;
  --input-bg: #FFFFFF;
  --primary-text: var(--primary-black);
  --secondary-text: var(--gray-dark);
  --border-color: rgba(0, 0, 0, 0.12);
  --shadow-color: rgba(0, 0, 0, 0.08);
  --error-color: #DC2626;
  --success-color: #059669;
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
.register-container {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 56px);
  max-width: 440px;
  margin: 0 auto;
}

/* En-tête */
.register-header {
  text-align: center;
  padding: 16px 0 32px;
}

.logo-icon {
  width: 68px;
  height: 68px;
  background: linear-gradient(135deg, var(--primary-black), var(--brand-orange-dark), var(--brand-orange-light));
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 6px 20px rgba(232, 80, 2, 0.25);
}

.logo-icon ion-icon {
  color: var(--primary-white);
  font-size: 34px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.register-title {
  font-size: 26px;
  font-weight: 800;
  margin: 0 0 8px;
  color: var(--primary-black);
  letter-spacing: -0.5px;
}

.register-subtitle {
  font-size: 15px;
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

.custom-input.has-success {
  border-color: var(--success-color);
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.15);
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

/* Messages d'erreur et succès */
.error-message,
.success-message {
  display: flex;
  align-items: center;
  gap: 10px;
  border-radius: 10px;
  padding: 12px 14px;
  margin-top: 10px;
  animation: fadeIn 0.3s ease;
  font-size: 14px;
  font-weight: 500;
}

.error-message {
  background: rgba(220, 38, 38, 0.08);
  border: 1px solid rgba(220, 38, 38, 0.2);
  color: var(--error-color);
}

.success-message {
  background: rgba(5, 150, 105, 0.08);
  border: 1px solid rgba(5, 150, 105, 0.2);
  color: var(--success-color);
}

.error-message ion-icon,
.success-message ion-icon {
  font-size: 18px;
  flex-shrink: 0;
}

/* Indications mot de passe */
.password-hint {
  margin-top: 10px;
}

.hint-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 6px;
}

.hint-item.valid span {
  color: var(--success-color);
  font-weight: 500;
}

.hint-item:not(.valid) span {
  color: var(--gray-medium);
}

.hint-item ion-icon {
  font-size: 16px;
}

.hint-item.valid ion-icon {
  color: var(--success-color);
}

.hint-item:not(.valid) ion-icon {
  color: var(--gray-light);
}

.hint-item span {
  font-size: 14px;
}

/* Conditions d'utilisation */
.terms-section {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin: 32px 0 36px;
  padding: 20px;
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.03), rgba(193, 8, 1, 0.03));
  border-radius: 14px;
  border: 1.5px solid var(--border-color);
}

.terms-checkbox {
  --size: 20px;
  --background: var(--input-bg);
  --background-checked: var(--brand-orange);
  --border-color: var(--gray-light);
  --border-color-checked: var(--brand-orange);
  --checkmark-color: var(--primary-white);
  --border-radius: 6px;
  margin-top: 2px;
}

.terms-label {
  font-size: 14px;
  color: var(--gray-dark);
  line-height: 1.5;
  font-weight: 400;
}

.terms-link {
  color: var(--brand-orange);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.terms-link:active {
  color: var(--brand-orange-dark);
  text-decoration: underline;
}

/* Section des actions (EXACTEMENT COMME LA PAGE D'ACCUEIL) */
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

/* Bouton principal - Orange uni (Créer mon compte) - EXACTEMENT COMME PAGE D'ACCUEIL */
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

/* Bouton secondaire - Dégradé orange/noir (Se connecter) - EXACTEMENT COMME PAGE D'ACCUEIL */
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

/* Styles des icônes dans les boutons */
.primary-btn ion-icon,
.secondary-btn ion-icon {
  color: var(--primary-white) !important;
  margin-right: 8px;
}

/* Section avantages */
.benefits-section {
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.05), rgba(193, 8, 1, 0.05));
  border-radius: 16px;
  padding: 24px 20px;
  margin-top: 24px;
  border: 1.5px solid rgba(232, 80, 2, 0.15);
}

.benefits-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--primary-black);
  margin: 0 0 20px;
  text-align: center;
  position: relative;
  padding-bottom: 12px;
}

.benefits-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, var(--brand-orange-dark), var(--brand-orange-light));
  border-radius: 2px;
}

.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.benefit-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, rgba(232, 80, 2, 0.1), rgba(193, 8, 1, 0.1));
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.benefit-icon ion-icon {
  font-size: 20px;
  color: var(--brand-orange);
}

.benefit-item span {
  font-size: 15px;
  color: var(--gray-dark);
  font-weight: 500;
  line-height: 1.4;
}

/* Responsive - EXACTEMENT COMME PAGE D'ACCUEIL */
@media (max-height: 650px) {
  .register-header {
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

@media (max-height: 700px) {
  .logo-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 16px;
  }
  
  .logo-icon ion-icon {
    font-size: 30px;
  }
  
  .register-title {
    font-size: 24px;
  }
  
  .benefits-section {
    padding: 20px 16px;
  }
}

@media (min-width: 768px) {
  .register-container {
    max-width: 400px;
    padding-top: 20px;
  }
}

/* Pour les très petits écrans (iPhone SE, etc.) - EXACTEMENT COMME PAGE D'ACCUEIL */
@media (max-width: 350px) {
  .primary-btn,
  .secondary-btn {
    max-width: 300px;
    height: 44px;
    font-size: 14.5px;
  }
  
  ion-content {
    --padding-start: 16px;
    --padding-end: 16px;
  }
  
  .terms-section {
    padding: 16px;
  }
  
  .benefits-section {
    padding: 20px 16px;
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
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>