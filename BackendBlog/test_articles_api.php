<?php

// Test de l'API Articles
$baseUrl = 'http://localhost:8000/api/v1';

function curl_request($method, $endpoint, $data = null, $token = null) {
    global $baseUrl;
    
    $url = $baseUrl . $endpoint;
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => 0];
    }
    
    return [
        'data' => $response,
        'http_code' => $httpCode,
        'decoded' => json_decode($response, true)
    ];
}

function handle_response($response, $testName) {
    echo "=== $testName ===\n";
    echo "Code HTTP: " . $response['http_code'] . "\n";
    
    if (isset($response['error'])) {
        echo "❌ Erreur cURL: " . $response['error'] . "\n";
        return false;
    }
    
    if ($response['http_code'] >= 200 && $response['http_code'] < 300) {
        echo "✅ Succès\n";
        if ($response['decoded']) {
            echo "Données: " . json_encode($response['decoded'], JSON_PRETTY_PRINT) . "\n";
        }
        return true;
    } else {
        echo "❌ Échec\n";
        echo "Réponse: " . $response['data'] . "\n";
        return false;
    }
    echo "\n";
}

echo "🧪 Test de l'API Articles\n";
echo "========================\n\n";

// Test 1: Articles publics (sans authentification)
echo "Test 1: Articles publics\n";
$response = curl_request('GET', '/articles');
handle_response($response, 'Articles publics');

// Test 2: Inscription d'un utilisateur de test
echo "Test 2: Inscription utilisateur\n";
$registerData = [
    'name' => 'Test Articles',
    'username' => 'test_articles_' . time(),
    'email' => 'test_articles_' . time() . '@example.com',
    'password' => 'password',
    'password_confirmation' => 'password',
];

$response = curl_request('POST', '/auth/register', $registerData);
$token = null;

if ($response['http_code'] === 200 && isset($response['decoded']['data']['token'])) {
    $token = $response['decoded']['data']['token'];
    echo "✅ Inscription réussie\n";
    echo "Token: " . substr($token, 0, 20) . "...\n";
} else {
    echo "❌ Échec de l'inscription\n";
    echo "Réponse: " . $response['data'] . "\n";
    // Essayer de continuer avec un token de test
    echo "🔄 Tentative de connexion avec un utilisateur existant...\n";
    $loginData = [
        'identifier' => 'test_articles_1757414539@example.com',
        'password' => 'password'
    ];
    $loginResponse = curl_request('POST', '/auth/login', $loginData);
    if ($loginResponse['http_code'] === 200 && isset($loginResponse['decoded']['data']['token'])) {
        $token = $loginResponse['decoded']['data']['token'];
        echo "✅ Connexion réussie\n";
        echo "Token: " . substr($token, 0, 20) . "...\n";
    } else {
        echo "❌ Échec de la connexion\n";
        exit(1);
    }
}

echo "\n";

// Test 3: Articles de l'utilisateur (avec authentification)
echo "Test 3: Articles de l'utilisateur\n";
$response = curl_request('GET', '/user/articles', null, $token);
handle_response($response, 'Articles utilisateur');

// Test 4: Création d'un article de test
echo "Test 4: Création d'un article\n";
$articleData = [
    'title' => 'Article de Test',
    'excerpt' => 'Ceci est un article de test créé automatiquement.',
    'content' => 'Contenu complet de l\'article de test. Cet article a été créé pour tester l\'API.',
    'status' => 'published',
    'visibility' => 'public',
    'tags' => ['test', 'api']
];

$response = curl_request('POST', '/articles', $articleData, $token);
$articleId = null;

if ($response['http_code'] === 200 && isset($response['decoded']['data']['id'])) {
    $articleId = $response['decoded']['data']['id'];
    echo "✅ Article créé avec succès\n";
    echo "ID de l'article: " . $articleId . "\n";
} else {
    echo "❌ Échec de la création de l'article\n";
    echo "Réponse: " . $response['data'] . "\n";
}

echo "\n";

// Test 5: Vérifier que l'article apparaît dans la liste
echo "Test 5: Vérification de la liste des articles\n";
$response = curl_request('GET', '/user/articles', null, $token);
handle_response($response, 'Liste des articles après création');

// Test 6: Récupérer l'article par ID
if ($articleId) {
    echo "Test 6: Récupération de l'article par ID\n";
    $response = curl_request('GET', '/articles/' . $articleId, null, $token);
    handle_response($response, 'Article par ID');
}

echo "\n🎉 Tests terminés !\n";
echo "Si tous les tests passent, l'API Articles fonctionne correctement.\n";
echo "Vous pouvez maintenant tester la page frontend.\n";
