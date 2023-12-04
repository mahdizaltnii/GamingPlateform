<?php
namespace App\Controller;
use App\Entity\Event;
use App\Entity\Categorie;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Swift_Mailer;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }



 




    /**
     * @Route("/listevenement", name="evenement_pdf", methods={"GET"})
     */
   
  
    public function listpdf(EventRepository $EventRepository): Response
    {
$pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    
    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    $event = $EventRepository->findAll();
       
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('event/pdf.html.twig', [
        'event' => $event,
    ]);
    
    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => false
    ]);

}


/**
     * @Route("/DESCr", name="DESCr")
     */
    public function TrierParNomr(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findByNamer();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }
    /**
     * @Route("/ASCr", name="ASCr")
     */
    public function TrierParNomdesr(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findByNameascr();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }























    /**
     * @Route("/new", name="event_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, Swift_Mailer $mailer): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            }catch(FileException $e){

            }
            $entityManager->persist($event);
            $event->setPicture($fileName);
            $this->mail($mailer);
            $entityManager->flush();
             return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/showFront", name="event_show_front")
     */
    public function readFront(Request $request, PaginatorInterface $paginator)
    {
   $donnees = $this->getDoctrine()->getRepository(Event::class)->findBy([]);
   $event = $paginator->paginate(
       $donnees,
       $request->query->getInt('page' ,1),
       4
   );





         return $this->render('event/showfront.html.twig', array(
            'events' => $event,

        ));
    
}







    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }















    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"POST"})
     */
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
    }


/**
     * @Route("/search1/{searchString}", name="search")
     */
    public function searchEnt($searchString,SerializerInterface $serializer)
    {
        
        //$serializer = new Serializer([new ObjectNormalizer()]);
      
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $users = $repository->findByExampleField($searchString);
        $data = $serializer->normalize($users,'json',['groups'=>'post:read']);
        return new JsonResponse($data);
    }






    public function mail( \Swift_Mailer $mailer)
{
    $message = (new \Swift_Message('Vous Avez Ajouter un évenement!!'))
        ->setFrom('narjes.gawa@esprit.tn')
        ->setTo('zouhour.gawa@esprit.tn')
        ->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'emails/registration.html.twig',
              
            ),
            'text/html'
            
        );
        $mailer->send($message);
        // you can remove the following code if you don't define a text version for your emails
        //->addPart(
            //$this->renderView(
                // templates/emails/registration.txt.twig
               // 'emails/registration.txt.twig',
                
            //),
           // 'text/plain'
            
    //;

    $mailer->send($message);
        }


























        

/**
     * @Route("/event/add3", name="add_event")
     * @Method("GET")
     */



    public function AddEvent(Request $request, NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
   
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $em=$this->getDoctrine()->getManager();
    $event=new Event();
    $categorie1 = new Categorie();
    $categorie = new Categorie();
   
    $categorie1 = $request->get('Categorie');
    $repository = $this->getdoctrine()->getRepository(Categorie::class);
    $categorie = $repository->findOneBy(array('id' => $categorie1));

    if (!$categorie) {
        throw $this->createNotFoundException(
            'No product found for id '
        );
    }else
    { 
            $event->setCategorie($categorie);
            $event->setName($request->get('Name'));
            $event->setDescription($request->get('description'));
            $event->setDate(date_create_from_format("Y-m-d",$request->get("date")));
            $event->setLieu($request->get('lieu'));
            $event->setPicture($request->get('picture'));
           
            
            
   
    $em->persist($event);
    $em->flush();
    $jsonContent=$Normalizer->normalize($event,'json',['groups'=>'post:read']);
    
    return new Response(json_encode($jsonContent));
          
    }


















    }


/**
     * @Route("/event/updateevent/{id}", name="update_event")
     * @Method("PUT")
     */
    public function modifierEventAction(Request $request, NormalizerInterface $Normalizer) {
        $em = $this->getDoctrine()->getManager();
        $Event = $this->getDoctrine()->getManager()
            ->getRepository(Event::class)
            ->find($request->get("id"));
            $Name = $request->query->get("Name");
            $description = $request->query->get("description");
            $lieu = $request->query->get("lieu");
            $date = $request->query->get("date");
            $picture = $request->query->get("picture");

           
            $Event->setName($request->get('Name'));
            $Event->setDescription($request->get('description'));
            $Event->setDate(date_create_from_format("Y-m-d",$request->get("date")));
            $Event->setLieu($request->get('lieu'));
            $Event->setPicture($request->get('picture'));

        $em->persist($Event);
        $em->flush();
        $jsonContent=$Normalizer->normalize($Event,'json',['groups'=>'post:read']);
    
    return new Response(json_encode($jsonContent));
          
    }



 /**
     * @Route("/event/deleteevent", name="delete_event")
     * @Method("DELETE")
     */

    public function deleteEventAction(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $Event = $em->getRepository(event::class)->find($id);
        if($Event!=null ) {
            $em->remove($Event);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Votre Event a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id Event invalide.");


    }
    /**
     * @Route("/event/liste2",name="liste_event")
     */
    
    
    public function getEvent(NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
    $repository =$this->getDoctrine()->getRepository(Event::class);
    $Event=$repository->FindAll();
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $jsonContent=$Normalizer->normalize($Event,'json',['groups'=>'post:read']);
    
    
    
    return new Response(json_encode($jsonContent));
    dump($jsonContent);
    die;}
}