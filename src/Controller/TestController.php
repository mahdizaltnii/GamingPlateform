<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\UserRepository;
use App\Entity\User;


class TestController extends AbstractController
{ /**
    * @Route("/", name="test")
    */
 // public function index(): Response
    // {
    //     return $this->render('base-front.html.twig', [
    //         'controller_name' => 'TestController',
    //     ]);
    // }




       /**
     * @Route("/Home", name="Home")
     */
    public function Home(): Response
    {
        return $this->render('Front-office/Home.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function Forum(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/Marketplace", name="Marketplace")
     */
    public function Marketplace(): Response
    {
        return $this->render('Front-office/Marketplace.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/Annoces", name="Annoces")
     */
    public function Annoces(): Response
    {
        return $this->render('Front-office/Annoces.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/Events", name="Events")
     */
    public function Events(): Response
    {
        return $this->render('Front-office/Events.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('base-back.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/listUsers", name="listUsers")
     */
    public function listUsers(): Response
    {
        return $this->render('Back-office/listUsers.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/addProduct", name="ddProduct")
     */
    public function addProduct(): Response
    {
        return $this->render('Back-office/addProduct.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/listProduct", name="listProduct")
     */
    public function listProduct(): Response
    {
        return $this->render('Back-office/listProduct.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/posts", name="posts")
     */
    public function posts(): Response
    {
        return $this->render('Back-office/posts.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/comments", name="comments")
     */
    public function comments(): Response
    {
        return $this->render('Back-office/comments.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/showAnnouncement", name="showAnnouncement")
     */
    public function showAnnouncement(): Response
    {
        return $this->render('Back-office/showAnnouncement.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/addAnnouncement", name="addAnnouncement")
     */
    public function addAnnouncement(): Response
    {
        return $this->render('Back-office/addAnnouncement.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/showEvent", name="showEvent")
     */
    public function showEvent(): Response
    {
        return $this->render('Back-office/showEvent.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    /**
     * @Route("/addEvent", name="addEvent")
     */
    public function addEvent(): Response
    {
        return $this->render('Back-office/addEvent.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/user/userbyidsJSON/{id}", name="usersby")
     */
    public function Allusers( NormalizerInterface $normalizer, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->find($id);
        $jsonContent = $normalizer->normalize($users, 'json', ['users' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }
}
