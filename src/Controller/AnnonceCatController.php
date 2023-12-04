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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnnonceRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Annonce;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceCatController extends AbstractController
{
    /**
     * @Route("/back/annoncesCat/list", name="listannoncesCat")
     */
    public function listAnnoncesCat(AnnonceCatRepository $repo)
    {
        $annonceCat=$repo->findAll();
        return $this->render('Back-Categorie/afficheAnnonceCat.html.twig',[
            'annonceCat'=>$annonceCat
        ]);
    }
    /**
     *@Route ("/back/annoncesCat/delete/{id}", name="delete_annonceCatB")
     */
    public function deleteAnnonceCat(AnnonceCatRepository $repo,$id)
    {
        $annonceCat=$repo->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($annonceCat);
        $em->flush();
        return $this->redirectToRoute('listannoncesCat');
    }
    /**
     * @Route ("back/annoncesCat/edit{id}", name="editB_annonceCat")
     */
    public function editAnnonceCatB(AnnonceCatRepository $repo,$id,Request $request)
    {
        $annonceCat=$repo->find($id);
        $form=$this->createForm(AnnonceCatType::class,$annonceCat);
        $form->add('Editer',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listannoncesCat');
        }
        return $this->render('Back-Categorie/editBAnnonceCat.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/back/annoncesCat/add", name="addAnnonceCat")
     */
    public function addAnnonceCat(Request $request)
    {
        $annonceCat=new AnnonceCat();
        $form=$this->createForm(AnnonceCatType::class,$annonceCat);
        $form->add('Add',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($annonceCat);
            $em->flush();
            return $this->redirectToRoute('listannoncesCat');
        }
        return $this->render('Back-Categorie/ajoutAnnonceCat.html.twig',[
            'form'=>$form->createView()
        ]);

    }
}