import React from "react";
import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./App.css";

function Home() {
  return (
    <div style={{ 
      background: "black", 
      color: "red", 
      minHeight: "100vh",
      padding: "20px",
      fontFamily: "Arial"
    }}>
      <h1>🚗 GARAGE ELITE</h1>
      <p>Backend Laravel + Frontend React</p>
      <div>
        <Link to="/login" style={{ color: "white", marginRight: "20px" }}>
          Connexion
        </Link>
        <Link to="/register" style={{ color: "white" }}>
          Inscription
        </Link>
      </div>
    </div>
  );
}

function Login() {
  return (
    <div style={{ padding: "20px" }}>
      <h2>Connexion</h2>
      <form>
        <input type="email" placeholder="Email" /><br/>
        <input type="password" placeholder="Mot de passe" /><br/>
        <button>Se connecter</button>
      </form>
    </div>
  );
}

function Register() {
  return (
    <div style={{ padding: "20px" }}>
      <h2>Inscription</h2>
      <form>
        <input type="text" placeholder="Nom" /><br/>
        <input type="text" placeholder="Prénom" /><br/>
        <input type="email" placeholder="Email" /><br/>
        <input type="password" placeholder="Mot de passe" /><br/>
        <button>S'inscrire</button>
      </form>
    </div>
  );
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
