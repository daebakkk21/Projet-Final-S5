#!/usr/bin/env node
// backend/scripts/import_init.js
// Usage: node import_init.js /path/to/init.sql /path/to/serviceAccountKey.json

const fs = require('fs');
const path = require('path');
const admin = require('firebase-admin');

if (process.argv.length < 4) {
  console.error('Usage: node import_init.js /path/to/init.sql /path/to/serviceAccountKey.json [databaseURL]');
  process.exit(1);
}

const sqlPath = process.argv[2];
const serviceAccountPath = process.argv[3];
const dbUrl = process.argv[4] || process.env.FIREBASE_DB_URL;

if (!fs.existsSync(sqlPath)) { console.error('init.sql not found:', sqlPath); process.exit(2); }
if (!fs.existsSync(serviceAccountPath)) { console.error('serviceAccountKey.json not found:', serviceAccountPath); process.exit(2); }

const serviceAccount = require(path.resolve(serviceAccountPath));

if (!dbUrl) { console.error('databaseURL not provided (arg3 or FIREBASE_DB_URL env)'); process.exit(2); }

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: dbUrl.replace(/\/+$/, '')
});

const db = admin.database();

const content = fs.readFileSync(sqlPath, 'utf8');

// Basic parser for INSERT statements in the provided init.sql style
const insertRegex = /INSERT INTO\s+([`"]?)(\w+)\1\s*\(([^)]+)\)\s*VALUES\s*((?:\([^;]+\))(?:\s*,\s*\([^;]+\))*)\s*;/gmi;

function parseValuesTuple(tuple) {
  // tuple like ("demo_user_123", "Demo", "User", "demo@...")
  const vals = [];
  let cur = '';
  let inQuote = false;
  let quoteChar = null;
  for (let i = 0; i < tuple.length; i++) {
    const ch = tuple[i];
    if (!inQuote && (ch === '"' || ch === '\'')) {
      inQuote = true; quoteChar = ch; cur = '';
      continue;
    }
    if (inQuote && ch === quoteChar) {
      vals.push(cur);
      inQuote = false; quoteChar = null;
      // skip until comma or end
      while (i + 1 < tuple.length && /[ ,\t]/.test(tuple[i+1])) i++;
      continue;
    }
    if (!inQuote && /[0-9\-]/.test(ch) && cur === '') {
      // number or NULL handling: consume until comma
      let j = i;
      let num = '';
      while (j < tuple.length && /[^,)]/.test(tuple[j])) { num += tuple[j]; j++; }
      i = j - 1;
      const t = num.trim();
      if (/^NULL$/i.test(t)) vals.push(null); else if (/^[0-9\.\-]+$/.test(t)) vals.push(Number(t)); else vals.push(t.replace(/^\'|\'$/g, ''));
      continue;
    }
    if (inQuote) cur += ch;
  }
  return vals;
}

async function main() {
  const operations = [];
  let m;
  while ((m = insertRegex.exec(content)) !== null) {
    const table = m[2];
    const cols = m[3].split(',').map(s => s.trim().replace(/[`"\s]/g, ''));
    const tuplesRaw = m[4];
    const tupleMatches = [...tuplesRaw.matchAll(/\(([^)]+)\)/g)];
    for (const t of tupleMatches) {
      const tuple = t[1];
      const values = parseValuesTuple(tuple);
      const obj = {};
      for (let i = 0; i < cols.length; i++) obj[cols[i]] = values[i] === undefined ? null : values[i];
      operations.push({ table, obj });
    }
  }

  if (operations.length === 0) { console.log('No INSERT statements parsed.'); process.exit(0); }

  const results = { createdAuth: [], dbWrites: [] };

  for (const op of operations) {
    const { table, obj } = op;
    if (table.toLowerCase() === 'clients') {
      // prefer firebase_uid as uid
      let uid = obj.firebase_uid || ('user_' + Math.random().toString(36).slice(2, 10));
      const email = obj.email || null;
      const displayName = [obj.prenom, obj.nom].filter(Boolean).join(' ');
      const password = Math.random().toString(36).slice(2, 10) + 'A1!';
      try {
        await admin.auth().createUser({ uid, email: email || undefined, displayName: displayName || undefined, password });
      } catch (e) {
        if (e.code === 'auth/email-already-exists' || e.code === 'auth/uid-already-exists') {
          // ignore
        } else {
          console.warn('Auth create user warning:', e.message);
        }
      }
      // write to RTDB at /clients/{uid}
      const writeObj = Object.assign({}, obj, { created_at: obj.created_at || new Date().toISOString() });
      await db.ref('clients/' + uid).set(writeObj);
      results.createdAuth.push({ uid, email, password });
      results.dbWrites.push({ path: 'clients/' + uid, data: writeObj });
    } else if (table.toLowerCase() === 'admins') {
      const email = obj.email || ('admin_' + Math.random().toString(36).slice(2, 8) + '@example.com');
      const uid = 'admin_' + (email.replace(/[@\.]/g, '_'));
      const displayName = [obj.prenom, obj.nom].filter(Boolean).join(' ');
      const password = Math.random().toString(36).slice(2, 10) + 'A1!';
      try {
        await admin.auth().createUser({ uid, email, displayName, password });
      } catch (e) {
        if (e.code !== 'auth/email-already-exists' && e.code !== 'auth/uid-already-exists') console.warn('Auth create admin warning:', e.message);
      }
      // set admin custom claim
      try { await admin.auth().setCustomUserClaims(uid, { admin: true }); } catch (e) { console.warn('setCustomUserClaims:', e.message); }
      const writeObj = { nom: obj.nom || '', prenom: obj.prenom || '', email: email, created_at: new Date().toISOString() };
      await db.ref('admins/' + uid).set(writeObj);
      results.createdAuth.push({ uid, email, password, admin: true });
      results.dbWrites.push({ path: 'admins/' + uid, data: writeObj });
    } else {
      // push generic table entries under their table name
      const ref = db.ref(table);
      const pushRef = await ref.push(obj);
      results.dbWrites.push({ path: table + '/' + pushRef.key, data: obj });
    }
  }

  // write summary to file
  const outPath = path.join(__dirname, 'import_result.json');
  fs.writeFileSync(outPath, JSON.stringify(results, null, 2), 'utf8');
  console.log('Import completed. Summary written to', outPath);
  console.log('Created users (email/password):');
  results.createdAuth.forEach(u => console.log(u));
}

main().catch(err => { console.error(err); process.exit(10); });
