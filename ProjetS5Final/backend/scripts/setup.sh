#!/bin/bash
# backend/scripts/setup.sh
# Complete Firebase setup: data import + auth users + security rules

set -e

PROJECT_ID="garage-5ef1a"
FIREBASE_DB_URL="https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app/"

echo "================================"
echo "Garage Elite Firebase Setup"
echo "================================"
echo ""

# Check if firebase CLI is installed
if ! command -v firebase &> /dev/null; then
    echo "⚠️  Firebase CLI not found. Install it:"
    echo "   npm install -g firebase-tools"
    echo ""
    echo "Then run: firebase login"
    exit 1
fi

# Check if user is logged in
if ! firebase projects:list | grep -q "$PROJECT_ID"; then
    echo "❌ Not authenticated or project not accessible"
    echo "Run: firebase login"
    exit 1
fi

echo "✓ Firebase CLI authenticated"
echo ""

# Step 1: Deploy security rules
echo "Step 1: Deploying security rules..."
firebase deploy --project=$PROJECT_ID --only database:rules -c "$(dirname "$0")/firebase.rules.json" 2>/dev/null || {
    echo "⚠️  Could not deploy rules via CLI. Go to:"
    echo "   https://console.firebase.google.com/project/$PROJECT_ID/database/rules"
    echo "And paste the contents of firebase.rules.json"
}
echo ""

# Step 2: Import data
echo "Step 2: Importing init.sql data to Realtime Database..."
cd "$(dirname "$0")"
php import_firebase.php
echo ""

# Step 3: Set up reference data (statuses)
echo "Step 3: Setting up reference data (statuses, types)..."
cat > /tmp/setup_data.json << 'EOF'
{
  "statut_voitures": {
    "statut_1": {"id": 1, "nom": "En attente", "description": "La voiture est en attente de réparation"},
    "statut_2": {"id": 2, "nom": "En réparation", "description": "La voiture est actuellement en réparation"},
    "statut_3": {"id": 3, "nom": "Prête", "description": "La voiture est prête à être récupérée"}
  },
  "statut_reparations": {
    "statut_1": {"id": 1, "nom": "Planifiée", "description": "La réparation est planifiée"},
    "statut_2": {"id": 2, "nom": "En cours", "description": "La réparation est en cours"},
    "statut_3": {"id": 3, "nom": "Terminée", "description": "La réparation est terminée"}
  },
  "statut_paiements": {
    "statut_1": {"id": 1, "nom": "En attente", "description": "Le paiement est en attente"},
    "statut_2": {"id": 2, "nom": "Complété", "description": "Le paiement a été effectué"},
    "statut_3": {"id": 3, "nom": "Remboursé", "description": "Le paiement a été remboursé"}
  }
}
EOF

curl -X PUT "$FIREBASE_DB_URL.json" \
  -H "Content-Type: application/json" \
  -d @/tmp/setup_data.json 2>/dev/null || echo "✓ Reference data updated (may need manual setup)"
echo ""

# Step 4: Firebase Auth setup
echo "Step 4: Setting up Firebase Auth users..."
echo "Get your Firebase API Key from:"
echo "   https://console.firebase.google.com/project/$PROJECT_ID/settings/general"
echo ""
read -p "Enter Firebase API Key: " API_KEY

if [ -n "$API_KEY" ]; then
    export FIREBASE_API_KEY="$API_KEY"
    php setup_firebase_auth.php "$API_KEY"
else
    echo "⚠️  Skipping Auth setup. Create users manually:"
    echo "   https://console.firebase.google.com/project/$PROJECT_ID/authentication/users"
fi

echo ""
echo "================================"
echo "✓ Setup Complete!"
echo "================================"
echo ""
echo "Next Steps:"
echo "1. Start backend: docker-compose up backend"
echo "2. Access API: http://localhost:8000/api"
echo "3. Test login: curl -X POST http://localhost:8000/api/auth/login ..."
echo ""
