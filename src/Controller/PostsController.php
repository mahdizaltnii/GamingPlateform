<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * @Route("admin/posts")
 */
class PostsController extends AbstractController
{
    /**
     * @Route("/", name="list_posts", methods={"GET"})
     */
    public function index(PostsRepository $postsRepository): Response
    {
        return $this->render('back-office/posts/index.html.twig', [
            'posts' => $postsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout", name="ajout_posts", methods={"GET","POST"})
     */
    public function ajout(Request $request): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        /** @var UploadedFile $uploadedFile */
                        $uploadedFile = $form['imageFile']->getData();
                        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
                        $uploadedFile->move(
                            $destination,
                            $newFilename
                        );
                        $post->setpicture($newFilename);
                
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('list_posts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back-office/posts/ajout.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="voir_posts", methods={"GET"})
     */
    public function voir(Posts $post): Response
    {
        return $this->render('back-office/posts/voir.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier_posts", methods={"GET","POST"})
     */
    public function modifier(Request $request, Posts $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_posts');
        }

        return $this->render('back-office/posts/modifier.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

   

    /**
     * @Route("/supprimer/{id}", name="supprimer_posts")
     */
    public function delete($id){
        $post= $this->getDoctrine()->getRepository(Posts::class)->find($id);
        $em= $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute("list_posts");
    }
}
