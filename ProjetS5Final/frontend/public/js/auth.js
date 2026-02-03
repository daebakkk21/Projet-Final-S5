<<<<<<< HEAD
// This file is deprecated - React handles all UI now
console.log('Auth.js deprecated - using React instead');
=======
// Configuration du thème Garage Elite
const garageElite = {
    version: '1.0.0',
    theme: {
        colors: {
            primary: '#ff0000',
            gold: '#ffd700',
            black: '#000000',
            white: '#ffffff'
        },
        urls: {
            dashboard: 'dashboard.html',
            home: 'index.html'
        }
    },
    demo: {
        email: 'demo@garage-elite.com',
        password: 'Demo123!'
    },
    settings: {
        animations: true,
        notifications: true,
        autoRedirect: true
    }
};

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function() {
    console.log('%c🚗 GARAGE ELITE v' + garageElite.version, 
        'color: #ff0000; font-size: 18px; font-weight: bold; background: #000; padding: 10px;');
    console.log('%c🔧 Système d\'authentification premium', 'color: #ffd700;');
    console.log('%c📧 Demo: ' + garageElite.demo.email + ' / ' + garageElite.demo.password, 
        'color: #fff; background: #333; padding: 5px;');
    
    initGarageElite();
});

function initGarageElite() {
    // Initialisation des composants
    initMobileMenu();
    initPasswordToggles();
    initPasswordStrength();
    initFormSubmissions();
    initAnimations();
    initDemoFeatures();
    initNotifications();
    
    // Vérification des paramètres URL
    checkUrlParams();
}
>>>>>>> 1ccc550b (Add files via upload)

// Menu mobile
function initMobileMenu() {
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            this.innerHTML = navMenu.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
        
        // Fermer en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
        
        // Fermer en scrollant
        window.addEventListener('scroll', function() {
            navMenu.classList.remove('active');
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
        });
    }
}
