<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Posts;
use App\Entity\Rating;

use App\Form\PostType;
use App\Entity\Comments;
use App\Entity\PostLike;
use App\Form\CommentsType;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class MessagesController extends AbstractController
{
    /**
     * @Route("/messages", name="app_messages", methods={"GET","POST"})
     */
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'MessagesController',
        ]);
    }
}
