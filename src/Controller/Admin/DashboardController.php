<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Format;
use App\Entity\Ticket;
use App\Entity\ActionColum;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\FormatCrudController;
use App\Controller\Admin\TicketCrudController;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ActionColumCrudController;
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
                yield MenuItem::linkToCrud('Gestion Formats', 'fas fa-file-alt', Format::class)->setController(FormatCrudController::class),
                yield MenuItem::linkToCrud('Gestion Demande', 'fas fa-file-alt', Ticket::class)->setController(TicketCrudController::class),
                yield MenuItem::linkToCrud('Gestion Utilisateurs', 'fas fa-file-alt', User::class)->setController(UserCrudController::class),
                yield MenuItem::linkToCrud('Gestion Actions', 'fas fa-file-alt', ActionColum::class)->setController(ActionColumCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('univ-reunion.fr', 'fas fa-at', 'https://www.univ-reunion.fr/')->setLinkTarget('_blank'),
            ];

        } else {
            return [
                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

                yield MenuItem::section('GESTIONS'),
                // yield MenuItem::linktoRoute('Gérer la Une', 'far fa-newspaper', 'app_articles_en_une'),
                yield MenuItem::linkToCrud('Gestion Formats', 'fas fa-file-alt', Format::class)->setController(FormatCrudController::class),
                yield MenuItem::linkToCrud('Gestion Demande', 'fas fa-file-alt', Ticket::class)->setController(TicketCrudController::class),
                yield MenuItem::linkToCrud('Gestion Utilisateurs', 'fas fa-file-alt', User::class)->setController(UserCrudController::class),
                yield MenuItem::linkToCrud('Gestion Actions', 'fas fa-file-alt', ActionColum::class)->setController(ActionColumCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('univ-reunion.fr', 'fas fa-at', 'https://www.univ-reunion.fr/')->setLinkTarget('_blank'),
            ];
            //return $this->redirect('/');
        }
    }
}
