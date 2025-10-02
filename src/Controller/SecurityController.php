<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_personne_list');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', name: 'app_registration')]
    public function registration(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em)
    {
        if($request->isMethod('POST'))
        {
            $data = $request->request->all();
            if($data['password'] === $data['confirm'])
            {
                $user = new User();
                $user
                    ->setEmail($data['email'])
                    ->setPassword($hasher->hashPassword($user, $data['password']))
                    ->setRoles(['ROLE_ADMIN']);

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/signup.html.twig');
    }
}
