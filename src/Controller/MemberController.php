<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Attachment;
use App\Form\FormulaireUrType;
use App\Service\TrelloService;
use App\Repository\FormatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MemberController extends AbstractController
{
    private $repoFormat;
    private $manager;
    private TrelloService $trello;
    private UploaderHelper $uploaderHelper;
    private $httpClient;
    private $trelloKey;
    private $trelloToken;
    private string $col_a_faire;
    private string $col_en_cours;
    private string $col_terminer;

    public function __construct(FormatRepository $repoFormat, EntityManagerInterface $manager, TrelloService $trello, UploaderHelper $uploaderHelper, HttpClientInterface $httpClient)
    {
        $this->repoFormat = $repoFormat;
        $this->manager = $manager;
        $this->trello = $trello;
        $this->uploaderHelper = $uploaderHelper;
        $this->httpClient = $httpClient;

        $this->trelloKey = $_ENV['TRELLO_KEY'];
        $this->trelloToken = $_ENV['TRELLO_TOKEN'];
        $this->col_a_faire = $_ENV['COL_A_FAIRE'];
        $this->col_en_cours = $_ENV['COL_EN_COURS'];
        $this->col_terminer = $_ENV['COL_TERMINER'];
    }

    #[Route('/dashboard', name: 'app_member')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
            'userConnected' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/history', name: 'app_member_history')]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
            'userConnected' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/create', name: 'app_member_create')]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $formats = $this->repoFormat->findAll();

        if ($request->isMethod('POST')) {
            $ticket = new Ticket();
            $ticket
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setUser($this->getUser())
                ->setTitle($request->request->get('intitule_projet'))
                ->setBrief($request->request->get('brief'))
                ->setComposante($request->request->get('composant_consernee'))
                ->setLink($request->request->get('lien_ressources'))
                ->setDateEvent(new \DateTimeImmutable($request->request->get('date_event')))
                ->setDateLimit(new \DateTimeImmutable($request->request->get('date_limite')));

            $formatIds = $request->request->all('formats');
            foreach ($formatIds as $fid) {
                $format = $this->repoFormat->find($fid);
                if ($format) {
                    $ticket->addFormat($format);
                }
            }

            $attachmentIds = explode(',', $request->request->get('attachments_ids', ''));
            foreach ($attachmentIds as $aid) {
                if ($aid) {
                    $attachment = $this->manager->getRepository(Attachment::class)->find($aid);
                    if ($attachment) {
                        $ticket->addAttachment($attachment);
                    }
                }
            }

            $this->manager->persist($ticket);
            $statusCreate = $this->createTrelloCard($ticket);

            if ($statusCreate['status']) {
                $this->manager->flush();
                $this->addFlash('success', 'Demande envoyée avec succès et carte Trello créée.');
                return $this->redirectToRoute('app_member_history');
            } else {
                $this->addFlash('error', 'Demande enregistrée, mais échec de création de la carte Trello.');
                return $this->redirectToRoute('app_member_history');
            }

        }

        return $this->render('pages/actions/add.html.twig', [
            'formats' => $formats,
            'userConnected' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/upload', name: 'app_member_upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        $attachment = new Attachment();
        $attachment->setImageFile($file);
        $attachment->setCreatedAt(new \DateTimeImmutable());
        $attachment->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($attachment);
        $this->manager->flush();

        return new JsonResponse(['id' => $attachment->getId()]);
    }

    public function createTrelloCard(Ticket $ticket): array
    {
        $desc = sprintf(
            "**Projet :** %s\n\n" .
            "**De :** %s\n" .
            "**Email :** [%s](mailto:%s \"‌\")\n" .
            "**Composante :** %s\n\n" .
            "**Date événement :** %s\n" .
            "**Date limite :** %s\n" .
            "**Lien ressources :** %s\n\n" .
            "---\n\n" .
            "**Brief :**\n%s\n\n" .
            "‌\n\n" .
            "```\nDélivrable :\n```",
            $ticket->getTitle(),
            $ticket->getUser()?->getUsername() ?? '—',
            $ticket->getUser()?->getEmail() ?? '—',
            $ticket->getUser()?->getEmail() ?? '-',
            $ticket->getComposante() ?: '(aucun)',
            $ticket->getDateEvent()?->format('d/m/Y') ?? '—',
            $ticket->getDateLimit()?->format('d/m/Y') ?? '—',
            $ticket->getLink() ?: '(aucun)',
            $ticket->getBrief() ?: '(aucun)'
        );


        $cardId = $this->trello->createCard(
            $ticket->getTitle(),
            $desc,
            $this->col_a_faire, // ou $this->col_en_cours etc.
            $this->trelloKey,
            $this->trelloToken
        );

        if (!$cardId) {
            return ['status' => false];
        }

        foreach ($ticket->getAttachments() as $attachment) {
            $relativePath = $this->uploaderHelper->asset($attachment, 'imageFile'); // ← 'imageFile' = nom du champ
            $filePath = $this->getParameter('kernel.project_dir') . '/public' . $relativePath;
            $fileName = basename($filePath);

            if (file_exists($filePath)) {
                $this->trello->attachFileToCard(
                    $cardId,
                    $filePath,
                    $fileName,
                    $this->trelloKey,
                    $this->trelloToken
                );
            }
        }

        return ['status' => true, 'cardId' => $cardId];
    }


}
