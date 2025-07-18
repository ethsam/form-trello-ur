<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Repository\ActionColumRepository;
use League\CommonMark\CommonMarkConverter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WebhookController extends AbstractController
{
    private string $projectDir;
    private $repoActionColum;
    private $repoTicket;
    private $mailer;

    public function __construct(#[Autowire('%kernel.project_dir%')] string $projectDir, ActionColumRepository $repoActionColum, TicketRepository $repoTicket, MailerInterface $mailer)
    {
        $this->projectDir = $projectDir;
        $this->repoActionColum = $repoActionColum;
        $this->repoTicket = $repoTicket;
        $this->mailer = $mailer;
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

        if (
            isset($data['action']['type']) &&
            $data['action']['type'] === 'updateCard' &&
            isset($data['action']['data']['listBefore'], $data['action']['data']['listAfter'])
        ) {
            $cardName = $data['action']['data']['card']['name'] ?? 'Carte inconnue';
            $fromList = $data['action']['data']['listBefore']['name'] ?? 'Inconnue';
            $toList = $data['action']['data']['listAfter']['name'] ?? 'Inconnue';
            $movedBy = $data['action']['memberCreator']['fullName'] ?? 'Quelqu’un';
            $timestamp = $data['action']['date'] ?? date('c');
            $contentBodyCard = $data['action']['data']['card']['desc'] ?? 'rien...';

            $cardIdTrello = $data['action']['data']['card']['id'];
            $ticketByIdTrello = $this->repoTicket->findOneBy(['idTrello' => $cardIdTrello]);

            if ($ticketByIdTrello) {
                $this->routingNotification($ticketByIdTrello, $toList, $this->convertMarkdownToHtml($contentBodyCard));
            }

            $log = sprintf("Carde ID : %s - [%s] 📦 %s a déplacé \"%s\" de [%s] vers [%s]\n", $cardIdTrello, $timestamp, $movedBy, $cardName, $fromList, $toList);

            file_put_contents($this->projectDir . '/DEBUG/trello_webhook.log', $log, FILE_APPEND);
        }

        // $data = json_decode($request->getContent(), true);
        // file_put_contents($this->projectDir . '/DEBUG/trello_webhook.log', print_r($data, true), FILE_APPEND);

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

    public function routingNotification(Ticket $ticket, $toList, $contentBodyCard)
    {
        $arrayActionColum = $this->repoActionColum->findBy(['titleColumn' => $toList]);

        foreach ($arrayActionColum as $actionColumSingle) {
            $emailReceipt = $actionColumSingle->getEmailReceipt();
            $statusAction = $actionColumSingle->isStatus();
            $titleColumn = $actionColumSingle->getTitleColumn();

            if ($emailReceipt === 'DEMANDEUR' && $statusAction === true) {
                $this->sendMailAfterChangeColumnToReceipt($ticket, $contentBodyCard);
            } elseif ($emailReceipt != 'DEMANDEUR' && $statusAction === false) {
                $this->sendMailAfterChangeColumnToEmailContainInActionColum($ticket, $contentBodyCard, $emailReceipt, $titleColumn);
            }
        }
    }

    public function sendMailAfterChangeColumnToReceipt($ticket, $contentBodyCard): void
    {
        $dateNow = new \DateTimeImmutable();
        $emailRecept = $ticket->getUser()->getEmail();
        $objetMail = 'Votre demande est validée : ' .$dateNow->format('d/m/Y') . ' - '. $ticket->getTitle();

        $email = (new TemplatedEmail())
            ->from('support@viceversa.re')
            ->to($emailRecept)
            ->subject($objetMail)
            ->htmlTemplate('emails/ticket_validee_receipt.html.twig')
            ->context([
                'ticket' => $ticket,
                'contentBodyCard' => $contentBodyCard,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $log = sprintf("Erreur lors de l'envoi de l'email : %s\n", $e->getMessage());
            file_put_contents($this->projectDir . '/DEBUG/trello_webhook.log', $log, FILE_APPEND);
        }
    }

    public function sendMailAfterChangeColumnToEmailContainInActionColum($ticket, $contentBodyCard, $emailReceipt, $titleColumn): void
    {
        $dateNow = new \DateTimeImmutable();
        $objetMail = 'Une demande à été déplacée dans la colonne : [' . $titleColumn . '] - ' .$dateNow->format('d/m/Y') . ' - '. $ticket->getTitle();

        $email = (new TemplatedEmail())
            ->from('support@viceversa.re')
            ->to($emailReceipt)
            ->subject($objetMail)
            ->htmlTemplate('emails/ticket_action_colum.html.twig')
            ->context([
                'ticket' => $ticket,
                'contentBodyCard' => $contentBodyCard,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $log = sprintf("Erreur lors de l'envoi de l'email : %s\n", $e->getMessage());
            file_put_contents($this->projectDir . '/DEBUG/trello_webhook.log', $log, FILE_APPEND);
        }
    }

    public function convertMarkdownToHtml($contentBodyCard)
    {
        $converter = new CommonMarkConverter();
        $htmlBody = $converter->convert($contentBodyCard);
        return $htmlBody;
    }

}
