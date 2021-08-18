<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Entity\Users;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //////// Modifier profil utilisateur //////

    /**
     * @Route("/users/profil/edit", name="users_profil_modifier")
     */
    public function editerProfil(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Votre profil à bien été mis à jour');

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/editeprofile.html.twig', [
            'form' => $form->createView()
        ]);
    }

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
