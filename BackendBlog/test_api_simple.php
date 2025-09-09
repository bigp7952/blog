<?php

/**
 * Script de Test API Simple et Propre
 * Test complet de toutes les fonctionnalités de l'API Blog Personnel
 */

// Configuration
$baseUrl = 'http://localhost:8000/api/v1';
$testUser = [
    'name' => 'Test User API',
    'username' => 'testuser_' . time(),
    'email' => 'test_api_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$token = null;
$userId = null;
$articleId = null;

/**
 * Fonction pour faire des requêtes HTTP
 */
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ], $headers));
    
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'code' => 0,
            'body' => ['error' => 'CURL Error: ' . $error],
            'raw' => $response
        ];
    }
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

/**
 * Fonction pour afficher les résultats de test
 */
function printTestResult($testName, $result, $expectedSuccess = true) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "TEST: $testName\n";
    echo str_repeat("=", 60) . "\n";
    echo "Code HTTP: " . $result['code'] . "\n";
    echo "Réponse: " . json_encode($result['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    $success = ($result['code'] >= 200 && $result['code'] < 300);
    
    if ($expectedSuccess) {
        if ($success) {
            echo "✅ SUCCÈS\n";
            return true;
        } else {
            echo "❌ ÉCHEC\n";
            return false;
        }
    } else {
        if (!$success) {
            echo "✅ SUCCÈS (échec attendu)\n";
            return true;
        } else {
            echo "❌ ÉCHEC (succès inattendu)\n";
            return false;
        }
    }
}

echo "🚀 DÉBUT DES TESTS API BLOG PERSONNEL\n";
echo "URL de base: $baseUrl\n";
echo "Utilisateur de test: " . $testUser['email'] . "\n";

$testsResults = [];

// ========================================
// PHASE 1: TESTS PUBLICS
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "PHASE 1: TESTS DES ENDPOINTS PUBLICS\n";
echo str_repeat("=", 80) . "\n";

// Test 1: Articles publics
$result = makeRequest("$baseUrl/articles");
$testsResults['Articles publics'] = printTestResult("Articles publics", $result);

// Test 2: Test public
$result = makeRequest("$baseUrl/../test/public");
$testsResults['Test public'] = printTestResult("Test public", $result);

// ========================================
// PHASE 2: AUTHENTIFICATION
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "PHASE 2: TESTS D'AUTHENTIFICATION\n";
echo str_repeat("=", 80) . "\n";

// Test 3: Inscription
$result = makeRequest("$baseUrl/auth/register", 'POST', $testUser);
$testsResults['Inscription'] = printTestResult("Inscription utilisateur", $result);

if ($testsResults['Inscription'] && isset($result['body']['data']['token'])) {
    $token = $result['body']['data']['token'];
    $userId = $result['body']['data']['user']['id'] ?? null;
    echo "✅ Token récupéré: " . substr($token, 0, 20) . "...\n";
    echo "✅ ID utilisateur: $userId\n";
}

// Test 4: Connexion
$loginData = [
    'identifier' => $testUser['email'],
    'password' => $testUser['password']
];
$result = makeRequest("$baseUrl/auth/login", 'POST', $loginData);
$testsResults['Connexion'] = printTestResult("Connexion utilisateur", $result);

if ($testsResults['Connexion'] && isset($result['body']['data']['token'])) {
    $token = $result['body']['data']['token'];
    echo "✅ Token de connexion récupéré: " . substr($token, 0, 20) . "...\n";
}

// ========================================
// PHASE 3: TESTS AVEC AUTHENTIFICATION
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "PHASE 3: TESTS AVEC AUTHENTIFICATION\n";
echo str_repeat("=", 80) . "\n";

if ($token) {
    $headers = ['Authorization: Bearer ' . $token];
    
    // Test 5: Utilisateur authentifié
    $result = makeRequest("$baseUrl/auth/user", 'GET', null, $headers);
    $testsResults['Utilisateur authentifié'] = printTestResult("Utilisateur authentifié", $result);
    
    // Test 6: Profil utilisateur
    $result = makeRequest("$baseUrl/user/profile", 'GET', null, $headers);
    $testsResults['Profil utilisateur'] = printTestResult("Profil utilisateur", $result);
    
    // Test 7: Articles de l'utilisateur
    $result = makeRequest("$baseUrl/user/articles", 'GET', null, $headers);
    $testsResults['Articles utilisateur'] = printTestResult("Articles utilisateur", $result);
    
    // Test 8: Amis de l'utilisateur
    $result = makeRequest("$baseUrl/friends", 'GET', null, $headers);
    $testsResults['Amis utilisateur'] = printTestResult("Amis utilisateur", $result);
    
    // Test 9: Notifications
    $result = makeRequest("$baseUrl/notifications", 'GET', null, $headers);
    $testsResults['Notifications'] = printTestResult("Notifications", $result);
    
    // Test 10: Création d'un article
    $articleData = [
        'title' => 'Article de Test API',
        'excerpt' => 'Ceci est un article de test créé via l\'API',
        'content' => 'Contenu complet de l\'article de test. Cet article a été créé pour tester les fonctionnalités de l\'API.',
        'status' => 'published',
        'visibility' => 'public',
        'tags' => ['Test', 'API', 'Blog']
    ];
    $result = makeRequest("$baseUrl/articles", 'POST', $articleData, $headers);
    $testsResults['Création article'] = printTestResult("Création d'un article", $result);
    
    if ($testsResults['Création article'] && isset($result['body']['data']['id'])) {
        $articleId = $result['body']['data']['id'];
        echo "✅ Article créé avec l'ID: $articleId\n";
    }
    
    // Test 11: Like d'un article (si article créé)
    if ($articleId) {
        $result = makeRequest("$baseUrl/articles/$articleId/like", 'POST', null, $headers);
        $testsResults['Like article'] = printTestResult("Like d'un article", $result);
        
        // Test 12: Signet d'un article
        $result = makeRequest("$baseUrl/articles/$articleId/bookmark", 'POST', null, $headers);
        $testsResults['Signet article'] = printTestResult("Signet d'un article", $result);
    }
}

// ========================================
// PHASE 4: TESTS DE SÉCURITÉ
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "PHASE 4: TESTS DE SÉCURITÉ\n";
echo str_repeat("=", 80) . "\n";

// Test 13: Accès sans token (doit échouer)
$result = makeRequest("$baseUrl/auth/user", 'GET');
$testsResults['Sécurité sans token'] = printTestResult("Accès sans token (doit échouer)", $result, false);

// Test 14: Accès avec token invalide (doit échouer)
$headers = ['Authorization: Bearer token_invalide_12345'];
$result = makeRequest("$baseUrl/auth/user", 'GET', null, $headers);
$testsResults['Sécurité token invalide'] = printTestResult("Accès avec token invalide (doit échouer)", $result, false);

// ========================================
// PHASE 5: DÉCONNEXION
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "PHASE 5: DÉCONNEXION\n";
echo str_repeat("=", 80) . "\n";

if ($token) {
    $headers = ['Authorization: Bearer ' . $token];
    $result = makeRequest("$baseUrl/auth/logout", 'POST', null, $headers);
    $testsResults['Déconnexion'] = printTestResult("Déconnexion", $result);
}

// ========================================
// RÉSUMÉ DES TESTS
// ========================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "RÉSUMÉ DES TESTS\n";
echo str_repeat("=", 80) . "\n";

$totalTests = count($testsResults);
$passedTests = array_sum($testsResults);

echo "Résultats par test:\n";
foreach ($testsResults as $test => $passed) {
    echo ($passed ? "✅" : "❌") . " $test\n";
}

echo "\nRésultat global: $passedTests/$totalTests tests réussis\n";

if ($passedTests === $totalTests) {
    echo "🎉 TOUS LES TESTS SONT PASSÉS!\n";
    echo "✅ Votre API fonctionne parfaitement!\n";
} else {
    echo "⚠️  Certains tests ont échoué.\n";
    echo "Vérifiez la configuration et les logs.\n";
}

echo "\n🏁 Tests terminés!\n";
echo "\nPour exécuter ce script:\n";
echo "1. Assurez-vous que votre serveur Laravel est démarré: php artisan serve\n";
echo "2. Exécutez le script: php test_api_simple.php\n";
echo "3. Vérifiez que la base de données est configurée et migrée\n";
