<?php

namespace App\Controller;

use App\Model\GoogleClient;

class AuthController {
    private GoogleClient $googleClient;

    public function __construct(GoogleClient $googleClient) {
        $this->googleClient = $googleClient;
    }

    public function login() {
        $authUrl = $this->googleClient->getClient()->createAuthUrl();
        header("Location: $authUrl");
        exit;
    }

    public function callback() {
        if (isset($_GET['code'])) {
            $token = $this->googleClient->getClient()->fetchAccessTokenWithAuthCode($_GET['code']);

            if (!isset($token['error'])) {
                $_SESSION['access_token'] = $token;
                header('Location: /addEvent');
                exit;
            }
        }

        // If something went wrong
        header('Location: /');
        exit;
    }

    public function logout() {
        unset($_SESSION['access_token']);
        session_destroy();
        header('Location: /');
        exit;
    }
}
