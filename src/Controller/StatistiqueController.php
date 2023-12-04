<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




use App\Entity\Reclamation;
use App\Entity\Reponse;








class StatistiqueController extends AbstractController
{
    /**
     * @Route("/statistique", name="statistique")
     */
    public function index(): Response
    {
        $Entreprises=$this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $EntreprisesNbr=sizeof($Entreprises);


        $Etudiants=$this->getDoctrine()->getRepository(Reponse::class)->findAll();
        $EtudiantsNbr=sizeof($Etudiants);



        return $this->render('stat/index.html.twig', [
            'controller_name' => 'StatistiqueController',
            'EntreprisesNbr'=>$EntreprisesNbr,'EtudiantsNbr'=>$EtudiantsNbr
        ]);
    }
}

