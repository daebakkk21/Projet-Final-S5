import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

const firebaseConfig = {
  apiKey: "AIzaSyBOxGQcSAKOcZEztFxjdaZ4thzOWHzbhbg",
  authDomain: "login-finals5.firebaseapp.com",
  projectId: "login-finals5",
  storageBucket: "login-finals5.firebasestorage.app",
  messagingSenderId: "811880324174",
  appId: "1:811880324174:web:44bb1e9a87f9c8d8c4b71f"
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
