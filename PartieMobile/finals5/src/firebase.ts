import { initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";

const firebaseConfig = {
	databaseURL: "https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app",
	// Note: For messaging to work, you need the full Firebase config with apiKey, authDomain, projectId, etc.
	// Uncomment and add all required fields if you need cloud messaging
};

const app = initializeApp(firebaseConfig);

export const db = getDatabase(app);

// NOTE: We don't initialize Firebase Auth here because the project
// uses a custom authentication flow based on Realtime Database.
// Initializing `getAuth()` without a full firebaseConfig (apiKey, etc.)
// causes `auth/invalid-api-key` errors in the browser. If you need
// Firebase Auth later, provide the full config (apiKey, authDomain,...).

export default app;