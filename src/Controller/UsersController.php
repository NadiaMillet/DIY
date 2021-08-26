<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Entity\Users;
use App\Entity\Images;
use App\Form\EditProfileType;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {

        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }


    //////// Ajout d'une annonce par les utilisateurs //////

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request)
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // va effectuer la requête d'UPDATE en base de données
            $file = $annonce->getImg1();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg1($fileName);


            $file = $annonce->getImg2();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg2($fileName);


            $file = $annonce->getImg3();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg3($fileName);

            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em = $this->getDoctrine()->getManager();

            // Affilier en cascade les images à une annonce
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }


    //////// Modifier une annonce //////

    /**
     * @Route("/users/annonces/editer/{id}", name="users_annonces_editer")
     */
    public function editerAnnonce(Request $request, Annonces $annonce)
    {

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // va effectuer la requête d'UPDATE en base de données
            $file = $annonce->getImg1();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg1($fileName);


            $file = $annonce->getImg2();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg2($fileName);


            $file = $annonce->getImg3();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $annonce->setImg3($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(), 'annonces' => $annonce
        ]);
    }


    ////// Supprimer une annonce /////

    /**
     * @Route("users/annonce/supprimer/{id<\d+>}", name="users_annonce_supprimer")
     */
    public function supprimerAnnonce(Request $request, Annonces $annonce)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($annonce);
        $em->flush();

        $this->addFlash('message', "L'annonce à bien été supprimée");

        return $this->redirectToRoute('users');
    }


    // //////// Modifier profil utilisateur //////

    // /**
    //  * @Route("/users/profil/edit", name="users_profil_modifier")
    //  */
    // public function editerProfil(Request $request)
    // {
    //     $user = $this->getUser();
    //     $form = $this->createForm(EditProfileType::class, $user);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($user);
    //         $em->flush();

    //         $this->addFlash('message', 'Votre profil à bien été mis à jour');

    //         return $this->redirectToRoute('users');
    //     }

    //     return $this->render('users/annonces/editeprofile.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }

    //////// Modifier mot de passe utilisateur //////

    /**
     * @Route("/users/motdepasse/edit", name="users_mdp_modifier")
     */
    public function editerMotDePasse(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            if ($request->request->get('password') == $request->request->get('password2')) {

                $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
                $em->flush();
                $this->addFlash('success', 'Votre mot de passe à bien été modifié');

                return $this->redirectToRoute('users');
            } else {
                $this->addFlash('error', 'Les mots de passe saisis ne sont pas identiques');
            }
        }

        return $this->render('users/annonces/editmdp.html.twig');
    }
}
