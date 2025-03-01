<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UserLoginType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }
    #[Route('/register', name: 'user_register')]
    public function register(Request $request, EntityManagerInterface $emi, UserPasswordHasherInterface $pH): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->add('submit', SubmitType::class, [
            'label' => 'Register',
            'attr' => ['class' => 'btn btn-primary w-100']
        ]);
        $form->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPassword();
            $hashedPassword = $pH->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $emi->persist($user);
            $emi->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('utilisateur/userRegister.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/login', name: 'user_login')]
    public function login(
        Request $request,
        EntityManagerInterface $emi,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UserLoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data->getEmail();
            $plainPassword = $data->getPassword();

            $user = $emi->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if (!$user || !$passwordHasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Invalid email or password.');
                return $this->render('utilisateur/userLogin.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('utilisateur/userLogin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/showUsers', name: 'user_list')]
    public function showUsers(UtilisateurRepository $repo): Response
    {
        $list = $repo->findAll();
        return $this->render('utilisateur/userList.html.twig', [
            'list' => $list,
        ]);
    }

    #[Route('/removeUser/{id}', name: 'user_remove')]
    public function removeUser($id, UtilisateurRepository $repo, EntityManagerInterface $emi): Response
    {
        $user = $repo->find($id);
        $emi->remove($user);
        $emi->flush();
        return $this->redirectToRoute('user_list');
    }

    #[Route('/editUser/{id}', name: 'user_update')]
    public function editUser($id, UtilisateurRepository $repo, Request $request, EntityManagerInterface $emi, UserPasswordHasherInterface $pH): Response
    {
        $user = $repo->find($id);
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => ['class' => 'btn btn-primary w-100']
        ]);
        $form->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'attr' => ['class' => 'form-control'],
            'mapped' => false,
            'required' => true,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('password')->getData())) { 
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $pH->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $emi->persist($user);
            $emi->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('utilisateur/userUpdate.html.twig', ['f' => $form->createView()]);
    }
}
