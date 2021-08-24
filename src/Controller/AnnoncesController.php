<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnonceContactType;
use App\Form\RechercheType;
use App\Repository\AnnoncesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annonces", name="annonces_")
 * @package App\Controller
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="liste")
     */
    public function index(
        AnnoncesRepository $annoncesRepository,
        PaginatorInterface $paginatorInterface,
        Request $request
    ) {
        $repository = $this->getDoctrine()->getRepository(Annonces::class);

        // stock dans $data toutes les annonces
        $data = $annoncesRepository->findAll();

        $form = $this->createForm(RechercheType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Appel méthide repository

            $data = $annoncesRepository->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData()
            );
        }

        // stock le résultat de la pagination dans $annonces qui ne contiendra que les annonces de la page que l'on appel
        // appel de la méthode par défaur propore au bundle knp = paginate( 1.données, ici les annonces 2.Savoir où il en est et en INTEGER = page rendra une valeur numérique, si null page=1 = /annonces/?page=1 3.Nombres d'éléments par page) 
        $annonces = $paginatorInterface->paginate(
            $data,
            // Cherche dans la requête un integer stocké dans ce qui va s'appeler 'page'
            $request->query->getInt('page', 1),
            1

        );

        return $this->render(
            'annonces/index.html.twig',
            [
                'annonces' => $annonces,
                'form' => $form->createView()
            ]
        );
    }


    /// Afficher une annonce page unique ////
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function detailsAnnonce($slug, AnnoncesRepository $annoncesRepository, Request $request, MailerInterface $mailerInterface)
    {
        $annonce = $annoncesRepository->findOneBy(['slug' => $slug]);

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }

        $form = $this->createForm(AnnonceContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = (new TemplatedEmail())
                // récupère la donnée 'email' lors de la soumission du formulaire stockée dans $contact
                ->from($contact->get('email')->getData())
                // envoi à la donnée email récupérer dans user 
                ->to($annonce->getUsers()->getEmail())
                ->subject('Nouveau message au sujet de votre annonce"' . $annonce->getTitle() . '"')
                ->htmlTemplate('emails/contact_annonce.html.twig')
                // envoi de toutes les variables à utiliser dans le twig
                ->context([
                    'annonce' => $annonce,
                    // /!\ $email est une variable réservée = utiliser autre $
                    'mail' => $contact->get('email')->getData(),
                    'nom' => $annonce->getUsers()->getFirstname(),
                    'message' => $contact->get('message')->getData()
                ]);
            $mailerInterface->send($email);

            $this->addFlash('message', 'Votre message à bien été envoyé');
            return $this->redirectToRoute('annonces_details', ['slug' => $annonce->getSlug()]);
        }

        return $this->render(
            'annonces/details.html.twig',
            [
                'annonce' => $annonce,
                'form' => $form->createView()
            ]
        );
    }

    //// Ajout d'une annonce en favoris /////
    /**
     * @Route("/favoris/ajout/{id}", name="ajout_favoris")
     */
    public function ajoutFavoris(Annonces $annonce)
    {

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->addFavori($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    //// Retrait d'une annonce en favoris /////
    /**
     * @Route("/favoris/retrait/{id}", name="retrait_favoris")
     */
    public function retraitFavoris(Annonces $annonce)
    {

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->removeFavori($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }
}
