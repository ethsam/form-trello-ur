<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $url = $adminUrlGenerator
            ->setController(FormatCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('UNIVERSITE REUNION');
    }

    public function configureMenuItems(): iterable
    {
        if ( $this->isGranted('ROLE_ADMIN') ) {
            return [
                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

                yield MenuItem::section('GESTIONS'),
                // yield MenuItem::linktoRoute('Gérer la Une', 'far fa-newspaper', 'app_articles_en_une'),
                yield MenuItem::linkToCrud('Gestion Formats', 'fas fa-file-alt', \App\Entity\Format::class)->setController(FormatCrudController::class),
                yield MenuItem::linkToCrud('Gestion Demande', 'fas fa-file-alt', \App\Entity\Ticket::class)->setController(TicketCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('univ-reunion.fr', 'fas fa-at', 'https://www.univ-reunion.fr/')->setLinkTarget('_blank'),
            ];

        } else {
            return [
                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

                yield MenuItem::section('GESTIONS'),
                // yield MenuItem::linktoRoute('Gérer la Une', 'far fa-newspaper', 'app_articles_en_une'),
                yield MenuItem::linkToCrud('Gestion Formats', 'fas fa-file-alt', \App\Entity\Format::class)->setController(FormatCrudController::class),
                yield MenuItem::linkToCrud('Gestion Demande', 'fas fa-file-alt', \App\Entity\Ticket::class)->setController(TicketCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('univ-reunion.fr', 'fas fa-at', 'https://www.univ-reunion.fr/')->setLinkTarget('_blank'),
            ];
            //return $this->redirect('/');
        }
    }
}
