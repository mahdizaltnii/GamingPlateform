<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Posts;
use App\Entity\Rating;

use App\Form\PostType;
use App\Entity\Comments;
use App\Entity\PostLike;
use App\Form\CommentsType;
use App\Repository\BadWordsRepository;
use App\Repository\UserRepository;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="posts_index", methods={"GET","POST"})
     */
    public function index(Request $request,PaginatorInterface $paginator ): Response
    {

        // $user = $this->getUser();

        // $entityManager = $this->getDoctrine()->getManager();
        // $isPostAlreadyLiked= $entityManager->getRepository(PostLike::class)->countByBlogAndUser($user,$post);
        // $em = $this->getDoctrine()->getManager();
        $donnees=$this->getDoctrine()->getRepository(Posts::class)->findAll();
        $post=$paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('blog/index.html.twig', [
            'posts' =>$post
            // 'isPostAlreadyLiked'=>$isPostAlreadyLiked
        ]);
    }
    

    /**
     * @Route("/detail/{id}", name="afficher_posts", methods={"GET","POST"})
     */
    public function affiche($id, Request $request, Posts $post , BadWordsRepository $bwr): Response
    {    $allowed=true;
        $user = $this->getUser();
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $badwords = $bwr->findAll();
            foreach($badwords as $word){
              if(strpos($form->get('content')->getData() , $word->getWord()) !== false){
                $allowed = false;
              }
            }
            if($allowed){
                $comment->setCreatedAt(new \DateTimeImmutable())
                ->setPoste($post)
                ->setUser($user);
            $this->addFlash('message', 'Votre Commentaire a été bien envoyé');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute('afficher_posts', ['id' => $post->getId()],);

            }else{

                echo '<script>alert("Vous avez saisi un Mauvais mot")</script>';

            //   return $this->redirectToRoute('posts_index');
            }
            
        }

        $user = $this->getUser();
        $result = $post->getUser() === $user;
        $commentresult = $comment->getUser() === $user;

        $post = $this->getDoctrine()->getRepository(Posts::class)->find($id);
        
        $comment = $post->getComments();
        
        return $this->render('blog/details.html.twig', [
            'post' => $post,
            'comment' => $comment,
            'Commentform' => $form->createView(),
            'result' => $result,
            'commentresult' => $commentresult,
           

        ]);
    }



    /**
     * @Route("/details/{id}", name="afficher_commentaires", methods={"GET","POST"})
     */
    public function getCommentaires($id)
    {
        $post = $this->getDoctrine()->getRepository(Posts::class)->find($id);
        $comment = $post->getComments();
        return $this->render('blog/details.html.twig', [
            'comment' => $comment,
        ]);
    }



    /**
     * @Route("/blog/add", name="comments_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/ajoutC.html.twig', [
            'comment' => $comment,
            'commentform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blog/addpost", name="ajoutpost",methods={"GET","POST"})
     */
    public function ajoutpost(Request $request)
    {
        $post = new Posts();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $post->setpicture($newFilename);
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('blog', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/ajoutposte.html.twig', [
            'post' => $post,
            'postform' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="posts_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('posts_index', [], Response::HTTP_SEE_OTHER);
        }
        

        return $this->render('blog/edit.html.twig', [
            'post' => $post,
            'postform' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}/delete", name="posts_delete")
     */
    public function delete($id)
    {

        $post = $this->getDoctrine()->getRepository(Posts::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute("posts_index");
    }


    /**
     * @Route("/{id}/editC", name="comments_edit")
     */
    public function editCommentaire(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('posts_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('blog/editComment.html.twig', [
            'comment' => $comment,
            'Commentform' => $form->createView(),

        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="commentsdelete")
     */
    public function deleteComment($id, Posts $post)
    {

        $comment = $this->getDoctrine()->getRepository(Comments::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('afficher_posts', ['id' => $post->getId()],);
    }



  

    /**
     * @Route("/comment/CommentbyidsJSON/", name="commentby")
     */
    public function Allcomments(Request $request, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repository->findAll();
        $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }
    // /**
    //  * je peux liker ou unliker un article
    //  * @Route("/post/{id}/like", name="post_like" )
    //  * @param Posts $post
    //  * @param PostLikeRepository $likeRepo
    //  */
    // public function like(Posts $post, EntityManagerInterface $entityManager, PostLikeRepository $likeRepo) 

    // {
    //     $user=$this->getUser();

    //     if(!$user)
    //     return $this->json([
    //         'code'=>403,
    //         'message'=>'il faut etre connecté'
    //     ],403);
        
    //     if($post->isLikedByUser($user))
    //     {

    //         $like=$likeRepo->findOneBy([
    //          'post'=>$post,
    //          'user'=>$user

    //         ]);

    //         $entityManager->remove($like);
    //         $entityManager->flush();

    //         return $this->json([
    //             'code'=>200,
    //             'message'=>'like bien supprimé',
    //             'likes'=>$likeRepo->count(['post'=>$post],
    //             200)
    //         ]);
    //     }
         
    //     $like=new PostLike();
    //     $like->setPost($post)
    //          ->setUser($user);
    //          $entityManager->persist($like);
    //          $entityManager->flush();
             
    //          return $this->json([
    //             'code'=>200,
    //             'message'=>'like bien ajouté',
    //             'likes'=>$likeRepo->count(['post'=>$post],
    //             200)
    //         ]);


    //             return $this->json(['code'=>200,'message'=>'Ca marche tres bien'],200);

    // }

     /**
     * @Route("/post/{id}/like", name="post_like")
     */
    public function like(Posts $post, EntityManagerInterface $manager, PostLikeRepository $likeRepo)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['code' => 403, 'error' => 'Vous devez être connecté !'], 403);
        }

        if ($post->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy(['post' => $post, 'user' => $user]);

            $manager->remove($like);
            $manager->flush();

            return $this->json(['code' => 200, 'likes' => $likeRepo->count(['post'=>$post])
        
        
        ], 200);
        }

        $like = new PostLike();
        $like->setPost($post)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json(['code' => 200, 'likes' => $likeRepo->count(['post'=>$post])
    
    
    ], 200);
    }

    /**
     * @Route("/rating/{id}/{nbr}", name="add_rating")
     */
    public function addRating(Request $request,Posts $post,int $nbr) {
        
        $rating = new Rating();
        $rating->setPost($post);
        $rating->setNbrEtoiles($nbr);

        $user = $this->getUser();

        $rating->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($rating);
            $em->flush($rating);
            return $this->redirectToRoute('posts_index');
   }
    /**
     * @Route("/Allposts", name="afficher_toutposts" )
     */
    public function afficherposts(NormalizerInterface $normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Posts::class);
        $posts = $repository->findAll();
        $jsonContent = $normalizer->normalize($posts, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }

    

      /**
     * @Route("/CommentbyidsJSON/", name="commentbyid")
     */
    public function commentsbyID(Request $request, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repository->findAll();
        $output = array();

        foreach($comment as $event)
        {
           // $output['id']=$event->getId();
           // $output['content']=$event->getContent();
           // $output['created_at']=$event->getCreatedAt()->format("d-m-y");
            $output[] = array($event->getId(), $event->getContent(),$event->getCreatedAt()->format("d-m-y"));

        }
        
        
        return new JsonResponse($output);
       // return new Response(json_encode($output));
        
       // $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
       // return new Response(json_encode($jsonContent));
    }


    
      /**
     * @Route("/postjson/", name="postjson")
     */
    public function afficherpostsjson(Request $request,NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Posts::class);
        $comment = $repository->findAll();
        
        

        $output = array();

        foreach($comment as $event)
        {
          
            $output[] = array('id'=>$event->getId(),'content'=> $event->getContent(),'title'=>$event->getTitle(),'Objet'=>$event->getObjet(),'created_at'=>$event->getCreatedAt()->format("d-m-y"),'picture'=>$event->getPicture(),'nbre de like'=>$event->getLikes()->count(),'les commentaires'=>$event->getComments()->count());
          

        }
        return new JsonResponse($output);
    }
     /**
     * @Route("/postbyidsJSON/{id}", name="postbyid")
     */
    public function postssbyID(Request $request,$id, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(Posts::class);
        $post = $repository->find($id);
        //$comment = new Comments();
       // $comment=$post->getComments();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }
    


    /**
     * @Route("/commentsbyidpost/{id}", name="commentbypost")
     */
    public function commentsbyidpost(Request $request,$id, NormalizerInterface $Normalizer)
    {
        
        $repository = $this->getDoctrine()->getRepository(Posts::class);
        $post = $repository->find($id);
        $comment = new Comments();
        $comment=$post->getComments();
        $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }


    /**
     * @Route("/detailpostjs", name="detail_articlejs")
     * @Method("GET")
     */
    public function detailpostAction(Request $request)
    {
        $id = $request->get("id");
        $article = $this->getDoctrine()->getManager()->getRepository(Posts::class)->find($id);
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
       $normalizer->setCircularReferenceHandler(function ($object) {
           // return $object->getId();
                   
                          
        });
        $serializer = new Serializer([$normalizer], [$encoder]);
        $formatted = $serializer->normalize($article);
        return new JsonResponse($formatted);
    }



      /**
     * @Route("/detailcommentjs", name="detail_commentjs")
     * @Method("GET")
     */
    public function detailcommentAction(Request $request)
    {
        $id = $request->get("id");
        $article = $this->getDoctrine()->getManager()->getRepository(Comments::class)->find($id);
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
       $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
                   
                          
        });
        $serializer = new Serializer([$normalizer], [$encoder]);
        $formatted = $serializer->normalize($article);
        return new JsonResponse($formatted);
    }
    

   /**
    * @Route("/ajoutCJson/new/" , name="ajoutJsonComment")
    */
    public function AjoutJComments(Request $request, NormalizerInterface $Normalizer)

    {

        //$post = $this->getDoctrine()->getManager()->getRepository(Posts::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $comment=new Comments();
       // $dateImmutable = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', strtotime('now')); # also tried using \DateTimeImmutable
      // $date = DateTimeImmutable::createFromFormat('j-M-Y', 'now');
      // $date = \DateTimeImmutable('Y-m-d H:i:s', strtotime('Wed, 21 Jul 2010 00:28:50 GMT'));
      $date=new \DateTimeImmutable();


        //$date= DateTime::createFromFormat('Y-m-d H:i:s', strtotime('now'));
        $comment->setContent($request->get('content'));
        $user=$this->getUser();
        $comment->setUser($user);
       // $comment->setPoste($post);
        $comment->setCreatedAt($date);

        $em->persist($comment);
        $em->flush();
        $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));

    }

   /**
    * @Route("/updateCJson/{id}" , name="updateJsonComment")
    */
    public function updateJComments(Request $request, NormalizerInterface $Normalizer, $id)

    {   $em=$this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comments::class)->find($id);

        
         $comment->setContent($request->get('content'));

 
        $em->flush();
        $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));

    }

   /**
    * @Route("/deleteCJson/{id}" , name="deleteComment")
    */
    public function deleteJComments(Request $request, NormalizerInterface $Normalizer, $id)

    {
        $em=$this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comments::class)->find($id);

        
       
        $em->remove($comment);
        $em->flush();
        $jsonContent = $Normalizer->normalize($comment, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));

    }

    /**
    * @Route("/ajoutJson/new" , name="ajoutJsonPost")
    */
    public function AjoutJPosts(Request $request, NormalizerInterface $Normalizer)

    {
        $user=$this->getUser();

        $em=$this->getDoctrine()->getManager();
        $post=new Posts();
        $date=new \DateTimeImmutable();
        $post->setContent($request->get('content'));
        $post->setTitle($request->get('title'));
        $post->setObjet($request->get('objet'));
        $post->setPicture($request->get('picture'));

        $post->setUser($user);

        $post->setCreatedAt($date);

        $em->persist($post);
        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));

    }


    /**
    * @Route("/updatepostJson/{id}" , name="updateJsonPost")
    */
    public function updateJPosts(Request $request, NormalizerInterface $Normalizer, $id)

    {

        $em=$this->getDoctrine()->getManager();
        $post=$em->getRepository(Posts::class)->find($id);
        $date=new \DateTimeImmutable();
        $post->setContent($request->get('content'));
        $post->setTitle($request->get('title'));
        $post->setObjet($request->get('objet'));
        $post->setPicture($request->get('picture'));
        $post->setCreatedAt($date);
        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));


    }
   /**
    * @Route("/deletepostJson/{id}" , name="deleteJsonComment")
    */
    public function deleteJpost( NormalizerInterface $Normalizer, $id)

    {
        $em=$this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);

        
       
        $em->remove($post);
        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));

    }
}
