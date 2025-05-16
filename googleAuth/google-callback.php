<?php
session_start([
    'cookie_lifetime' => 86400 * 30,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            throw new Exception($token['error_description'] ?? 'Google authentication failed');
        }

        $client->setAccessToken($token);

        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        // Split name
        $full_name = $userInfo->name;
        $name_parts = explode(" ", $full_name, 2);
        $firstname = $name_parts[0];
        $lastname = isset($name_parts[1]) ? $name_parts[1] : '';

        try {
            $stmt = $pdo->prepare("SELECT id, role FROM user WHERE email = ?");
            $stmt->execute([$userInfo->email]);
            $user = $stmt->fetch();

            if ($user) {
                // ✅ Existing user: Update info, retain original role
                $updateStmt = $pdo->prepare("UPDATE user SET 
                    firstname = ?,
                    lastname = ?,
                    name = ?,
                    last_login = NOW(),
                    login_count = IFNULL(login_count, 0) + 1,
                    profile_picture = COALESCE(profile_picture, ?)
                    WHERE email = ?");
                $updateStmt->execute([
                    $firstname,
                    $lastname,
                    $userInfo->name,
                    $userInfo->picture ?? null,
                    $userInfo->email
                ]);
                $_SESSION['user_id'] = $user['id'];
            } else {
                // ✅ New user: Insert with default role "teacher"
                $insertStmt = $pdo->prepare("INSERT INTO user 
                    (email, firstname, lastname, name, profile_picture, role, login_method, created_at, last_login, login_count) 
                    VALUES (?, ?, ?, ?, ?, 'teacher', 'google', NOW(), NOW(), 1)");
                $insertStmt->execute([
                    $userInfo->email,
                    $firstname,
                    $lastname,
                    $userInfo->name,
                    $userInfo->picture ?? null
                ]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
            }

            $_SESSION = [
                'logged_in' => true,
                'user_id' => $_SESSION['user_id'],
                'user_email' => $userInfo->email,
                'username' => $userInfo->name,
                'google_email' => $userInfo->email,
                'google_name' => $userInfo->name,
                'google_picture' => $userInfo->picture,
                'profile_picture' => $userInfo->picture,
                'last_activity' => time()
            ];

            header('Location: /Sagayoc/dashboards/dashboard.php');
            exit();

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = 'Could not complete your login. Please try again.';
            header('Location: /Sagayoc/login.php');
            exit();
        }

    } catch (Exception $e) {
        error_log("Google authentication error: " . $e->getMessage());
        $_SESSION['error'] = 'Google authentication failed: ' . $e->getMessage();
        header('Location: /Sagayoc/login.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Invalid authentication request.';
    header('Location: /Sagayoc/login.php');
    exit();
}
