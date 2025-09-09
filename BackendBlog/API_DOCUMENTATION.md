# Documentation API Blog Personnel

## Vue d'ensemble

Cette API REST fournit toutes les fonctionnalités nécessaires pour un blog personnel avec système d'amis, commentaires et notifications.

**Base URL:** `http://localhost:8000/api/v1`

## Authentification

L'API utilise Laravel Sanctum pour l'authentification par token Bearer.

### Headers requis pour les routes protégées:
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
Accept: application/json
```

## Structure des Réponses

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
  "error": "Détails de l'erreur (optionnel)",
  "errors": { ... } // Pour les erreurs de validation
}
```

---

## 🔐 Authentification

### POST /auth/register
Inscription d'un nouvel utilisateur

**Body:**
```json
{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Réponse:**
```json
{
  "success": true,
  "message": "Utilisateur créé avec succès",
  "data": {
    "user": { ... },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### POST /auth/login
Connexion d'un utilisateur

**Body:**
```json
{
  "identifier": "john@example.com", // email, username ou phone
  "password": "password123"
}
```

### GET /auth/user
Récupération des informations de l'utilisateur connecté

**Headers:** `Authorization: Bearer TOKEN`

### POST /auth/logout
Déconnexion de l'utilisateur

**Headers:** `Authorization: Bearer TOKEN`

### POST /auth/refresh
Rafraîchissement du token

**Headers:** `Authorization: Bearer TOKEN`

---

## 👤 Gestion des Utilisateurs

### GET /users/{id}
Affichage d'un utilisateur public

### GET /users/{id}/articles
Articles publics d'un utilisateur

### GET /user/profile
Profil de l'utilisateur connecté

**Headers:** `Authorization: Bearer TOKEN`

### PUT /user/profile
Mise à jour du profil

**Headers:** `Authorization: Bearer TOKEN`

**Body:**
```json
{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "bio": "Ma biographie",
  "avatar": "url_de_l_avatar"
}
```

### PUT /user/password
Mise à jour du mot de passe

**Headers:** `Authorization: Bearer TOKEN`

**Body:**
```json
{
  "current_password": "ancien_mot_de_passe",
  "password": "nouveau_mot_de_passe",
  "password_confirmation": "nouveau_mot_de_passe"
}
```

### GET /user/articles
Articles de l'utilisateur connecté

**Headers:** `Authorization: Bearer TOKEN`

**Query Parameters:**
- `status`: draft, published, all
- `visibility`: public, private, all
- `search`: terme de recherche
- `limit`: nombre d'articles par page

### GET /user/bookmarks
Signets de l'utilisateur connecté

**Headers:** `Authorization: Bearer TOKEN`

---

## 📝 Gestion des Articles

### GET /articles
Liste des articles publics

**Query Parameters:**
- `tag`: filtre par tag
- `author`: filtre par auteur
- `search`: terme de recherche
- `limit`: nombre d'articles par page

### GET /articles/{id}
Affichage d'un article

### POST /articles
Création d'un article

**Headers:** `Authorization: Bearer TOKEN`

**Body:**
```json
{
  "title": "Titre de l'article",
  "excerpt": "Résumé de l'article",
  "content": "Contenu complet de l'article",
  "status": "published", // draft ou published
  "visibility": "public", // public ou private
  "comments_enabled": true,
  "featured": false,
  "tags": ["Tag1", "Tag2", "Tag3"],
  "published_at": "2024-01-01 12:00:00" // optionnel
}
```

### PUT /articles/{id}
Mise à jour d'un article

**Headers:** `Authorization: Bearer TOKEN`

### DELETE /articles/{id}
Suppression d'un article

**Headers:** `Authorization: Bearer TOKEN`

### POST /articles/{id}/like
Like d'un article

**Headers:** `Authorization: Bearer TOKEN`

### POST /articles/{id}/unlike
Unlike d'un article

**Headers:** `Authorization: Bearer TOKEN`

### POST /articles/{id}/bookmark
Ajouter un article aux signets

**Headers:** `Authorization: Bearer TOKEN`

### POST /articles/{id}/unbookmark
Retirer un article des signets

**Headers:** `Authorization: Bearer TOKEN`

---

## 💬 Gestion des Commentaires

### GET /articles/{articleId}/comments
Liste des commentaires d'un article

### POST /articles/{articleId}/comments
Création d'un commentaire

**Headers:** `Authorization: Bearer TOKEN`

**Body:**
```json
{
  "content": "Contenu du commentaire",
  "parent_id": 123 // optionnel, pour les réponses
}
```

### GET /comments/{id}
Affichage d'un commentaire

### PUT /comments/{id}
Mise à jour d'un commentaire

**Headers:** `Authorization: Bearer TOKEN`

### DELETE /comments/{id}
Suppression d'un commentaire

**Headers:** `Authorization: Bearer TOKEN`

### POST /comments/{id}/like
Like d'un commentaire

**Headers:** `Authorization: Bearer TOKEN`

### POST /comments/{id}/unlike
Unlike d'un commentaire

**Headers:** `Authorization: Bearer TOKEN`

---

## 👥 Gestion des Amis

### GET /friends
Liste des amis de l'utilisateur connecté

**Headers:** `Authorization: Bearer TOKEN`

### POST /friends
Envoi d'une demande d'ami

**Headers:** `Authorization: Bearer TOKEN`

**Body:**
```json
{
  "friend_id": 123
}
```

### GET /friends/search
Recherche d'utilisateurs

**Headers:** `Authorization: Bearer TOKEN`

**Query Parameters:**
- `query`: terme de recherche (nom, username, email)

### GET /friends/requests
Demandes d'ami reçues

**Headers:** `Authorization: Bearer TOKEN`

### POST /friends/{id}/accept
Accepter une demande d'ami

**Headers:** `Authorization: Bearer TOKEN`

### POST /friends/{id}/reject
Rejeter une demande d'ami

**Headers:** `Authorization: Bearer TOKEN`

### DELETE /friends/{id}
Supprimer un ami

**Headers:** `Authorization: Bearer TOKEN`

---

## 🔔 Gestion des Notifications

### GET /notifications
Liste des notifications

**Headers:** `Authorization: Bearer TOKEN`

### GET /notifications/unread-count
Nombre de notifications non lues

**Headers:** `Authorization: Bearer TOKEN`

### POST /notifications/mark-all-read
Marquer toutes les notifications comme lues

**Headers:** `Authorization: Bearer TOKEN`

### POST /notifications/{id}/mark-read
Marquer une notification comme lue

**Headers:** `Authorization: Bearer TOKEN`

### GET /notifications/{id}
Affichage d'une notification

**Headers:** `Authorization: Bearer TOKEN`

### DELETE /notifications/{id}
Suppression d'une notification

**Headers:** `Authorization: Bearer TOKEN`

---

## 🧪 Endpoints de Test

### GET /test/public
Test d'endpoint public

### GET /test/debug
Informations de debug

### GET /test/protected
Test d'endpoint protégé

**Headers:** `Authorization: Bearer TOKEN`

---

## 🔧 Administration

### GET /admin/users
Liste des utilisateurs (admin)

**Headers:** `Authorization: Bearer TOKEN`

### POST /admin/users
Création d'un utilisateur (admin)

**Headers:** `Authorization: Bearer TOKEN`

### DELETE /admin/users/{id}
Suppression d'un utilisateur (admin)

**Headers:** `Authorization: Bearer TOKEN`

---

## Codes de Statut HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête incorrecte
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Non trouvé
- `422` - Erreur de validation
- `500` - Erreur serveur

---

## Exemples d'Utilisation

### Flux complet d'authentification

```bash
# 1. Inscription
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# 2. Connexion
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "john@example.com",
    "password": "password123"
  }'

# 3. Utilisation du token
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/v1/auth/user
```

### Création d'un article

```bash
curl -X POST http://localhost:8000/api/v1/articles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Mon Premier Article",
    "excerpt": "Résumé de mon article",
    "content": "Contenu complet de mon article...",
    "status": "published",
    "visibility": "public",
    "tags": ["Blog", "Premier Article"]
  }'
```

---

## Configuration

### Variables d'environnement importantes

```env
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000
```

### Installation et démarrage

```bash
# Installation des dépendances
composer install

# Configuration de la base de données
php artisan migrate

# Démarrage du serveur
php artisan serve
```

### Test de l'API

```bash
# Exécution du script de test
php test_api_simple.php
```

---

## Support

Pour toute question ou problème, consultez les logs Laravel dans `storage/logs/laravel.log`.
