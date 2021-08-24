<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\ContactType;
use App\Form\RechercheType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DiyController extends AbstractController
{
    // /**
    //  * @Route("/", name="app_home")
    //  */
    // public function index()
    // {


    //     // if ($form->isSubmitted() && $form->isValid()) {}


    //     return $this->render('diy/index.html.twig', []);
    // }

    // Affichage des 8 dernières annonces à l'accueil
    // Gestion formulaire de recherche

    /**
     * @Route("/", name="app_home")
     */
    public function aperçuAnnonce(Request $request, AnnoncesRepository $annoncesRepository)
    {
        // $repository = variable par défaut symfony visant la class voulu
        // get Repository va aller au niveau des données dans la table précisée
        // SELECT query
        $repository = $this->getDoctrine()->getRepository(Annonces::class);
        // a ce stade il a accès au données
        // je veux stocker dans la variable $selections TOUT mes selections
        $annonces = $annoncesRepository->findBy(['active' => true], ['created_at' => 'desc'], 8);
        //La méthode findAll() retourne toutes les entités. Le format du retour est un simple Array, que vous pouvez parcourir (avec un foreach par exemple) pour utiliser les objets qu'il contient
        // entre guillmet c'est le nom utilisé sur Twig

        $form = $this->createForm(RechercheType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Appel méthide repository

            $annonces = $annoncesRepository->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData()
            );
        }

        return $this->render('diy/index.html.twig', ['annonces' => $annonces, 'form' => $form->createView()]);
    }

    /**
     * @Route("/a", name="home")
     */
    public function recherche(Request $request, AnnoncesRepository $annoncesRepository)
    {
        // $repository = variable par défaut symfony visant la class voulu
        // get Repository va aller au niveau des données dans la table précisée
        // SELECT query
        $repository = $this->getDoctrine()->getRepository(Annonces::class);
        // a ce stade il a accès au données
        // je veux stocker dans la variable $selections TOUT mes selections
        $annonces = $annoncesRepository->findBy(['active' => true], ['created_at' => 'desc'], 8);
        //La méthode findAll() retourne toutes les entités. Le format du retour est un simple Array, que vous pouvez parcourir (avec un foreach par exemple) pour utiliser les objets qu'il contient
        // entre guillmet c'est le nom utilisé sur Twig

        $form = $this->createForm(RechercheType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Appel méthide repository

            $annonces = $annoncesRepository->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData()
            );
        }

        return $this->render('base.html.twig', ['annonces' => $annonces, 'form' => $form->createView()]);
    }


    /**
     * @Route("/details/{slug}", name="details")
     */
    public function detailsAnnonce($slug, AnnoncesRepository $annoncesRepository)
    {
        $annonce = $annoncesRepository->findOneBy(['slug' => $slug]);

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }

        return $this->render(
            'annonces/details.html.twig',
            compact('annonce')
        );
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailerInterface)
    {

        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = (new TemplatedEmail())
                // récupère la donnée 'email' lors de la soumission du formulaire stockée dans $contact
                ->from($contact->get('email')->getData())
                // envoi à la donnée email récupérer dans user 
                ->to('contact@diy.fr')
                ->subject('Nouveau contact DIY!')
                ->htmlTemplate('diy/email.html.twig')
                // envoi de toutes les variables à utiliser dans le twig
                ->context([
                    // /!\ $email est une variable réservée = utiliser autre $
                    'nom' => $contact->get('nom')->getData(),
                    'prenom' => $contact->get('prenom')->getData(),
                    'objet' => $contact->get('objet')->getData(),
                    'tel' => $contact->get('tel')->getData(),
                    'mail' => $contact->get('email')->getData(),
                    'message' => $contact->get('message')->getData()
                ]);
            $mailerInterface->send($email);

            $this->addFlash('message', 'Votre message à bien été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render(
            'diy/contact.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
