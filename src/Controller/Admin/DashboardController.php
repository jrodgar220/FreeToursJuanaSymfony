<?php

namespace App\Controller\Admin;
 
use App\Entity\User;
use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;


class DashboardController extends AbstractDashboardController
{
    #[Route('/rutas', name:'nueva_ruta')]
    public function indexRuta( ): Response
    {
        return $this->render('rutas/index.html.twig');
    }

    #[Route('/', name:'home')]
    public function indexFront( ): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('base.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('FreeToursJuana');

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Front Web', 'fa fa-home', 'home');    
        yield MenuItem::linkToCrud('Usuarios', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Sitios visitables', 'fas fa-list', Item::class);
        yield MenuItem::linktoRoute('Rutas', 'fa fa-chart-bar', 'vista_lista_ruta');    
        yield MenuItem::linktoRoute('Tours', 'fa fa-chart-bar', 'vista_lista_tour');    
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
        ->addCssFile("styles/admin.css");
        
    }
    
}
