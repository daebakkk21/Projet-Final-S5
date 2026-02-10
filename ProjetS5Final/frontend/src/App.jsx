import React, { useState, useEffect } from "react";
import { BrowserRouter, Routes, Route, Link, useParams } from "react-router-dom";
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
          <Link to="/frontend" className="nav-item" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-users"></i><span>Liste utilisateurs</span>
          </Link>
          <Link to="/repairs" className="nav-item" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-car-side"></i><span>Réparations</span>
          </Link>
          <Link to="/garage" className="nav-item" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-warehouse"></i><span>Garage</span>
          </Link>
          <Link to="/admin" className="nav-item" onClick={() => setNavMenuOpen(false)}>
            <i className="fas fa-cog"></i><span>Admin</span>
          </Link>
        </div>
        <div className={`nav-actions ${navMenuOpen ? 'active' : ''}`}>
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
        // redirection automatique selon rôle (email admin détecté) :
        const isAdmin = (data.user.role && data.user.role === 'admin') || (data.user.email && data.user.email.includes('admin'));
        if (isAdmin) {
          window.location.href = "/admin";
        } else {
          window.location.href = "/client";
        }
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

// --- Admin Dashboard (frontend-only logic, works if API empty) ---
function AdminDashboard() {
  const [interventions, setInterventions] = useState([]);
  const [repairs, setRepairs] = useState([]);
  const [clientsCount, setClientsCount] = useState(0);
  const [form, setForm] = useState({ name: '', price: '', duration: '', unit: 's' });

  useEffect(() => {
    // load types and repairs
    fetch('http://localhost:8000/api/type_interventions')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setInterventions(d); })
      .catch(() => {});

    fetch('http://localhost:8000/api/repairs')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setRepairs(d); })
      .catch(() => {});

    fetch('http://localhost:8000/api/clients')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setClientsCount(d.length); })
      .catch(() => {});
  }, []);

  // add a TYPE d'intervention (name + price + duration + unit)
  const addIntervention = async (e) => {
    e.preventDefault();
    // convert duration to seconds based on unit
    const val = parseFloat(form.duration) || 0;
    let seconds = 0;
    if (form.unit === 's') seconds = Math.round(val);
    if (form.unit === 'h') seconds = Math.round(val * 3600);
    if (form.unit === 'd') seconds = Math.round(val * 86400);

    const payload = { nom: form.name || 'Intervention', prix: parseFloat(form.price) || 0, duree_secondes: seconds };
    try {
      const res = await fetch('http://localhost:8000/api/type_interventions', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload)});
      const data = await res.json();
      if (data && data.success === false) {
        alert('Erreur: ' + (data.message || '')); return;
      }
      // optimistic update: insert new type at top
      const newItem = { id: data.id || Date.now(), nom: payload.nom, prix: payload.prix, duree_secondes: payload.duree_secondes };
      setInterventions(prev => [newItem, ...prev]);
      setForm({ name: '', price: '', duration: '', unit: 's' });
    } catch (err) {
      alert('Erreur connexion API: ' + err.message);
    }
  };

  // total amount is based on repairs fetched from backend
  const totalAmount = repairs.reduce((s, r) => s + (Number(r.prix) || 0), 0);
  // totals paid / unpaid across all repairs
  const totalPaid = repairs.reduce((s, r) => s + ((r.paid === true) ? (Number(r.prix) || 0) : 0), 0);
  const totalUnpaid = repairs.reduce((s, r) => s + ((r.paid !== true) ? (Number(r.prix) || 0) : 0), 0);

  // interventions en cours = nombre de voitures uniques avec réparations en statut 'En cours' (ou similar) OR in_garage
  const ongoing = (() => {
    const inProgress = repairs.filter(r => (
      (r.in_garage === true) ||
      (r.statut || '').toLowerCase().includes('en cours') ||
      (r.statut || '').toLowerCase().includes('en-cours') ||
      (r.statut || '').toLowerCase().includes('en_cours')
    ));
    const uniqueVoitures = new Set(inProgress.map(r => r.voiture_id || r.immatriculation || r.id));
    return uniqueVoitures.size;
  })();

  // helper to compute a friendly status when backend provides none
  const formatStatus = (r) => {
    const now = Date.now();
    const endTs = r.end_time ? Date.parse(r.end_time) : null;
    if (r.in_garage) {
      if (endTs && endTs <= now) {
        return r.paid ? 'Terminée' : 'Terminée (attente paiement)';
      }
      return 'En cours';
    }
    // not in garage
    if (endTs && endTs <= now) {
      return r.paid ? 'Terminée' : 'Terminée (attente paiement)';
    }
    return r.statut || 'Planifiée';
  };

  const renderBarChart = () => {
    const values = interventions.map(it => Number(it.duree_secondes || it.duration || 0));
    if (values.length === 0) return null;
    const labels = interventions.map(it => it.nom || it.name || '');
    const max = Math.max(1, ...values);
    const barSlot = 90; // space per bar (increased)
    const width = Math.max(360, values.length * barSlot);
    const height = 260; // increased height for readability
    const barW = Math.max(36, Math.floor(width / values.length) - 18);

    const gradId = 'barGrad';

    return (
      <svg className="chart" viewBox={`0 0 ${width} ${height}`} preserveAspectRatio="xMidYMid meet" style={{width: '100%', height: height}}>
        <defs>
          <linearGradient id={gradId} x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="var(--primary)" />
            <stop offset="100%" stopColor="var(--gold)" />
          </linearGradient>
        </defs>
        {/* background */}
        <rect x={0} y={0} width={width} height={height} rx="8" fill="rgba(255,255,255,0.02)" />
        {values.map((v, i) => {
          const h = Math.round((v / max) * (height - 70));
          const x = i * (barW + 12) + 18;
          const y = height - h - 40;
          return (
            <g key={i}>
              <rect x={x} y={y} width={barW} height={h} rx="4" fill={`url(#${gradId})`} />
              <text x={x + barW/2} y={y - 10} fill="#fff" fontSize="14" fontWeight="700" textAnchor="middle">{v}</text>
              <text x={x + barW/2} y={height - 8} fill="rgba(240,240,240,0.95)" fontSize="13" textAnchor="middle">{labels[i]}</text>
            </g>
          );
        })}
      </svg>
    );
  };

  return (
    <div className="admin-area">
      <div className="container" style={{padding: '40px'}}>
      <Navigation />
      <h2>Admin — Gestion des interventions</h2>
      <div style={{display: 'flex', gap: '20px', marginTop: '20px'}}>
        <div style={{flex: 1}}>
          <table className="table" style={{width: '100%'}}>
            <thead>
              <tr><th>Nom</th><th>Prix (€)</th><th>Durée (s)</th></tr>
            </thead>
            <tbody>
              {interventions.length === 0 && <tr><td colSpan={3}>Aucun type d'intervention</td></tr>}
              {interventions.map(it => (
                <tr key={it.id}>
                  <td>{it.nom}</td>
                  <td>{it.prix !== undefined ? Number(it.prix).toFixed(2) : '0.00'}</td>
                  <td>{it.duree_secondes}</td>
                </tr>
              ))}
            </tbody>
          </table>
          
          <div style={{marginTop: 20}}>
            <h4>Réparations (base)</h4>
            <div className="card" style={{marginTop: 40}}>
              {repairs.length === 0 ? (
                <p>Aucune réparation enregistrée.</p>
              ) : (
                <div className="table-responsive">
                  <table className="table repairs-table">
                    <thead>
                      <tr><th>Voiture</th><th>Intervention</th><th>Prix</th><th>Durée (s)</th><th>Statut</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                      {repairs.map(r => (
                        <tr key={r.id}>
                          <td>{r.immatriculation || r.voiture_id || '—'}</td>
                          <td>{r.intervention || '—'}</td>
                          <td>{r.prix ? `${Number(r.prix).toFixed(2)} €` : '—'}</td>
                          <td>{r.duree_secondes || '—'}</td>
                          <td><span className={`status ${formatStatus(r).toLowerCase().replace(/\s+/g,'-')}`}>{formatStatus(r)}</span></td>
                          <td>{r.created_at ? new Date(r.created_at).toLocaleString() : '-'}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          </div>
        </div>

        <aside style={{width: '320px'}}>
          <div className="card" style={{padding: '16px', marginBottom: '16px', background: '#000', border: '1px solid #111'}}>
            <h4 style={{color: '#fff'}}>Statistiques</h4>
            <p style={{color: '#fff'}}>Montant total: <strong>€{totalAmount.toFixed(2)}</strong></p>
            <p style={{color: '#fff'}}>Montant payé: <strong>€{totalPaid.toFixed(2)}</strong></p>
            <p style={{color: '#fff'}}>Montant non payé: <strong>€{totalUnpaid.toFixed(2)}</strong></p>
            <p style={{color: '#fff'}}>Interventions en cours: <strong>{ongoing}</strong></p>
            <p style={{color: '#fff'}}>Nombre de clients: <strong>{clientsCount}</strong></p>
          </div>
        </aside>
      </div>
      {/* Full-width chart below */}
      <div style={{marginTop: 30}}>
        <div className="card">
          <h4>Durée des interventions (s)</h4>
          {interventions.length === 0 ? <p>Aucune donnée pour le graphique</p> : (
            <div style={{width: '100%'}}>
              {renderBarChart()}
            </div>
          )}
        </div>
      </div>
      </div>
    </div>
  );
}

// --- Client page: affiche QR code renvoyant vers l'app mobile (placeholder) ---
function ClientPage() {
  const [user, setUser] = useState(null);
  useEffect(() => {
    try {
      const u = JSON.parse(localStorage.getItem('user'));
      setUser(u);
    } catch (e) { setUser(null); }
  }, []);

  const target = user ? `https://mobile.garage-elite.app/?user=${encodeURIComponent(user.email||user.nom||'guest')}` : 'https://mobile.garage-elite.app/';
  // Use local QR image if provided, otherwise fallback to generated QR
  const qrLocal = '/images/Lien.jpeg';
  const qr = qrLocal; // switched to local image

  return (
    <div className="auth-page">
      <Navigation />
      <div className="qr-center">
        <h2>Client — Accès mobile</h2>
        <p className="lead">Rejoignez-nous sur l'app mobile</p>
        <div className="qr-wrap">
          <img src={qr} alt="QR code vers l'app mobile" className="qr-image" />
        </div>
        <p><a href={target} target="_blank" rel="noreferrer" className="btn btn-luxe-primary">Ouvrir l'application</a></p>
      </div>
    </div>
  );
}

// --- Frontend page: liste clients / réparations en cours (vue publique) ---
function FrontendList() {
  const [clients, setClients] = useState([]);
  const [repairs, setRepairs] = useState([]);
  const [voitures, setVoitures] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/clients')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setClients(d); else if (d && d.clients) setClients(d.clients); })
      .catch(() => setClients([]));

    fetch('http://localhost:8000/api/repairs')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setRepairs(d); else if (d && d.repairs) setRepairs(d.repairs); })
      .catch(() => setRepairs([]));

    fetch('http://localhost:8000/api/voitures')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setVoitures(d); else if (d && d.voitures) setVoitures(d.voitures); })
      .catch(() => setVoitures([]));
  }, []);

  // helper to compute a friendly status when backend provides none
  const formatStatus = (r) => {
    const now = Date.now();
    const endTs = r.end_time ? Date.parse(r.end_time) : null;
    if (r.in_garage) {
      if (endTs && endTs <= now) {
        return r.paid ? 'Terminée' : 'Terminée (attente paiement)';
      }
      return 'En cours';
    }
    // not in garage
    if (endTs && endTs <= now) {
      return r.paid ? 'Terminée' : 'Terminée (attente paiement)';
    }
    return r.statut || 'Planifiée';
  };

  return (
    <div className="container" style={{padding: '40px'}}>
      <Navigation />
      <div style={{display: 'flex', gap: 24, alignItems: 'flex-start', marginTop: 24}}>
        <div style={{flex: 1}}>
          <div className="card">
            <div style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
              <h3>Clients</h3>
              <span className="badge">{clients.length}</span>
            </div>
            <div className="table-responsive" style={{marginTop: 12}}>
              {clients.length === 0 ? (
                <p>Aucun client trouvé.</p>
              ) : (
                <table className="table clients-table">
                  <thead>
                    <tr><th>Nom</th><th>Email</th><th>Inscrit le</th><th>Action</th></tr>
                  </thead>
                  <tbody>
                    {clients.map(c => (
                      <tr key={c.id || c.email}>
                        <td>{(c.prenom || '') + ' ' + (c.nom || '')}</td>
                        <td>{c.email}</td>
                        <td>{c.created_at ? new Date(c.created_at).toLocaleString() : '-'}</td>
                        <td>
                          <Link 
                            to={`/client/${c.id}`}
                            className="btn btn-luxe-primary" 
                            style={{fontSize: 12, padding: '6px 12px', cursor: 'pointer', textDecoration: 'none', display: 'inline-block'}}
                          >
                            Voir voitures
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          </div>
        </div>

        <div style={{width: 560}}>
          <div className="card">
            <div style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
              <h3>Réparations en cours</h3>
              <span className="badge">{repairs.length}</span>
            </div>
            <div className="table-responsive" style={{marginTop: 12}}>
              {repairs.length === 0 ? (
                <p>Aucune réparation en cours.</p>
              ) : (
                <table className="table repairs-table">
                  <thead>
                    <tr><th>Client / Voiture</th><th>Intervention</th><th>Prix</th><th>Durée (s)</th><th>Statut</th></tr>
                  </thead>
                  <tbody>
                    {repairs.map(r => (
                      <tr key={r.id}>
                        <td>{r.immatriculation ? `${r.immatriculation}` : (r.client_email || '—')}</td>
                        <td>{r.intervention || '—'}</td>
                        <td>{r.prix !== null && r.prix !== undefined ? `${Number(r.prix).toFixed(2)} €` : '—'}</td>
                        <td>{r.duree_secondes || '—'}</td>
                        <td><span className={`status ${formatStatus(r).toLowerCase().replace(/\s+/g,'-')}`}>{formatStatus(r)}</span></td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          </div>
        </div>
      </div>

    </div>
  );
}

// --- Client Details Page: affiche les voitures d'un client ---
function ClientDetails() {
  const { clientId } = useParams();
  const [clients, setClients] = useState([]);
  const [voitures, setVoitures] = useState([]);
  const [repairs, setRepairs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    fetch('http://localhost:8000/api/clients')
      .then(r => r.json())
      .then(d => { 
        console.log('Clients fetched:', d);
        console.log('Looking for clientId:', clientId);
        if (Array.isArray(d)) {
          console.log('IDs available:', d.map(c => c.id));
          setClients(d);
        } else if (d && d.clients) setClients(d.clients);
        else setClients([]);
      })
      .catch(e => { console.error('Clients fetch error:', e); setClients([]); })
      .finally(() => setLoading(false));

    fetch('http://localhost:8000/api/voitures')
      .then(r => r.json())
      .then(d => { 
        console.log('Voitures fetched:', d);
        if (Array.isArray(d)) setVoitures(d); 
        else if (d && d.voitures) setVoitures(d.voitures);
        else setVoitures([]);
      })
      .catch(e => { console.error('Voitures fetch error:', e); setVoitures([]); });

    // Fetch both active and archived repairs
    Promise.all([
      fetch('http://localhost:8000/api/repairs').then(r => r.json()),
      fetch('http://localhost:8000/api/repairs/archived').then(r => r.json()).catch(() => [])
    ])
    .then(([activeData, archivedData]) => { 
      const active = Array.isArray(activeData) ? activeData : (activeData && activeData.repairs) ? activeData.repairs : [];
      const archived = Array.isArray(archivedData) ? archivedData : (archivedData && archivedData.repairs) ? archivedData.repairs : [];
      setRepairs([...active, ...archived]);
    })
    .catch(e => { console.error('Repairs fetch error:', e); setRepairs([]); });
  }, [clientId]);

  const client = clients.find(c => {
    console.log(`Comparing: "${c.id}" === "${clientId}" (${c.id === clientId})`);
    return c.id === clientId;
  });

  if (loading) {
    return (
      <div className="container" style={{padding: '40px', minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center'}}>
        <Navigation />
        <div style={{textAlign: 'center', color: '#d4af37'}}>
          <div style={{fontSize: 32, marginBottom: 16}}>⏳</div>
          <p>Chargement des données...</p>
        </div>
      </div>
    );
  }

  if (!client) {
    return (
      <div className="container" style={{padding: '40px', minHeight: '100vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center'}}>
        <Navigation />
        <div className="card" style={{textAlign: 'center', maxWidth: 400, padding: 40}}>
          <div style={{fontSize: 48, marginBottom: 16}}><i className="fas fa-exclamation-circle"></i></div>
          <h3 style={{color: '#d32f2f', marginBottom: 8}}>Client non trouvé</h3>
          <p style={{color: '#666', fontSize: 14, marginBottom: 20}}>L'ID client demandé n'existe pas dans la base de données.</p>
          <p style={{background: '#111', padding: '12px', borderRadius: '4px', color: '#999', fontSize: 12, marginBottom: 20}}>
            ID recherché: <strong>{clientId}</strong><br/>
            Clients disponibles: <strong>{clients.length}</strong>
          </p>
          <Link to="/frontend" className="btn btn-luxe-primary" style={{textDecoration: 'none'}}>
            ← Retour à la liste
          </Link>
        </div>
      </div>
    );
  }

  const clientVoitures = voitures.filter(v => v.client_uid === clientId || v.email === client.email);
  
  // Helper to get repair for a car by immatriculation
  const getRepairForCar = (immatriculation) => {
    return repairs.find(r => r.immatriculation === immatriculation);
  };
  
  const getStatutColor = (statut) => {
    switch(statut) {
      case 'enAttente': return '#ff9800';
      case 'enReparation': return '#2196f3';
      case 'prete': return '#4caf50';
      case 'terminee': return '#4caf50';
      case 'terminée': return '#4caf50';
      default: return '#999';
    }
  };

  const formatRepairStatus = (statut) => {
    if (statut === 'enAttente') return 'En attente';
    if (statut === 'enReparation') return 'En réparation';
    if (statut === 'prete') return 'Prête';
    if (statut === 'terminee' || statut === 'terminée') return 'Terminée';
    return statut || 'Planifiée';
  };

  return (
    <div className="container" style={{padding: '40px'}}>
      <Navigation />
      <div style={{marginBottom: 32}}>
        <Link 
          to="/frontend"
          style={{
            display: 'inline-flex',
            alignItems: 'center',
            gap: 8,
            fontSize: 14,
            cursor: 'pointer',
            color: '#d4af37',
            textDecoration: 'none',
            transition: 'all 0.3s ease',
            padding: '8px 12px',
            borderRadius: 4,
            marginBottom: 20
          }}
          onMouseEnter={(e) => e.target.style.color = '#fff'}
          onMouseLeave={(e) => e.target.style.color = '#d4af37'}
        >
          <i className="fas fa-arrow-left"></i><span>Retour à la liste</span>
        </Link>
      </div>
      
      <div className="card" style={{marginBottom: 32, background: 'linear-gradient(135deg, rgba(212,175,55,0.05) 0%, rgba(212,175,55,0.02) 100%)', borderLeft: '4px solid #d4af37'}}>
        <div style={{display: 'flex', alignItems: 'center', gap: 20, marginBottom: 16}}>
          <div style={{fontSize: 40, width: 60, height: 60, display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'rgba(212,175,55,0.1)', borderRadius: 8}}>
            <i className="fas fa-user-circle"></i>
          </div>
          <div>
            <h2 style={{margin: '0 0 8px 0', fontSize: 28}}>{client.prenom} {client.nom}</h2>
            <p style={{color: '#999', margin: 0, fontSize: 14}}><i className="fas fa-envelope"></i> {client.email}</p>
            {client.created_at && (
              <p style={{color: '#999', margin: '4px 0 0 0', fontSize: 13}}>
                <i className="fas fa-calendar"></i> Client depuis le {new Date(client.created_at).toLocaleDateString('fr-FR')}
              </p>
            )}
          </div>
        </div>
      </div>

      {clientVoitures.length === 0 ? (
        <div className="card" style={{textAlign: 'center', padding: 40, background: 'rgba(255,152,0,0.05)', border: '2px dashed #ff9800'}}>
          <div style={{fontSize: 48, marginBottom: 16, color: '#ff9800'}}><i className="fas fa-car"></i></div>
          <h3 style={{color: '#ff9800', marginBottom: 8}}>Aucune voiture</h3>
          <p style={{color: '#666', fontSize: 14}}>Ce client n'a pas encore de véhicule enregistré dans le système.</p>
        </div>
      ) : (
        <div className="card">
          <div style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20}}>
            <h3 style={{margin: 0}}><i className="fas fa-car-side"></i> Véhicules ({clientVoitures.length})</h3>
            <span className="badge">{clientVoitures.length}</span>
          </div>
          <div className="table-responsive">
            <table className="table" style={{fontSize: 13}}>
              <thead>
                <tr>
                  <th style={{width: '18%'}}>Immatriculation</th>
                  <th style={{width: '30%'}}>Description</th>
                  <th style={{width: '26%'}}>Statut réparation</th>
                  <th style={{width: '26%'}}>Payée</th>
                </tr>
              </thead>
              <tbody>
                {clientVoitures.map(v => {
                  const repair = getRepairForCar(v.immatriculation);
                  const repairStatus = repair ? repair.statut_id : v.statut_id;
                  const isPaid = repair ? repair.paid === true : false;
                  
                  return (
                    <tr key={v.id} style={{borderBottom: '1px solid #222', transition: 'background-color 0.2s ease'}} onMouseEnter={(e) => e.currentTarget.style.backgroundColor = 'rgba(212,175,55,0.05)'} onMouseLeave={(e) => e.currentTarget.style.backgroundColor = 'transparent'}>
                      <td>
                        <strong style={{display: 'flex', alignItems: 'center', gap: 8, color: '#d4af37'}}>
                          <i className="fas fa-hashtag"></i>{v.immatriculation || '—'}
                        </strong>
                      </td>
                      <td>
                        <div style={{color: '#ccc'}}>{v.description || '—'}</div>
                      </td>
                      <td>
                        <span style={{
                          fontSize: 12,
                          padding: '6px 12px',
                          background: getStatutColor(repairStatus),
                          color: '#fff',
                          borderRadius: 20,
                          fontWeight: 600,
                          textTransform: 'capitalize',
                          display: 'inline-block'
                        }}>
                          {formatRepairStatus(repairStatus)}
                        </span>
                      </td>
                      <td style={{fontSize: 12, fontWeight: 600}}>
                        <span style={{color: isPaid ? '#4caf50' : '#ff9800'}}>
                          {isPaid ? '✓ Payée' : '⚠️ Non payée'}
                        </span>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}

// --- Repairs page: voitures en attente d'entrer au garage ---
function RepairsPage() {
  const [repairs, setRepairs] = useState([]);
  const [loading, setLoading] = useState(true);
  
  const load = () => {
    fetch('http://localhost:8000/api/repairs')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setRepairs(d); else setRepairs([]); setLoading(false); })
      .catch(e => { console.error(e); setRepairs([]); setLoading(false); });
  };

  useEffect(() => { load(); }, []);

  const addToGarage = async (id) => {
    try {
      const res = await fetch(`http://localhost:8000/api/repairs/${id}/add_to_garage`, { method: 'POST' });
      const j = await res.json();
      if (!j.success) alert('❌ ' + (j.message || 'Erreur')); 
      else { alert('✓ Voiture mise au garage!'); load(); }
    } catch (e) { alert('Erreur réseau: ' + e.message); }
  };

  const waitingRepairs = repairs.filter(r => !r.in_garage);

  return (
    <div className="container" style={{padding: 24}}>
      <Navigation />
      <div style={{marginTop: 18, display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}>
        <h2>📋 Voitures en attente</h2>
        <button onClick={() => { setLoading(true); load(); }} style={{padding: '8px 16px', background: '#666', color: '#fff', border: 'none', borderRadius: 4, cursor: 'pointer'}}>↻</button>
      </div>

      {loading && <div style={{marginTop: 12, fontSize: 14, color: '#666'}}>Chargement...</div>}

      {!loading && waitingRepairs.length === 0 && (
        <div style={{marginTop: 12, padding: 20, background: '#0f0f0f', borderRadius: 8, border: '1px solid #222'}}>
          <strong style={{color: '#ddd'}}>✓ Aucune voiture en attente - toutes sont au garage!</strong>
        </div>
      )}

      <div style={{display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))', gap: 16, marginTop: 18}}>
        {waitingRepairs.map(r => (
          <div className="card" key={r.id} style={{padding: 16}}>
            <h3 style={{marginTop: 0}}><strong>{r.immatriculation || 'Voiture #' + r.id}</strong></h3>
            <div style={{fontSize: 13, color: '#666', marginBottom: 8}}>🔧 {r.intervention || '—'}</div>
            <div style={{fontSize: 13, marginBottom: 12}}>💰 {r.prix || 0}€ | ⏱️ {r.duree_secondes || '?'}s</div>
            <button 
              className="btn btn-luxe-primary" 
              onClick={() => addToGarage(r.id)}
              style={{width: '100%'}}
            >
              ➕ Mettre au garage
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

// --- Garage page: simulation de garage avec 3 slots + attente ---
function GaragePage() {
  const [repairs, setRepairs] = useState([]);
  const [now, setNow] = useState(Date.now());
  const [selectedClient, setSelectedClient] = useState(null); // for details modal
  
  const load = () => {
    fetch('http://localhost:8000/api/repairs')
      .then(r => r.json())
      .then(d => { if (Array.isArray(d)) setRepairs(d); else setRepairs([]); })
      .catch(e => { console.error(e); setRepairs([]); });
  };

  useEffect(() => { 
    load(); 
    const t = setInterval(() => { setNow(Date.now()); }, 1000); 
    return () => clearInterval(t); 
  }, []);

  // Check every time 'now' updates and reload repairs, then move finished ones
  useEffect(() => {
    load();
  }, [now]);

  // Only show repairs that are in_garage and not yet recovered
  const garageRepairs = repairs.filter(r => r.in_garage && r.recovered !== true);
  
  // Waiting slot: receives finished repairs ONLY if slot is empty (max 1 car)
  // First, find the repair that should be in waiting slot (finished, not in garage, not recovered, not paid)
  const finishedRepairs = repairs.filter(r => {
    if (r.recovered === true) return false;
    const endTs = r.end_time ? Date.parse(r.end_time) : null;
    if (!endTs) return false;
    return endTs <= now; // finished
  });

  // Separate: in waiting slot vs still in garage (waiting for payment/recovery)
  const inWaitingSlot = finishedRepairs.filter(r => !r.in_garage); // moved out (only 1 max)
  const finishedInGarageStillThere = garageRepairs.filter(r => {
    const endTs = r.end_time ? Date.parse(r.end_time) : null;
    if (!endTs) return false;
    return endTs <= now; // finished but still waiting for payment/recovery
  });

  const waitingSlot = inWaitingSlot.length > 0 ? inWaitingSlot[0] : null; // Only 1 car max in waiting slot

  // When repairs or now changes, auto-move finished repairs to waiting slot if empty
  useEffect(() => {
    // Waiting slot can hold MAX 1 repair
    const finishedButStillInGarage = garageRepairs.filter(r => {
      const endTs = r.end_time ? Date.parse(r.end_time) : null;
      if (!endTs) return false;
      return endTs <= now; // finished
    });

    // Auto-move first finished repair to waiting slot ONLY if waiting slot is empty
    if (!waitingSlot && finishedButStillInGarage.length > 0) {
      const toMove = finishedButStillInGarage[0];
      console.log('Auto-moving to waiting slot:', toMove.immatriculation);
      
      // First update the statut_id to "terminee"
      fetch(`http://localhost:8000/api/repairs/${toMove.id}`, { 
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ statut_id: 'terminee' })
      })
        .then(res => res.json())
        .then(data => {
          // Then move to waiting slot (DO NOT archive yet)
          return fetch(`http://localhost:8000/api/repairs/${toMove.id}/move-to-waiting-slot`, { method: 'POST' });
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            console.log('Moved successfully to waiting slot');
            // Reload repairs after move
            setTimeout(() => {
              fetch('http://localhost:8000/api/repairs')
                .then(r => r.json())
                .then(d => { if (Array.isArray(d)) setRepairs(d); })
                .catch(() => {});
            }, 500);
          }
        })
        .catch(e => console.error('Move error:', e));
    }
  }, [repairs, now, waitingSlot]);

  const markAsPaid = async (id) => {
    try {
      const res = await fetch(`http://localhost:8000/api/repairs/${id}/mark-paid`, { method: 'POST' });
      const j = await res.json();
      if (!j.success) alert('Erreur: ' + (j.message || '')); 
      else { alert('✓ Paiement enregistré!'); load(); }
    } catch (e) { alert('Erreur: ' + e.message); }
  };

  const removeFromGarage = async (id) => {
    try {
      const res = await fetch(`http://localhost:8000/api/repairs/${id}/remove-from-garage`, { method: 'POST' });
      const j = await res.json();
      if (!j.success) alert('Erreur: ' + (j.message || '')); 
      else { alert('✓ Voiture récupérée et archivée!'); load(); }
    } catch (e) { alert('Erreur: ' + e.message); }
  };

  const markAsFinished = async (id) => {
    try {
      const res = await fetch(`http://localhost:8000/api/repairs/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ statut_id: 'terminee' })
      });
      const j = await res.json();
      return j.success || false;
    } catch (e) { 
      console.error('Erreur mark as finished:', e);
      return false;
    }
  };
  
  const renderSlot = (index) => {
    const repair = garageRepairs[index];
    if (!repair) {
      return (
        <div style={{
          background: 'linear-gradient(135deg, #0b0b0b 0%, #1a1a1a 100%)',
          border: '3px dashed #333',
          borderRadius: 8,
          padding: 20,
          textAlign: 'center',
          color: '#ccc',
          fontSize: 48,
          height: 240,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center'
        }}>
          <span>🅿️</span>
        </div>
      );
    }

    const endTs = repair.end_time ? Date.parse(repair.end_time) : null;
    const remaining = endTs ? Math.max(0, Math.round((endTs - now) / 1000)) : null;
    const isFinished = remaining === 0;
    const isPaid = repair.paid === true;
    const canLeave = isFinished && isPaid;

    return (
      <div style={{
        background: canLeave ? '#ffebee' : '#ffeb3b',
        border: canLeave ? '3px solid #f44336' : '3px solid #fbc02d',
        borderRadius: 8,
        padding: 20,
        textAlign: 'center',
        position: 'relative',
        height: 240,
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-between'
      }}>
        <div>
          <div style={{fontSize: 32, marginBottom: 8}}>🚗</div>
          <h4 style={{margin: '8px 0'}}>{repair.immatriculation || 'Voiture'}</h4>
          <div style={{fontSize: 12, color: '#333', marginBottom: 4}}>
            {repair.intervention || '—'}
          </div>
        </div>

        <div style={{
          fontSize: isFinished ? 16 : 24,
          fontWeight: 700,
          color: '#d32f2f',
          fontFamily: 'monospace'
        }}>
          {remaining !== null ? (
            <div>
              {Math.floor(remaining/3600).toString().padStart(2,'0')}:{Math.floor((remaining%3600)/60).toString().padStart(2,'0')}:{(remaining%60).toString().padStart(2,'0')}
              {isFinished && <div style={{fontSize: 14, color: '#333'}}>✓ Réparation finie</div>}
            </div>
          ) : (
            <div>--:--:--</div>
          )}
        </div>

        <div>
          <div style={{
            width: '100%',
            padding: 8,
            background: isPaid ? '#1b5e20' : '#3e2723',
            color: '#fff',
            borderRadius: 4,
            textAlign: 'center',
            marginBottom: 8,
            fontSize: 12,
            fontWeight: 'bold'
          }}>
            {isPaid ? `✓ Payé (${repair.prix || 0}€)` : `Non payé (${repair.prix || 0}€)`}
          </div>

          {canLeave && (
            <div style={{
              width: '100%',
              padding: 8,
              background: '#111',
              color: '#fff',
              borderRadius: 4,
              textAlign: 'center',
              marginBottom: 0,
              fontSize: 13,
              fontWeight: '700'
            }}>
              Prête — en attente de récupération
            </div>
          )}
        </div>
      </div>
    );
  };

  return (
    <div className="container" style={{padding: 24}}>
      <Navigation />
      <h2 style={{marginTop: 18}}>🏭 Garage de réparation</h2>
      <p style={{color: '#666', fontSize: 14}}>
        Capacité: <strong>{garageRepairs.length} / 3</strong> voitures
      </p>

      <div>
        <h3 style={{marginTop: 24, marginBottom: 16}}>En réparation</h3>
        <div style={{
          display: 'grid',
          gridTemplateColumns: '1fr 1fr 1fr',
          gap: 24,
          marginTop: 24,
          maxWidth: 1200,
          margin: '24px auto'
        }}>
          <div>
            <h4 style={{textAlign: 'center', color: '#666'}}>Slot 1</h4>
            {renderSlot(0)}
          </div>
          <div>
            <h4 style={{textAlign: 'center', color: '#666'}}>Slot 2</h4>
            {renderSlot(1)}
          </div>
          <div>
            <h4 style={{textAlign: 'center', color: '#666'}}>Slot 3</h4>
            {renderSlot(2)}
          </div>
        </div>
      </div>

      <div style={{marginTop: 40}}>
        <h3 style={{marginBottom: 16}}>En attente de récupération</h3>
        <h4 style={{textAlign: 'center', color: '#666', marginBottom: 16}}>Slot d'attente (capacité: 1)</h4>
        
        {/* Waiting Slot - rendered like repair slots */}
        {!waitingSlot ? (
          <div style={{
            background: 'linear-gradient(135deg, #0b0b0b 0%, #1a1a1a 100%)',
            border: '3px dashed #333',
            borderRadius: 8,
            padding: 20,
            textAlign: 'center',
            color: '#ccc',
            fontSize: 48,
            height: 240,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            maxWidth: 400,
            margin: '0 auto 40px'
          }}>
            <span>📋</span>
          </div>
        ) : (
          <div style={{
            background: waitingSlot.paid ? '#e8f5e9' : '#fff3cd',
            border: waitingSlot.paid ? '3px solid #4caf50' : '3px solid #ff9800',
            borderRadius: 8,
            padding: 20,
            textAlign: 'center',
            maxWidth: 400,
            margin: '0 auto 40px',
            height: 240,
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between'
          }}>
            <div>
              <div style={{fontSize: 32, marginBottom: 8}}>🚗</div>
              <h4 style={{margin: '8px 0'}}>{waitingSlot.immatriculation || 'Voiture'}</h4>
              <div style={{fontSize: 12, color: '#333', marginBottom: 4}}>
                {waitingSlot.intervention || '—'}
              </div>
            </div>

            <div style={{
              fontSize: 16,
              fontWeight: 700,
              color: '#d32f2f',
              fontFamily: 'monospace'
            }}>
              ✓ Réparation finie
            </div>

            <div>
              <div style={{
                width: '100%',
                padding: 8,
                background: waitingSlot.paid ? '#1b5e20' : '#3e2723',
                color: '#fff',
                borderRadius: 4,
                textAlign: 'center',
                marginBottom: 0,
                fontSize: 12,
                fontWeight: 'bold'
              }}>
                {waitingSlot.paid ? `✓ Payé (${waitingSlot.prix || 0}€)` : `Non payé (${waitingSlot.prix || 0}€)`}
              </div>
            </div>
          </div>
        )}

        {/* If waiting slot is full, show finished repairs still in garage */}
        {finishedInGarageStillThere.length > 0 && (
          <div style={{marginTop: 30}}>
            <h4 style={{textAlign: 'center', color: '#ff9800', marginBottom: 16}}>Slot d'attente plein - Attente dans le garage</h4>
            <div style={{display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))', gap: 16}}>
              {finishedInGarageStillThere.map(r => (
                <div key={r.id} style={{
                  background: r.paid ? '#e8f5e9' : '#fff3cd',
                  border: r.paid ? '2px solid #4caf50' : '2px solid #ff9800',
                  borderRadius: 8,
                  padding: 16
                }}>
                  <p><strong>{r.immatriculation || 'Voiture'}</strong></p>
                  <p style={{fontSize: 12, color: '#666'}}>Intervention: {r.intervention || '—'}</p>
                  <p style={{fontSize: 12, color: '#666'}}>Prix: {r.prix ? `${Number(r.prix).toFixed(2)} €` : '—'}</p>
                  <p style={{fontSize: 12, color: r.paid ? '#4caf50' : '#ff9800'}}>{r.paid ? '✓ Payée' : '⚠️ Attente paiement'}</p>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>

      <div style={{marginTop: 32, padding: 16, background: '#000000', borderRadius: 8, border: '1px solid #111'}}>
        <h4 style={{color: '#fff'}}>📊 Résumé</h4>
        <ul style={{fontSize: 13, color: '#fff'}}>
          <li><strong>Voitures au garage:</strong> {garageRepairs.length}/3</li>
          <li><strong>En réparation:</strong> {garageRepairs.filter(r => !r.end_time || Math.round((Date.parse(r.end_time) - now) / 1000) > 0).length}</li>
          <li><strong>Réparées (dans garage):</strong> {finishedInGarageStillThere.length}</li>
          <li><strong>Slot d'attente:</strong> {waitingSlot ? '1/1 (plein)' : '0/1 (vide)'}</li>
          <li><strong>Prêtes à récupérer:</strong> {(waitingSlot ? 1 : 0) + finishedInGarageStillThere.length}</li>
        </ul>
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
        <Route path="/admin" element={<AdminDashboard />} />
        <Route path="/client" element={<ClientPage />} />
        <Route path="/client/:clientId" element={<ClientDetails />} />
        <Route path="/frontend" element={<FrontendList />} />
        <Route path="/repairs" element={<RepairsPage />} />
        <Route path="/garage" element={<GaragePage />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
