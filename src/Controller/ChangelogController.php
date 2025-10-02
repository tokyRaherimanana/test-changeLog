<?php
// src/Controller/ChangelogController.php
namespace App\Controller;

use App\Repository\ChangelogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangelogController extends AbstractController
{
    #[Route('/changelog', name: 'app_changelog')]
    public function index(ChangelogRepository $repo): Response
    {
        $entries = $repo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('changelog/index.html.twig', [
            'entries' => $entries,
        ]);
    }
}
