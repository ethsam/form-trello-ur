<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Attachment;
use App\Form\FormulaireUrType;
use App\Repository\FormatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MemberController extends AbstractController
{
    private $repoFormat;
    private $manager;

    public function __construct(FormatRepository $repoFormat, EntityManagerInterface $manager)
    {
        $this->repoFormat = $repoFormat;
        $this->manager = $manager;
    }

    #[Route('/dashboard', name: 'app_member')]
    public function index(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }

    #[Route('/dashboard/history', name: 'app_member_history')]
    public function history(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }

    #[Route('/dashboard/create', name: 'app_member_create')]
    public function create(Request $request): Response
    {
        $formats = $this->repoFormat->findAll();

        if ($request->isMethod('POST')) {
            $ticket = new Ticket();
            $ticket
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
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
            $this->manager->flush();

            $this->addFlash('success', 'Demande envoyée avec succès !');
            return $this->redirectToRoute('app_member_history');
        }

        return $this->render('pages/actions/add.html.twig', [
            'formats' => $formats,
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
}
