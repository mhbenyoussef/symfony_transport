<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VoyageRepository;
use App\Entity\Voyage;
use App\Form\VoyageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\VehiculeRepository;
final class VoyageController extends AbstractController
{
    #[Route('/voyage', name: 'app_voyage')]
    public function index(): Response
    {
        return $this->render('voyage/index.html.twig', [
            'controller_name' => 'VoyageController',
        ]);
    }
    #[Route('/voyages', name: 'app_voyage_show')]
    public function show(VoyageRepository $repoVoyage): Response
    {
        $voyages = $repoVoyage->findAll();
        return $this->render('voyage/showvoyages.html.twig', [
            'voyages' => $voyages,
        ]); 
      
    }
    #[Route('/voyage/add/{id}', name: 'app_voyage_add')]
    public function add_voyage(Request $request,
    EntityManagerInterface $em,
    VehiculeRepository $repoVehicule,
    int $id): Response
    {
        $vehicule = $repoVehicule->find($id);
        $voyage = new Voyage();
        $voyage->setVehicule($vehicule);
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newVoyage = $form->getData();
            $em->persist($newVoyage);
            $em->flush();
            return $this->redirectToRoute('app_voyage_show');
        }
        return $this->render('voyage/addvoyage.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }
    #[Route('/voyage/edit/{id}', name: 'app_voyage_edit')]
    public function edit_voyage(
        Request $request,
        EntityManagerInterface $em,
        Voyage $voyage
    ): Response
    {
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_voyage_show');
        }
        return $this->render('voyage/editvoyage.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/voyage/delete/{id}', name: 'app_voyage_delete')]
    public function delete_voyage(
        EntityManagerInterface $em,
        Voyage $voyage
    ): Response
    {
        $em->remove($voyage);
        $em->flush();
        return $this->redirectToRoute('app_voyage_show');
    }
}