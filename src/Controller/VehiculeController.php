<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VehiculeRepository;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


final class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'app_vehicule')]
    public function index(): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'controller_name' => 'VehiculeController',
        ]);
    }
    #[Route('/vehicules', name: 'app_vehicule_show')]
    public function show(VehiculeRepository $repoVehicle): Response
    {
        $vehicules = $repoVehicle->findAll();
        return $this->render('vehicule/showvehicules.html.twig', [
            'vehicules' => $vehicules,
        ]);


    }
    #[Route('/vehicule/add', name: 'app_vehicule_add')]
    public function add_vehicule(
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newVehicule = $form->getData();
            $em->persist($newVehicule);
            $em->flush();
            return $this->redirectToRoute('app_vehicule_add');
        }
        return $this->render('vehicule/addvehicules.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/vehicule/edit/{id}', name: 'app_vehicule_edit')]
    public function edit_vehicule(
        Request $request,
        EntityManagerInterface $em,
        Vehicule $vehicule
    ): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_vehicule_show');
        }
        return $this->render('vehicule/editvehicule.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/vehicule/delete/{id}', name: 'app_vehicule_delete')]
    public function delete_vehicule(
        EntityManagerInterface $em,
        Vehicule $vehicule
    ): Response
    {
        $em->remove($vehicule);
        $em->flush();
        return $this->redirectToRoute('app_vehicule_show');
    }



}