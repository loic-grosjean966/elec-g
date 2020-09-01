<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;
use App\Entity\User;
use App\Entity\Opinion;
use App\Form\OpinionFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     * Cette méthode permet de générer la vue d'accueil ainsi que le formulaire d'avis, la zone d'affichage des avis
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        //Création d'un avis vide
        $newOpinion=new Opinion();

        //Création d'un formulaire de création d'avis lié à $newOpinion
        $opinionForm= $this->createForm(OpinionFormType::class, $newOpinion);

        // Récupération de l'user actuellement connecté
        $userConnected = $this->getUser();
        //Liaison des données de requête (POST) avec le formulaire
        $opinionForm->handleRequest($request);

        //Si le formulaire est envoyé
        if($opinionForm->isSubmitted() && $opinionForm->isValid())
        {
            $newOpinion
                ->setPublicationDate(new DateTime())
                ->setAuthor($userConnected)
            ;

            // Sauvegarde de l'avis en base de données via le manager général des entités
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newOpinion);
            $entityManager->flush();

            //Message flash de type "success"
            $this->addFlash('success', 'Votre avis a été publié avec succès !');

            //suppression des 2 variables contenant le formulaire validé et l'avis nouvellement crée (pour éviter que le nouveau formulaire soit rempli avec)
            unset($newOpinion);
            unset($opinionForm);

            // création d'un nouvel avis vide et de son formulaire lié
            $newOpinion = new Opinion();
            $opinionForm = $this->createForm(OpinionFormType::class, $newOpinion);
        }

        // Récupération et affichage des avis sur la page d'accueil

         // On récupère dans l'url la données GET page (si elle n'existe pas, la valeur retournée par défaut sera la page 1)
         $requestedPage = $request->query->getInt('page', 1);

        // Si le numéro de page demandé dans l'url est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }
 
        // Récupération du manager des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête qui servira au paginator pour récupérer les avis de la page courante
        $query = $em->createQuery('SELECT a FROM App\Entity\Opinion a');

        // On stocke dans $pageArticles les 10 articles de la page demandée dans l'URL
        $opinions = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les avis
            6     // Nombre d'avis par page
        );

        return $this->render('main/index.html.twig', [
            'user' => $userConnected,
            'opinion' => $newOpinion,
            'opinions' => $opinions,
            'opinionForm' => $opinionForm->createView()
        ]);
    }

    /**
     * @Route("/contact-us", name="contactez_nous")
     */
    public function contact()
    {
        return $this->render('main/contact.html.twig', []);
    }
}