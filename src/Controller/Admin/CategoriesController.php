<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $categoriesRepository)
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    ///// Ajout de categorie ////

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutCategorie(Request $request)
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('message', "La catégorie à bien été ajoutée");
            return $this->redirectToRoute('admin_categories_home');
        }
        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }


    ///// Modifier une catégorie ////
    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierCategorie(Categories $categorie, Request $request)
    {

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }
        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    ////// Supprimer une catégories /////

    /**
     * @Route("/supprimer/{id<\d+>}", name="supprimer")
     */
    public function supprimerAnnonce(Request $request, Categories $categories)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($categories);
        $em->flush();

        $this->addFlash('message', "La catégorie à bien été supprimée");

        return $this->redirectToRoute("admin_categories_home");
    }
}
