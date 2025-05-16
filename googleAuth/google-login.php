<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
session_start();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope('email');
$client->addScope('profile');

// If we have a "code", handle Google callback
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        // Store user info in session
        $_SESSION['google_email'] = $userInfo->email;
        $_SESSION['google_name'] = $userInfo->name;
        $_SESSION['google_picture'] = $userInfo->picture;

        header("Location: dashboards/accounts.php");
        exit();
    } else {
        echo "Error fetching Google access token: " . htmlspecialchars($token['error']);
    }
} else {
    // No code yet, redirect to Google's OAuth page
    header('Location: ' . $client->createAuthUrl());
    exit();
}
