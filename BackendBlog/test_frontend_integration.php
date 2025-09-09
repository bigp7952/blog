<?php

/**
 * Script de test pour vérifier l'intégration frontend-backend
 */

$baseUrl = 'http://localhost:8000/api/v1';

/**
 * Test d'un endpoint simple
 */
function testEndpoint($url, $name) {
    echo "\n=== Test: $name ===\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    echo "Code HTTP: $httpCode\n";
    echo "Content-Type: $contentType\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "✅ Succès - Structure JSON valide\n";
            echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
            if (isset($data['data'])) {
                if (is_array($data['data'])) {
                    echo "Données: " . count($data['data']) . " éléments\n";
                } else {
                    echo "Données: " . gettype($data['data']) . "\n";
                }
            }
        } else {
            echo "❌ Échec - Structure JSON invalide\n";
            echo "Réponse: " . substr($response, 0, 200) . "...\n";
        }
    } else {
        echo "❌ Échec - Code HTTP: $httpCode\n";
        echo "Réponse: " . substr($response, 0, 200) . "...\n";
    }
}

echo "🧪 Test d'intégration Frontend-Backend\n";
echo "Base URL: $baseUrl\n";

// Tests des endpoints principaux
testEndpoint("$baseUrl/articles", "Articles publics");
testEndpoint("$baseUrl/../test/public", "Test public");

// Test d'authentification (inscription)
echo "\n=== Test: Inscription ===\n";
$testUser = [
    'name' => 'Test Frontend',
    'username' => 'testfrontend_' . time(),
    'email' => 'testfrontend_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/register");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testUser));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";

if ($httpCode === 201) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "✅ Inscription réussie\n";
        $token = $data['data']['token'] ?? null;
        if ($token) {
            echo "Token: " . substr($token, 0, 20) . "...\n";
            
            // Test avec token
            echo "\n=== Test: Utilisateur authentifié ===\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$baseUrl/auth/user");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Code HTTP: $httpCode\n";
            
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if ($data && isset($data['success']) && $data['success']) {
                    echo "✅ Authentification réussie\n";
                    echo "Utilisateur: " . ($data['data']['name'] ?? 'N/A') . "\n";
                } else {
                    echo "❌ Échec de l'authentification\n";
                }
            } else {
                echo "❌ Échec - Code HTTP: $httpCode\n";
            }
        }
    } else {
        echo "❌ Échec de l'inscription\n";
        echo "Réponse: " . substr($response, 0, 200) . "...\n";
    }
} else {
    echo "❌ Échec de l'inscription - Code HTTP: $httpCode\n";
    echo "Réponse: " . substr($response, 0, 200) . "...\n";
}

echo "\n🏁 Tests terminés!\n";
echo "\nSi tous les tests passent, l'API backend est prête pour le frontend.\n";
echo "Assurez-vous que le frontend utilise l'URL: http://localhost:8000/api/v1\n";
