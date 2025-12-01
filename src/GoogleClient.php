<?php

namespace App;

use Google\Client;
use Google\Service\Calendar;

class GoogleClient {
    private Client $client;

    public function __construct() {
        $this->client = new Client();
        $this->client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $this->client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $this->client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->addScope('email');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function getClient(): Client {
        return $this->client;
    }

    public function isLoggedIn(): bool {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);

            if ($this->client->isAccessTokenExpired()) return false;
            else return true;
        }
        else return false;
    }
}
