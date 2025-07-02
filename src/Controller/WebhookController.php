<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WebhookController extends AbstractController
{
    private string $projectDir;

    public function __construct(#[Autowire('%kernel.project_dir%')] string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    #[Route('/webhook', name: 'app_webhook', methods: ['GET', 'POST', 'HEAD'])]
    public function index(Request $request): Response
    {
        if ($request->getMethod() === 'HEAD') {
            return new Response('OK', 200);
        }

        if ($request->getMethod() === 'GET') {
            return new Response('Webhook en ligne ✅ (GET)', 200);
        }

        $data = json_decode($request->getContent(), true);
        file_put_contents($this->projectDir . '/DEBUG/trello_webhook.log', print_r($data, true), FILE_APPEND);

        return new Response('OK', 200);
    }

    #[Route('/webhook/log', name: 'app_webhook_log', methods: ['GET'])]
    public function showLog(): Response
    {
        $logFile = $this->projectDir . '/DEBUG/trello_webhook.log';

        if (!file_exists($logFile)) {
            return new Response('Aucun log trouvé.', 404);
        }

        $content = file_get_contents($logFile);

        return new Response(
            nl2br(htmlspecialchars($content)), // sécurise l'affichage
            200,
            ['Content-Type' => 'text/html']
        );
    }



}
