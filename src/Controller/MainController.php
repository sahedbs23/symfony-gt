<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig');
    }

    #[Route('/custom/{name?}', name: 'custom')]
    /**
     * @param Request $request
     * @return Response
     */
    public function custom(Request $request): Response
    {
        $name = $request->get('name');
        return $this->render('home/custom.html.twig', ['name' => $name]);
    }
}
