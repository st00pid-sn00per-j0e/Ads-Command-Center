<?php

namespace App\Services\GoogleAds;

use App\Models\GoogleAdsConnection;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V25\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V25\GoogleAdsClientBuilder;

class GoogleAdsClientFactory
{
    public function make(GoogleAdsConnection $connection): GoogleAdsClient
    {
        $oAuth2Credential = (new OAuth2TokenBuilder)
            ->withClientId($connection->client_id)
            ->withClientSecret($connection->client_secret)
            ->withRefreshToken($connection->refresh_token)
            ->build();

        return (new GoogleAdsClientBuilder)
            ->withDeveloperToken($connection->developer_token)
            ->withLoginCustomerId($connection->login_customer_id)
            ->withOAuth2Credential($oAuth2Credential)
            ->build();
    }
}
