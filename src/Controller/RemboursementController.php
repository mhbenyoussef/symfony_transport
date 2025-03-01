<?php

namespace App\Controller;

use App\Entity\Remboursement;
use App\Entity\Utilisateur;
use App\Form\RemboursementType;
use App\Form\UtilisateurType;
use App\Repository\RemboursementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RemboursementController extends AbstractController
{
    #[Route('/remboursement', name: 'app_remboursement')]
    public function index(): Response
    {
        return $this->render('remboursement/index.html.twig', [
            'controller_name' => 'RemboursementController',
        ]);
    }
    #[Route('/showRemboursements', name: 'remboursement_list')]
    public function showRemboursements(RemboursementRepository $repo): Response
    {
        $list = $repo->findAll();
        return $this->render('remboursement/remboursementList.html.twig', [
            'list' => $list,
        ]);
    }
    #[Route('/addRemboursement', name: 'remboursement_add')]
    public function addRemboursements(Request $request, EntityManagerInterface $emi): Response
    {
        $remboursement = new Remboursement();
        $form = $this->createForm(RemboursementType::class, $remboursement);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => ['class' => 'btn btn-primary w-100']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($remboursement);
            $emi->flush();
            return $this->redirectToRoute('remboursement_list');
        }
        return $this->render('remboursement/remboursementForm.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/removeRemboursement/{id}', name: 'remboursement_remove')]
    public function removeRemboursement($id, RemboursementRepository $repo, EntityManagerInterface $emi): Response
    {
        $remboursement = $repo->find($id);
        $emi->remove($remboursement);
        $emi->flush();
        return $this->redirectToRoute('remboursement_list');
    }

    #[Route('/udpateRemboursements/{id}', name: 'remboursement_update')]
    public function showUsers($id, RemboursementRepository $repo, Request $request, EntityManagerInterface $emi): Response
    {
        $remboursement = $repo->find($id);
        $form = $this->createForm(RemboursementType::class, $remboursement);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => ['class' => 'btn btn-primary w-100']
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($remboursement);
            $emi->flush();
            return $this->redirectToRoute('remboursement_list');
        }
        return $this->render('remboursement/remboursementUpdate.html.twig', ['f' => $form->createView()]);
    }
    
}
