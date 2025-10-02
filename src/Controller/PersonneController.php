<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use App\Services\Applicatif\PersonneSA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('personne', name: 'app_personne')]
class PersonneController extends AbstractController
{

    private PersonneSA $personneSA;
    private PersonneRepository $personneRepository;
    public function __construct(
        PersonneSA $personneSA,
        PersonneRepository $personneRepository
    )
    {
        $this->personneSA = $personneSA;
        $this->personneRepository = $personneRepository;
    }

    #[Route('/list', name: '_list')]
    public function index()
    {
        return $this->render('personne/list.html.twig', ['personne' => $this->personneRepository->findAll()]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $data = $request->request->all();

            $res = $this->personneSA->create($data);

            $this->addFlash('sucess', $res['msg']);

            return $this->redirectToRoute('app_personne_list', ['personne' => $this->personneRepository->findAll()]);
        }

        return $this->render('personne/create.html.twig');
    }

    #[Route('/update/{id}', name: '_update')]
    public function update(Personne $personne, Request $request)
    {
        if($request->isMethod('POST'))
        {
            $data = $request->request->all();

            $res = $this->personneSA->update($personne, $data);

            $this->addFlash('sucess', $res['msg']);

            return $this->redirectToRoute('app_personne_list', ['personne' => $this->personneRepository->findAll()]);
        }

        return $this->render('personne/update.html.twig', ['personne' => $personne]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete(Personne $personne, EntityManagerInterface $em)
    {
        $em->remove($personne);
        $em->flush();
        return $this->redirectToRoute('app_personne_list', ['personne' => $this->personneRepository->findAll()]);
    }
}
