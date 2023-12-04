<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @param ProduitRepository $repository
     * @param Request $request
     * @return Response
     * @Route ("/search_ajax",name="search_ajax")
     */
    public function searchAction(Request $request,ProduitRepository $repository)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');



//        var_dump(strlen($requestString));
        $entities =  $em->getRepository(Produit::class)->findEntitiesByString($requestString);

        if(!$entities)
        {
            $result['entities']['error'] = "there is no demande with this titre";

        }
        if(strlen($requestString)==1)
        {

            $entities=$repository->findAll();
            $result['entities']=$this->getRealEntities($entities);
        }
        else
        {

            $result['entities'] = $this->getRealEntities($entities);
        }

        return new JsonResponse($result, 200);
    }


    public function getRealEntities($entities){


        foreach ($entities as $entity)
        {
            $realEntities[$entity->getId()] = [$entity->getId(),$entity->getNom(),$entity->getDescription(),$entity->getQteProd(),$entity->getPrixProd(),$entity->getImage()];
        }


        return $realEntities;
    }
    /**
     * @Route("/", name="app_produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ProduitFront", name="app_produit_index_Front", methods={"GET"})
     */
    public function indexFront(ProduitRepository $produitRepository): Response
    {
        return $this->render('Front-office/Marketplace.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit);
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/Front/{id}", name="app_produit_show_front", methods={"GET"})
     */
    public function showFront(Produit $produit): Response
    {
        return $this->render('produit/showFront.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit);
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }


}
