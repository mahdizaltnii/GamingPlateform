<?php

namespace App\Controller;
use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
/**
 * @Route("/reponse")
 */
class ReponseController extends AbstractController
{
    /**
     * @Route("/", name="reponse_index", methods={"GET"})
     */
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }



























    /**
     * @Route("/new", name="reponse_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="reponse_show", methods={"GET"})
     */
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reponse_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reponse_delete", methods={"POST"})
     */
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reponse_index', [], Response::HTTP_SEE_OTHER);
    }




    







/**
     * @Route("/reponse/add3", name="add_reponse")
     * @Method("GET")
     */



    public function AddReponse(Request $request, NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
   
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $em=$this->getDoctrine()->getManager();
    $reponse=new Reponse();
    $reclamation1 = new Reclamation();
    $reclamation = new Reclamation();
   
    $reclamation1 = $request->get('Reclamation');
    $repository = $this->getdoctrine()->getRepository(Reclamation::class);
    $reclamation = $repository->findOneBy(array('id' => $reclamation1));

    if (!$reclamation) {
        throw $this->createNotFoundException(
            'No product found for id '
        );
    }else
    { 
            $reponse->setReclamation($reclamation);
            $reponse->setReponse($request->get('reponse'));
            $reponse->setDate(date_create_from_format("Y-m-d",$request->get("date")));
            
   
    $em->persist($reponse);
    $em->flush();
    $jsonContent=$Normalizer->normalize($reponse,'json',['groups'=>'post:read']);
    
    return new Response(json_encode($jsonContent));
          
    }


    }


/**
     * @Route("/reponse/updatereponse/{id}", name="update_reponse")
     * @Method("PUT")
     */
    public function modifierReponseAction(Request $request, NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $Reponse = $this->getDoctrine()->getManager()
            ->getRepository(Reponse::class)
            ->find($request->get("id"));
            $reponse = $request->query->get("reponse");
           
            $date = $request->query->get("date");
          

           
            $Reponse->setReponse($request->get('reponse'));
          
            $Reponse->setDate(date_create_from_format("Y-m-d",$request->get("date")));
           

        $em->persist($Reponse);
        $em->flush();
        $jsonContent=$Normalizer->normalize($Reponse,'json',['groups'=>'post:read']);
    
    
    return new JsonResponse("reponse a ete modifiee avec success.");      
    }



 /**
     * @Route("/reponse/deletereponse", name="delete_reponse")
     * @Method("DELETE")
     */

    public function deleteReponseAction(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Reponse = $em->getRepository(reponse::class)->find($id);
        if($Reponse!=null ) {
            $em->remove($Reponse);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Votre reponse a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Reponse invalide.");


    }
    /**
     * @Route("/reponse/liste2",name="liste_reponse")
     */
    
    
    public function getReponse(NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
    $repository =$this->getDoctrine()->getRepository(Reponse::class);
    $Reponse=$repository->FindAll();
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $jsonContent=$Normalizer->normalize($Reponse,'json',['groups'=>'post:read']);
    
    
    
    return new Response(json_encode($jsonContent));
    dump($jsonContent);
    die;}














}
