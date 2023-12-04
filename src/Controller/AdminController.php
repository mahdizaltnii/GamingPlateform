<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\AddTeamType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use  Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin/index", name="admin/index")
     */
    public function index(): Response
    {
        return $this->render('base-back.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/Users", name="admin/Users", methods={"GET"})
     */
    public function list_users(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('Back-office/list_users.html.twig', array("users" => $users));
    }

    /**
     * @Route("/admin/deleteUser/{id}", name="deleteUser")
     */
    public function deleteUser($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute("admin/Users");
    }
    /**
     * @Route("/admin/deleteTeam/{id}", name="deleteTeam")
     */
    public function deleteTeam($id)
    {
        $user = $this->getUser();
        $user->setTeam(null);
        $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($team);
        $em->flush();
        return $this->redirectToRoute("admin/Teams");
    }

    /**
     * @Route("/admin/Teams", name="admin/Teams")
     */
    public function list_teams(): Response
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        return $this->render('Back-office/list_team.html.twig', array('teams' => $teams));
    }

    /**
     * @Route("/admin/AddTeam", name="admin/AddTeam")
     */
    public function list_addteam(Request $request): Response
    {
        $team = new Team();
        $form = $this->createForm(AddTeamType::class, $team);
        $form->add('Add Team', SubmitType::class, [
            'attr' => ['class' => 'btn btn-block bg-gradient-warning'],
        ]);
        $form->add('Reset', ResetType::class, [
            'attr' => ['class' => 'btn btn-block bg-gradient-warning'],
        ]);
        $form = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('admin/Teams');
        }
        return $this->render(
            'Back-office/add_team.html.twig',
            array('form' => $form->createView())
        );
    }
     /**
     * @Route("/admin/{id}/editteam", name="team-edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AddTeamType::class, $team);
        $form->add('Add Team', SubmitType::class, [
            'attr' => ['class' => 'btn btn-block bg-gradient-warning'],
        ]);
        $form->add('Reset', ResetType::class, [
            'attr' => ['class' => 'btn btn-block bg-gradient-warning'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin/Teams', [], Response::HTTP_SEE_OTHER);
        }
        

        

        return $this->render('Back-office/modifier_team.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/admin/UsersPDF", name="UsersPDF")
     */
    public function UsersPDF()
    {
        $options = new Options();
        $options->set('defaultFont', 'Roboto');


        $dompdf = new Dompdf($options);
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        // return $this->render('Back-office/UsersPDF.html.twig', array("users" => $users));
        $data = array(
            'headline' => 'my headline'
        );
        $html = $this->renderView('Back-office/UserPDF.html.twig', array("users" => $users));


        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("testpdf.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/user/AllUsers", name="AllUsers")
     */
    public function All_users(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        if ($users) {

            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($users);
            return new JsonResponse($formatted);
        } else {
            return new Response("no users");
        }
    }


     /**
     * @Route("/lesusers/", name="userss")
     */
    public function afficherpostsjson(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $comment = $repository->findAll();
        
        

        $output = array();

        foreach($comment as $event)
        {
          
            $output[] = array('id'=>$event->getId(),'username'=>$event->getUsername(),'email'=>$event->getEmail());
          

        }
        return new JsonResponse($output);
    }

    /**
     * @Route("/user/delUser/{id}", name="delUser")
     */
    public function delUser(NormalizerInterface $Normalizer, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json');
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/user/ajTeam", name="delUser")
     */
    public function adTeam(Request $request)
    {
        $name = $request->query->get("teamname");
        $description = $request->query->get("description");
        $team = new Team();
        $team->setTeamName($name);
        $team->setDescription($description);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return new JsonResponse("Team created", 200);
        } catch (\Exception $ex) {
            return new Response("exception" . $ex->getMessage());
        }
    }

    /**
     * @Route("/user/allTeam", name="allTeam")
     */
    public function All_teams(): Response
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        if ($teams) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($teams);
            return new JsonResponse($formatted);
        } else {
            return new Response("no teams");
        }
    }

    /**
     * @Route("/user/delTeam/{id}", name="delTeamm")
     */
    public function delTeam(NormalizerInterface $Normalizer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository(Team::class)->find($id);
        $em->remove($team);
        $em->flush();
        $jsonContent = $Normalizer->normalize($team, 'json');
        return new Response(json_encode($jsonContent));
    }
}
