<?php
namespace App\Service\Metier;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrelloServiceSM
{
    public function __construct(
        private HttpClientInterface $http,
        private string $apiKey,
        private string $token
    ) {}

    public function getCardByShortLink(string $shortLink): ?array
    {
        $url = "https://api.trello.com/1/cards/{$shortLink}?key={$this->apiKey}&token={$this->token}";
        try {
            $resp = $this->http->request('GET', $url);
            if ($resp->getStatusCode() !== 200) return null;
            return $resp->toArray();
        } catch (\Throwable $e) {
            // log if besoin
            return null;
        }
    }
}
