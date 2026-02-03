import React, { useState } from "react";
import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import "./style.css";
import "./auth.css";

// Navigation partagée
function Navigation() {
  const [navMenuOpen, setNavMenuOpen] = useState(false);
  return (
    <nav className="nav-luxe">
      <div className="nav-container">
        <Link to="/" className="logo-luxe" onClick={() => setNavMenuOpen(false)}>
          <div className="logo-icon"><i className="fas fa-crown"></i></div>
          <div className="logo-text">
            <span className="logo-main">GARAGE</span>
            <span className="logo-sub">ELITE</span>
          </div>
        </Link>
        <div className={`nav-menu ${navMenuOpen ? 'active' : ''}`} id="navMenu">
          <Link to="/" className="nav-item active" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-home"></i><span>Accueil</span>
          </Link>
          <Link to="/login" className="nav-item" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-sign-in-alt"></i><span>Connexion</span>
          </Link>
          <Link to="/register" className="nav-item nav-cta" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-user-plus"></i><span>S'inscrire</span>
          </Link>
        </div>
        <button className="mobile-toggle" onClick={() => setNavMenuOpen(!navMenuOpen)}>
          <i className="fas fa-bars"></i>
        </button>
      </div>
    </nav>
  );
}

// Page d'accueil
function Home() {
  return (
    <div>
      <Navigation />
      <section className="hero-luxe">
        <div className="hero-overlay"></div>
        <div className="hero-content">
          <div className="hero-badge">
            <i className="fas fa-gem"></i><span>EXPÉRIENCE PREMIUM</span>
          </div>
          <h1 className="hero-title">
            L'Excellence<span className="highlight"> Automobile</span><br />Réinventée
          </h1>
          <p className="hero-subtitle">
            Un écosystème complet de gestion garage alliant technologie de pointe et expérience utilisateur premium.
          </p>
          <div className="hero-actions">
            <Link to="/register" className="btn btn-luxe-primary">
              <i className="fas fa-rocket"></i><span>Commencer l'expérience</span>
            </Link>
            <a href="#features" className="btn btn-luxe-outline">
              <i className="fas fa-play-circle"></i><span>Voir la démo</span>
            </a>
          </div>
          <div className="hero-stats">
            <div className="stat-item"><div className="stat-number">99%</div><div className="stat-label">Satisfaction clients</div></div>
            <div className="stat-item"><div className="stat-number">24/7</div><div className="stat-label">Support premium</div></div>
            <div className="stat-item"><div className="stat-number">500+</div><div className="stat-label">Garages partenaires</div></div>
          </div>
        </div>
        <div className="hero-scroll"><div className="scroll-text">Découvrir</div><div className="scroll-line"></div></div>
      </section>

      <footer className="footer-luxe">
        <div className="footer-container">
          <div className="footer-content">
            <div className="footer-section">
              <h3><i className="fas fa-crown"></i> Garage Elite</h3>
              <p>Plateforme de gestion automobile premium pour les garages modernes.</p>
            </div>
            <div className="footer-section">
              <h4>Produit</h4>
              <ul>
                <li><a href="#features">Fonctionnalités</a></li>
                <li><a href="#pricing">Tarifs</a></li>
                <li><a href="#docs">Documentation</a></li>
              </ul>
            </div>
            <div className="footer-section">
              <h4>Support</h4>
              <ul>
                <li><a href="#help">Aide</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#status">Statut</a></li>
              </ul>
            </div>
            <div className="footer-section">
              <h4>Légal</h4>
              <ul>
                <li><a href="#">Conditions</a></li>
                <li><a href="#">Confidentialité</a></li>
                <li><a href="#">Cookies</a></li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

// Page de connexion
function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const response = await fetch("http://localhost:8000/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const data = await response.json();
      if (data.success) {
        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data.user));
        alert("Connexion réussie! " + data.user.nom);
        window.location.href = "/";
      } else {
        setError(data.message || "Erreur de connexion");
      }
    } catch (err) {
      setError("Erreur de connexion au serveur: " + err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-page">
      <div className="auth-container">
        <header className="auth-header-luxe">
          <Link to="/" className="back-to-home"><i className="fas fa-arrow-left"></i><span>Retour à l'accueil</span></Link>
          <Link to="/" className="auth-logo"><div className="logo-icon-small"><i className="fas fa-crown"></i></div><span>GARAGE ELITE</span></Link>
        </header>
        <main className="auth-main">
          <div className="auth-wrapper">
            <div className="auth-image-side">
              <div className="image-overlay-dark"></div>
              <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Voiture de sport luxe" />
              <div className="image-content">
                <div className="image-badge"><i className="fas fa-star"></i><span>ACCÈS PRIVILÉGIÉ</span></div>
                <h2>Bienvenue dans l'univers <span className="highlight">Garage Elite</span></h2>
                <p>Accédez à votre espace personnel et gérez vos véhicules en toute simplicité</p>
                <div className="image-features">
                  <div className="feature"><i className="fas fa-shield-alt"></i><span>Sécurité maximale</span></div>
                  <div className="feature"><i className="fas fa-bolt"></i><span>Interface ultra-rapide</span></div>
                  <div className="feature"><i className="fas fa-headset"></i><span>Support 24/7</span></div>
                </div>
              </div>
            </div>
            <div className="auth-form-side">
              <div className="form-container">
                <div className="form-header">
                  <div className="form-icon"><i className="fas fa-lock"></i></div>
                  <h1>Connexion</h1>
                  <p className="form-subtitle">Accédez à votre compte Garage Elite</p>
                </div>
                <form onSubmit={handleSubmit} className="auth-form-luxe">
                  {error && <div style={{background: "rgba(255, 51, 51, 0.2)", color: "#ff3333", padding: "12px", borderRadius: "8px", marginBottom: "20px", border: "1px solid #ff3333"}}>{error}</div>}
                  <div className="form-group-luxe">
                    <label htmlFor="email"><i className="fas fa-envelope"></i><span>Adresse email</span></label>
                    <input type="email" id="email" placeholder="votre@email.com" value={email} onChange={(e) => setEmail(e.target.value)} required className="input-luxe" />
                    <div className="input-border"></div>
                  </div>
                  <div className="form-group-luxe">
                    <label htmlFor="password"><i className="fas fa-key"></i><span>Mot de passe</span></label>
                    <div className="password-wrapper">
                      <input type="password" id="password" placeholder="Votre mot de passe" value={password} onChange={(e) => setPassword(e.target.value)} required className="input-luxe" />
                      <div className="input-border"></div>
                    </div>
                  </div>
                  <button type="submit" className="btn btn-luxe-primary btn-block" disabled={loading}>
                    <i className="fas fa-sign-in-alt"></i><span>{loading ? "Connexion en cours..." : "Se connecter"}</span>
                  </button>
                </form>
                <div className="auth-switch">
                  <p>Pas encore inscrit ?</p>
                  <Link to="/register" className="switch-link"><i className="fas fa-user-plus"></i>Créer un compte</Link>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}

// Page d'inscription
function Register() {
  const [formData, setFormData] = useState({ firstName: "", lastName: "", email: "", password: "" });
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const response = await fetch("http://localhost:8000/api/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });
      const data = await response.json();
      if (data.success) {
        alert("Inscription réussie! Vous pouvez maintenant vous connecter");
        window.location.href = "/login";
      } else {
        setError(data.message || "Erreur lors de l'inscription");
      }
    } catch (err) {
      setError("Erreur de connexion au serveur: " + err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-page">
      <div className="auth-container">
        <header className="auth-header-luxe">
          <Link to="/" className="back-to-home"><i className="fas fa-arrow-left"></i><span>Retour à l'accueil</span></Link>
          <Link to="/" className="auth-logo"><div className="logo-icon-small"><i className="fas fa-crown"></i></div><span>GARAGE ELITE</span></Link>
        </header>
        <main className="auth-main">
          <div className="auth-wrapper">
            <div className="auth-image-side">
              <div className="image-overlay-dark"></div>
              <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Mécanicien au travail" />
              <div className="image-content">
                <div className="image-badge"><i className="fas fa-gem"></i><span>COMMENCEZ L'AVENTURE</span></div>
                <h2>Rejoignez la révolution <span className="highlight">automobile</span></h2>
                <p>Créez votre compte et bénéficiez de tous nos services premium</p>
                <div className="image-features">
                  <div className="feature"><i className="fas fa-rocket"></i><span>Configuration en 2 minutes</span></div>
                  <div className="feature"><i className="fas fa-gift"></i><span>14 jours d'essai gratuit</span></div>
                  <div className="feature"><i className="fas fa-users"></i><span>Communauté active</span></div>
                </div>
              </div>
            </div>
            <div className="auth-form-side">
              <div className="form-container">
                <div className="form-header">
                  <div className="form-icon"><i className="fas fa-user-plus"></i></div>
                  <h1>Créer un compte</h1>
                  <p className="form-subtitle">Rejoignez Garage Elite en quelques secondes</p>
                </div>
                <form onSubmit={handleSubmit} className="auth-form-luxe">
                  {error && <div style={{background: "rgba(255, 51, 51, 0.2)", color: "#ff3333", padding: "12px", borderRadius: "8px", marginBottom: "20px", border: "1px solid #ff3333"}}>{error}</div>}
                  <div className="form-row">
                    <div className="form-group-luxe">
                      <label htmlFor="firstName"><i className="fas fa-user"></i><span>Prénom</span></label>
                      <input type="text" id="firstName" name="firstName" placeholder="Votre prénom" value={formData.firstName} onChange={handleChange} required className="input-luxe" />
                      <div className="input-border"></div>
                    </div>
                    <div className="form-group-luxe">
                      <label htmlFor="lastName"><i className="fas fa-user"></i><span>Nom</span></label>
                      <input type="text" id="lastName" name="lastName" placeholder="Votre nom" value={formData.lastName} onChange={handleChange} required className="input-luxe" />
                      <div className="input-border"></div>
                    </div>
                  </div>
                  <div className="form-group-luxe">
                    <label htmlFor="email"><i className="fas fa-envelope"></i><span>Adresse email</span></label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" value={formData.email} onChange={handleChange} required className="input-luxe" />
                    <div className="input-border"></div>
                  </div>
                  <div className="form-group-luxe">
                    <label htmlFor="password"><i className="fas fa-key"></i><span>Mot de passe</span></label>
                    <div className="password-wrapper">
                      <input type="password" id="password" name="password" placeholder="Votre mot de passe" value={formData.password} onChange={handleChange} required className="input-luxe" />
                      <div className="input-border"></div>
                    </div>
                  </div>
                  <button type="submit" className="btn btn-luxe-primary btn-block" disabled={loading}>
                    <i className="fas fa-user-plus"></i><span>{loading ? "Création en cours..." : "Créer mon compte"}</span>
                  </button>
                </form>
                <div className="auth-switch">
                  <p>Vous avez déjà un compte ?</p>
                  <Link to="/login" className="switch-link"><i className="fas fa-sign-in-alt"></i>Se connecter</Link>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}

// Application principale
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
