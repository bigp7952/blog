# Guide d'Intégration Frontend-Backend

## Problème Résolu

Les erreurs `JSON.parse: unexpected character at line 1 column 1` étaient causées par :

1. **URL API incorrecte** : Le frontend utilisait `/api` au lieu de `/api/v1`
2. **Endpoints non alignés** : Les endpoints du frontend ne correspondaient pas à ceux du backend
3. **Gestion d'erreurs insuffisante** : Pas de vérification du Content-Type

## Solutions Implémentées

### 1. Correction de l'URL API
```typescript
// Avant
const API_BASE_URL = 'http://localhost:8000/api';

// Après
const API_BASE_URL = 'http://localhost:8000/api/v1';
```

### 2. Amélioration de la Gestion des Erreurs
```typescript
// Vérification du Content-Type avant parsing JSON
const contentType = response.headers.get('content-type');
if (!contentType || !contentType.includes('application/json')) {
  const text = await response.text();
  console.error('Non-JSON response:', text);
  throw new Error('Réponse non-JSON reçue');
}
```

### 3. Configuration Centralisée
Création de `src/config/api.ts` avec :
- Configuration centralisée des URLs
- Messages d'erreur standardisés
- Endpoints définis
- Types TypeScript

### 4. Endpoints Corrigés
```typescript
// Authentification
POST /api/v1/auth/register
POST /api/v1/auth/login
GET  /api/v1/auth/user
POST /api/v1/auth/logout

// Articles
GET  /api/v1/articles
POST /api/v1/articles
PUT  /api/v1/articles/{id}
DELETE /api/v1/articles/{id}

// Amis
GET  /api/v1/friends
POST /api/v1/friends
POST /api/v1/friends/{id}/accept
POST /api/v1/friends/{id}/reject
DELETE /api/v1/friends/{id}
```

## Tests de Validation

### 1. Test Backend
```bash
cd BackendBlog
php test_frontend_integration.php
```

### 2. Test Frontend
```bash
cd Projet-React_Vacances/ignite-chronicle
npm run dev
```

## Vérifications Importantes

### 1. Serveur Backend Démarré
```bash
cd BackendBlog
php artisan serve
# Doit être accessible sur http://localhost:8000
```

### 2. Base de Données Configurée
```bash
cd BackendBlog
php artisan migrate
php artisan db:seed
```

### 3. Variables d'Environnement
Créer `.env.local` dans le frontend :
```env
VITE_API_URL=http://localhost:8000/api/v1
```

## Structure des Réponses API

### Succès
```json
{
  "success": true,
  "message": "Message de succès",
  "data": { ... }
}
```

### Erreur
```json
{
  "success": false,
  "message": "Message d'erreur",
  "error": "Détails de l'erreur"
}
```

## Endpoints Principaux

### Articles Publics
```bash
GET http://localhost:8000/api/v1/articles
```

### Inscription
```bash
POST http://localhost:8000/api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Connexion
```bash
POST http://localhost:8000/api/v1/auth/login
Content-Type: application/json

{
  "identifier": "john@example.com",
  "password": "password123"
}
```

### Utilisateur Authentifié
```bash
GET http://localhost:8000/api/v1/auth/user
Authorization: Bearer YOUR_TOKEN
```

## Dépannage

### Erreur "JSON.parse: unexpected character"
1. Vérifier que le serveur backend est démarré
2. Vérifier l'URL dans le frontend (`/api/v1`)
3. Vérifier les logs du navigateur pour voir la réponse brute

### Erreur 404
1. Vérifier que les routes sont correctes
2. Vérifier que le serveur Laravel est démarré
3. Vérifier les routes avec `php artisan route:list --path=api`

### Erreur 500
1. Vérifier les logs Laravel : `tail -f storage/logs/laravel.log`
2. Vérifier la configuration de la base de données
3. Vérifier que les migrations sont exécutées

### Erreur CORS
1. Vérifier la configuration CORS dans `config/cors.php`
2. S'assurer que `supports_credentials` est activé

## Commandes Utiles

### Backend
```bash
# Démarrer le serveur
php artisan serve

# Vérifier les routes
php artisan route:list --path=api

# Nettoyer les caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Vérifier les migrations
php artisan migrate:status
```

### Frontend
```bash
# Démarrer le serveur de développement
npm run dev

# Vérifier les variables d'environnement
npm run build

# Nettoyer les caches
rm -rf node_modules/.vite
```

## Résultat Attendu

Après ces corrections, le frontend devrait :
1. ✅ Se connecter correctement à l'API
2. ✅ Afficher les articles publics
3. ✅ Permettre l'inscription/connexion
4. ✅ Afficher le dashboard utilisateur
5. ✅ Gérer les amis et notifications

## Support

Si des erreurs persistent :
1. Vérifier les logs du navigateur (F12)
2. Vérifier les logs Laravel
3. Tester les endpoints avec curl/Postman
4. Vérifier la configuration réseau/firewall
