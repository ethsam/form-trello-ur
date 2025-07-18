<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrelloService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function createCard(string $title, string $description, string $listId, string $key, string $token): ?string
    {
        try {
            $response = $this->client->request('POST', 'https://api.trello.com/1/cards', [
                'query' => [
                    'key' => $key,
                    'token' => $token,
                    'idList' => $listId,
                    'name' => $title,
                    'desc' => $description,
                ]
            ]);

            $data = $response->toArray();
            return $data['id'] ?? null;
        } catch (\Throwable $e) {
            // Tu peux logguer l'erreur ici si besoin
            return null;
        }
    }

    public function attachFileToCard(string $cardId, string $filePath, string $fileName, string $key, string $token): void
    {
        $boundary = uniqid('trello_');
        $fileContents = file_get_contents($filePath);

        if ($fileContents === false) {
            return;
        }

        $body = "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"name\"\r\n\r\n";
        $body .= "$fileName\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"$fileName\"\r\n";
        $body .= "Content-Type: application/octet-stream\r\n\r\n";
        $body .= $fileContents . "\r\n";
        $body .= "--$boundary--\r\n";

        $this->client->request('POST', "https://api.trello.com/1/cards/{$cardId}/attachments", [
            'query' => [
                'key' => $key,
                'token' => $token,
            ],
            'body' => $body,
            'headers' => [
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            ],
        ]);
    }

    public function fetchCardDetails(string $cardId): ?array
    {
        $key = $_ENV['TRELLO_KEY'];
        $token = $_ENV['TRELLO_TOKEN'];

        try {
            $response = $this->client->request('GET', "https://api.trello.com/1/cards/{$cardId}", [
                'query' => [
                    'key' => $key,
                    'token' => $token,
                ],
            ]);

            return $response->toArray();
        } catch (\Throwable $e) {
            // Tu peux logguer l’erreur ici si besoin
            return null;
        }
    }

}
