<?php

namespace App\Controller;

use App\Form\TrelloCardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TestController extends AbstractController
{
    private string $trelloKey;
    private string $trelloToken;

    private string $col_a_faire;
    private string $col_en_cours;
    private string $col_terminer;

    public function __construct(private HttpClientInterface $httpClient)
    {
        $this->trelloKey = $_ENV['TRELLO_KEY'];
        $this->trelloToken = $_ENV['TRELLO_TOKEN'];
        $this->col_a_faire = $_ENV['COL_A_FAIRE'];
        $this->col_en_cours = $_ENV['COL_EN_COURS'];
        $this->col_terminer = $_ENV['COL_TERMINER'];
    }

    #[Route('/test', name: 'app_test')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TrelloCardType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Appel API Trello
            $this->httpClient->request('POST', 'https://api.trello.com/1/cards', [
                'query' => [
                    'key' => $this->trelloKey,
                    'token' => $this->trelloToken,
                    'idList' => $this->col_a_faire,
                    'name' => $data['name'],
                    'desc' => $data['desc'],
                ]
            ]);

            $this->addFlash('success', 'Carte Trello créée avec succès !');

            return $this->redirectToRoute('app_test');
        }

        return $this->render('test/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

