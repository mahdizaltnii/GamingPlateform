<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Team;
use App\Form\RegistrationType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\UserRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends AbstractController
{
   /**
     * @Route("/user/registration", name="user/registration")
     */
    public function registration(
        Request $request,
        ObjectManager $manager,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->add('Create Account', SubmitType::class, [
            'attr' => ['class' => 'mybtn1'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['picture_profil']->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $user->setPictureProfil($newFilename);
            $user->setIsactive(true);
            $user->setRoles(["ROLE_USER"]);
            //le cryptage de mot de passe
            //$hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($user->getPassword());
            //générer le token d'activation 
            $user->setActivationToken(md5(uniqid()));
            $manager->persist($user);
            $manager->flush();
            $message = (new \Swift_Message('Activation de compte'))
                ->setFrom('leith.ghandri@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('Front-office/email.html.twig', ['token' => $user->getActivationToken()]),
                    'text/html'
                );
            $mailer->send($message);
            //return $this->redirectToRoute('user/registration');
            return $this->json(['code' => 200, 'likes' =>'vous avez recu un mail' ], 200);

        }
        return $this->render('base-front.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/user/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['activation_token' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('utilisateur n\'existe pas');
        }
        $user->setActivationToken(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        //message flush
        $this->addFlash('info', 'votre compt est activé');
        return $this->redirectToRoute('user/registration');
    }

    /**
     * @Route("/user/connexion", name="login")
     */
    public function login()
    {
        return $this->render('Front-office/Home.html.twig');
    }

    /**
     * @Route("/logout", name= "logout")
     */
    public function logout()
    {
        return $this->render('Front-office/Home.html.twig');
    }

    /**
     * @Route("/user/Teams/{id}",name="JoinTeam")
     * @Method({"POST", "HEAD"})
     */
    public function JoinTeam($id): Response
    {
        $user = $this->getUser();
        $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        // $team->addUser($this->getUser());
        $user->setTeam($team);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        return $this->render('Front-office/team.html.twig', array('teams' => $teams));
    }
    /**
     * @Route("/user/quitTeams",name="QuitTeam")
     * @Method({"POST", "HEAD"})
     */
    public function QuitTeam(): Response
    {
        $user = $this->getUser();
        // $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        // $team->addUser($this->getUser());
        $user->setTeam(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        return $this->render('Front-office/team.html.twig', array('teams' => $teams));
    }

    /**
     * @Route("/user/Teams", name="user/Teams")
     */
    public function list_teams(): Response
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        return $this->render('Front-office/team.html.twig', array('teams' => $teams));
    }
/**
     * @Route("/user/signup", name="signup")
     */
    public function signupAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $email = $request->query->get("email");
        $username = $request->query->get("username");
        $password = $request->query->get("password");


        //controle sur l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid");
        }
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsactive(true);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("Account created", 200);
        } catch (\Exception $ex) {
            return new Response("exception" . $ex->getMessage());
        }
    }

    /**
     * @Route("/user/signin", name="signin")
     */
    public function signinAction(Request $request)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            // if (password_verify($password, $user->getPassword())) {
            //     $serializer = new Serializer([new ObjectNormalizer()]);
            //     $formatted = $serializer->normalize($user);
            //     return new JsonResponse($formatted);
            // } else {
            //     return new Response("password or email not found");
            // }
        } else {
            return new Response("password or email not found");
        }
    }

    /**
     * @Route("/user/editUser", name="editUser")
     */
    public function edituser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $id = $request->get("id");
        $username = $request->query->get("username");
        $password = $request->query->get("password");
        $email = $request->query->get("email");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setRoles(["ROLE_USER"]);
        $user->setIsactive(true);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("Account updated", 200);
        } catch (\Exception $ex) {
            return new Response("failed" . $ex->getMessage());
        }
    }

}
