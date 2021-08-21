<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DiyController extends AbstractController
{
    // /**
    //  * @Route("/", name="app_home")
    //  */
    // public function index()
    // {
    //     return $this->render('diy/index.html.twig');
    // }

    /**
     * @Route("/", name="app_home")
     */
    public function aperçuAnnonce()
    {
        // $repository = variable par défaut symfony visant la class voulu
        // get Repository va aller au niveau des données dans la table précisée
        // SELECT query
        $repository = $this->getDoctrine()->getRepository(Annonces::class);
        // a ce stade il a accès au données
        // je veux stocker dans la variable $selections TOUT mes selections
        $annonces = $repository->findBy(['active' => true], ['created_at' => 'desc'], 8);
        //La méthode findAll() retourne toutes les entités. Le format du retour est un simple Array, que vous pouvez parcourir (avec un foreach par exemple) pour utiliser les objets qu'il contient
        // entre guillmet c'est le nom utilisé sur Twig
        return $this->render('diy/index.html.twig', ['annonces' => $annonces]);
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
}
