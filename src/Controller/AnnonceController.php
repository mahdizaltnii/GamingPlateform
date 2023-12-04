<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\AnnonceCat;
use App\Form\AnnonceCatType;
use App\Form\AnnonceType;
use App\Form\SearchForm;
use App\Repository\AnnonceCatRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnnonceRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Annonce;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/back/annonces/list", name="listannonces")
     */
    public function listAnnoncesB(AnnonceRepository $repo ,Request $request, PaginatorInterface $paginator)
    {
        $data = new SearchData();
        $form=$this->createForm(SearchForm::class,$data);
        $form->handleRequest($request);
        $annonce=$repo->findSearch($data);

        $annonce = $paginator->paginate(
            $annonce,
            $request->query->getInt('page',1),
            4
        );

        return $this->render('Back-Annonces/afficheAnnonces.html.twig',[
            'annonce'=>$annonce,
            'form'=>$form->createView()
        ]);
    }
    /**
     *@Route ("/back/annonces/delete/{id}", name="delete_annonceB")
     */
    public function deleteAnnonce(AnnonceRepository $repo,$id)
    {
        $annonce=$repo->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();
        return $this->redirectToRoute('listannonces');
    }
    /**
     * @Route ("back/annonces/edit{id}", name="editB_annonce")
     */
    public function editAnnonceB(AnnonceRepository $repo,$id,Request $request,FileUploader $fileUploader)
    {
        $annonce=$repo->find($id);
        $form=$this->createForm(AnnonceType::class,$annonce);
        $form->add('Editer',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $annonce->setImage($fileName);
            }
            $em->flush();
            return $this->redirectToRoute('listannonces');
        }
        return $this->render('Back-Annonces/editBAnnonce.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/annonces/add", name="ajoutAnnonce")
     */
    public function addAnnonce(Request $request, FileUploader $fileUploader, \Swift_Mailer $mailer)
    {
        $annonce=new Annonce();
        $form=$this->createForm(AnnonceType::class,$annonce);
        $form->add('Post',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $annonce->setImage($fileName);
            }
            $em->persist($annonce);
            $em->flush();

            //SwiftMailer
            $contact=$form->getData()->getEmail();

            $message= (new\ Swift_Message('Validation de Lannonce'))
                ->setFrom ('leith.ghandri@gmail.com')
                ->setTo($contact)
                ->setBody(
                    $this->renderView('Mail/mail.html.twig',compact('contact')
                    ),
                    'text/html'
                );
            $mailer->send($message);

            //end
            $this->addFlash('success','Votre Annonce a été Ajouter ! Un Email a été envoyer !');

            return $this->redirectToRoute('listAnnoncesF');
        }
        return $this->render('Front-Annonces/ajoutAnnonce.html.twig',[
            'annonceform'=>$form->createView()
        ]);

    }
    /**
     * @Route ("/annonces", name="listAnnoncesF")
     */
    public function listAnnoncesF(AnnonceRepository $repo ,Request $request, PaginatorInterface $paginator)
    {
        $data = new SearchData();
        $form=$this->createForm(SearchForm::class,$data);
        $form->handleRequest($request);
        $annonce=$repo->findSearch($data);

        $annonce = $paginator->paginate(
            $annonce,
            $request->query->getInt('page',1),
            10
        );


        return $this->render('Front-Annonces/afficheAnnonce.html.twig',[
            'annonce'=>$annonce,
            'annonceform'=>$form->createView()
        ]);
    }
    /**
     * @Route("/annonces/show{id}", name="annonce_show")
     */
    public function showAnnonce($id,AnnonceRepository $repository)
    {
        $annonce=$repository->find($id);
        return $this->render('Front-Annonces/show_annonce.html.twig',[
            'annonce'=>$annonce
        ]);
    }
    /**
     * @Route ("/annonces/edit{id}", name="edit_annonce")
     */
    public function editAnnonce(AnnonceRepository $repo,$id,Request $request,FileUploader $fileUploader)
    {
        $annonce=$repo->find($id);
        $form=$this->createForm(AnnonceType::class,$annonce);
        $form->add('Editer',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $annonce->setImage($fileName);
            }
            $em->flush();
            $this->addFlash('success','Votre Annonce a été Modifier !');
            return $this->redirectToRoute('annonce_show',['id'=>$id]);
        }
        return $this->render('Front-Annonces/editAnnonce.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     *@Route ("/annonces/delete/{id}", name="delete_annonce")
     */
    public function deleteAnnonceF(AnnonceRepository $repo,$id)
    {
        $annonce=$repo->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();
        $this->addFlash('danger','Votre Annonce a été Supprimer !');
        return $this->redirectToRoute('listAnnoncesF');
    }

    /**
     * @Route("/liste", name="annonesJ")
     */
    public function allAnnonces(NormalizerInterface $normalizer,AnnonceRepository $repo)
    {
        $annonce=$repo->findAll();
        $jsonContent = $normalizer->normalize($annonce,'json',['groups'=>'post:read']);
        return new Response (json_encode($jsonContent));
    }
}