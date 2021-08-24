<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  @Route("/admin/annonces", name="admin_annonces_")
 * @package App\Controller
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepository)
    {
        $annonces = $annoncesRepository->findAll();
        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annonces
        ]);
    }

    ////// Activer une annonce ////
    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonce)
    {
        $annonce->setActive(($annonce->getActive()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return new Response("true");
    }

    ////// Supprimer une annonce /////

    /**
     * @Route("/supprimer/{id<\d+>}", name="supprimer")
     */
    public function supprimerAnnonce(Request $request, Annonces $annonce)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($annonce);
        $em->flush();

        $this->addFlash('message', "L'annonce à bien été supprimée");

        return $this->redirectToRoute("admin_annonces_home");
    }
}
