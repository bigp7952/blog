# Guide de Test - Authentification Frontend-Backend

## Problème Résolu

Les erreurs 401 Unauthorized étaient causées par l'absence d'un système d'authentification complet dans le frontend. J'ai implémenté :

1. **Contexte d'authentification** (`AuthContext`)
2. **Composants de connexion/inscription**
3. **Protection des routes**
4. **Gestion des tokens**

## Solutions Implémentées

### 1. Contexte d'Authentification
- `src/contexts/AuthContext.tsx` - Gestion globale de l'état d'authentification
- Chargement automatique du token depuis localStorage
- Méthodes de connexion, inscription et déconnexion

### 2. Composants d'Authentification
- `src/components/auth/LoginForm.tsx` - Formulaire de connexion
- `src/components/auth/RegisterForm.tsx` - Formulaire d'inscription
- `src/components/auth/ProtectedRoute.tsx` - Protection des routes
- `src/components/auth/AuthNav.tsx` - Navigation avec authentification

### 3. Pages d'Authentification
- `src/pages/auth/Login.tsx` - Page de connexion
- `src/pages/auth/Register.tsx` - Page d'inscription

### 4. Gestion des Erreurs
- Gestion gracieuse des erreurs d'authentification
- Messages d'erreur explicites
- Fallback pour les requêtes échouées

## Tests à Effectuer

### 1. Test d'Inscription
1. Aller sur `/register`
2. Remplir le formulaire d'inscription
3. Vérifier que l'utilisateur est automatiquement connecté
4. Vérifier la redirection vers le dashboard

### 2. Test de Connexion
1. Aller sur `/login`
2. Utiliser les identifiants créés
3. Vérifier la connexion et la redirection

### 3. Test des Routes Protégées
1. Essayer d'accéder à `/dashboard` sans être connecté
2. Vérifier la redirection vers la page de connexion
3. Se connecter et vérifier l'accès au dashboard

### 4. Test des Appels API
1. Aller sur la page de test API
2. Vérifier que les endpoints protégés fonctionnent
3. Tester la déconnexion

## Structure des Routes

### Routes Publiques
- `/` - Page d'accueil
- `/article/:id` - Article public
- `/login` - Connexion (redirige si connecté)
- `/register` - Inscription (redirige si connecté)

### Routes Protégées
- `/dashboard` - Tableau de bord
- `/dashboard/articles` - Gestion des articles
- `/dashboard/write` - Éditeur d'articles
- `/dashboard/friends` - Liste des amis
- `/dashboard/find-friends` - Recherche d'amis
- `/dashboard/profile` - Profil utilisateur

## Flux d'Authentification

### 1. Inscription
```
POST /api/v1/auth/register
→ Token généré
→ Stockage dans localStorage
→ Redirection vers dashboard
```

### 2. Connexion
```
POST /api/v1/auth/login
→ Token généré
→ Stockage dans localStorage
→ Redirection vers dashboard
```

### 3. Requêtes Authentifiées
```
Headers: Authorization: Bearer TOKEN
→ Vérification du token
→ Accès aux données utilisateur
```

### 4. Déconnexion
```
POST /api/v1/auth/logout
→ Suppression du token
→ Nettoyage du localStorage
→ Redirection vers page d'accueil
```

## Vérifications Importantes

### 1. Serveur Backend
```bash
cd BackendBlog
php artisan serve
# Doit être accessible sur http://localhost:8000
```

### 2. Base de Données
```bash
cd BackendBlog
php artisan migrate
php artisan db:seed
```

### 3. Frontend
```bash
cd Projet-React_Vacances/ignite-chronicle
npm run dev
# Doit être accessible sur http://localhost:5173
```

## Messages d'Erreur Courants

### 401 Unauthorized
- **Cause** : Token manquant ou invalide
- **Solution** : Se reconnecter

### 422 Validation Error
- **Cause** : Données du formulaire invalides
- **Solution** : Vérifier les champs requis

### 500 Server Error
- **Cause** : Erreur serveur
- **Solution** : Vérifier les logs Laravel

## Commandes de Dépannage

### Backend
```bash
# Vérifier les routes
php artisan route:list --path=api

# Vérifier les logs
tail -f storage/logs/laravel.log

# Nettoyer les caches
php artisan config:clear
php artisan route:clear
```

### Frontend
```bash
# Vérifier les erreurs dans la console
# F12 → Console

# Vérifier le localStorage
# F12 → Application → Local Storage

# Redémarrer le serveur de développement
npm run dev
```

## Test Complet

### 1. Inscription d'un Utilisateur
1. Aller sur `http://localhost:5173/register`
2. Remplir le formulaire
3. Vérifier la redirection vers `/dashboard`

### 2. Test du Dashboard
1. Vérifier que les données se chargent
2. Tester la navigation
3. Vérifier l'affichage des informations utilisateur

### 3. Test de Déconnexion
1. Cliquer sur "Déconnexion"
2. Vérifier la redirection vers la page d'accueil
3. Essayer d'accéder à `/dashboard` (doit rediriger vers login)

### 4. Test de Reconnexion
1. Aller sur `/login`
2. Utiliser les mêmes identifiants
3. Vérifier la reconnexion

## Résultat Attendu

Après ces corrections, vous devriez avoir :
- ✅ Système d'authentification complet
- ✅ Protection des routes
- ✅ Gestion des tokens
- ✅ Interface de connexion/inscription
- ✅ Dashboard fonctionnel
- ✅ Gestion des erreurs d'authentification

## Support

Si des erreurs persistent :
1. Vérifier que le backend est démarré
2. Vérifier les logs Laravel
3. Vérifier la console du navigateur
4. Vérifier le localStorage pour le token
5. Tester les endpoints avec curl/Postman
