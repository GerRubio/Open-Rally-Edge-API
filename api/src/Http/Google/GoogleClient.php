<?php

namespace App\Http\Google;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;

class GoogleClient
{
    private Google $client;

    public function __construct(
        string $clientId,
        string $clientSecret)
    {
        $this->client = new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => 'http://localhost:250/api/v1/users/google/oauth',
        ]);
    }

    /**
     * @throws IdentityProviderException
     */
    public function getAccessToken(string $grant = 'authorization_code', array $options = []): AccessToken
    {
        return $this->client->getAccessToken($grant, $options);
    }

    public function getUserInfo(AccessToken $accessToken): array
    {
        $resourceOwner = $this->client->getResourceOwner($accessToken);

        return $resourceOwner->toArray();
    }
}