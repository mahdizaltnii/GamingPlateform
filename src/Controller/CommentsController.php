<?php

namespace App\Controller;
use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("admin/comments")
     */
class CommentsController extends AbstractController
{
    /**
     * @Route("/", name="comments")
     */
    // public function index(): Response
    // {
    //     return $this->render('comments/index.html.twig', [
    //         'controller_name' => 'CommentsController',
    //     ]);
    // }

      /**
     * @Route("/", name="list_comments", methods={"GET"})
     */
    public function index(CommentsRepository $commentsRepository): Response
    {
        return $this->render('back-office/comments/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

     /**
     * @Route("/ajout", name="ajout_comments", methods={"GET","POST"})
     */
    public function ajout(Request $request): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                       
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('list_comments', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back-office/comments/ajout.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="voir_comments", methods={"GET"})
     */
    public function voir(Comments $comment): Response
    {
        return $this->render('back-office/comments/voir.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier_comments", methods={"GET","POST"})
     */
    public function modifier(Request $request, comments $comment): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_comments');
        }

        return $this->render('back-office/comments/modifier.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

   

    /**
     * @Route("/supprimer/{id}", name="supprimer_comments")
     */
    public function delete($id){
        $comment= $this->getDoctrine()->getRepository(Comments::class)->find($id);
        $em= $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute("list_comments");
    }
}
