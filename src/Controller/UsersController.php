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

            // // récupération des images
            // $images = $form->get('images')->getData();

            // // Ayant la possibilité d'un upload mutilples => boucle
            // foreach ($images as $image) {
            //     // nom unique.extension
            //     $fichier = md5(uniqid()) . '.' . $image->guessExtension();
            //     // emplacement
            //     $image->move(
            //         $this->getParameter('images_directory'),
            //         $fichier
            //     );

            //     // stockage 

            //     //Création d'une nouvelle instance
            //     $img = new Images;
            //     $img->setName($fichier);

            //     // Ajout de l'image à l'annonce
            //     $annonce->addImage($img);


            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em = $this->getDoctrine()->getManager();

            // Affilier en cascade les images à une annonce
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }



        return $this->render('users/annonces/ajout.html.twig', [
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


    // // //////// Supprimer une image //////

    // /**
    //  * @Route("/users/images/supprimer/{id}", name="users_images_supprimer", methods={"DELETE"})
    //  */

    // public function supprimerImage(Images $image, Request $request)
    // {
    //     // sécurisation de la route avec un token CRSF


    //     //json_deco = Récupère une chaîne encodée JSON et la convertit en une variable PHP. Prend en params la chaîne de caractères json à décoder.

    //     // récupération des données transférées en json puis les décoder
    //     $data = json_decode($request->getContent(), true);

    //     // Vérification de la validité du token CRSF = Protection contre les CRSF => En sécurité des systèmes d'information, le cross-site request forgery, abrégé CSRF (parfois prononcé sea-surf en anglais) ou XSRF, est un type de vulnérabilité des services d'authentification web.L’objet de cette attaque est de transmettre à un utilisateur authentifié une requête HTTP falsifiée qui pointe sur une action interne au site, afin qu'il l'exécute sans en avoir conscience et en utilisant ses propres droits. L’utilisateur devient donc complice d’une attaque sans même s'en rendre compte. L'attaque étant actionnée par l'utilisateur, un grand nombre de systèmes d'authentification sont contournés.

    //     //Un jeton CSRF est une valeur unique, secrète et imprévisible qui est générée par l'application côté serveur et transmise au client de manière à être incluse dans une requête HTTP ultérieure effectuée par le client. Lorsque la demande ultérieure est effectuée, l'application côté serveur valide que la demande inclut le jeton attendu et rejette la demande si le jeton est manquant ou non valide. Les jetons CSRF peuvent empêcher les attaques CSRF en empêchant un attaquant de construire une requête HTTP entièrement valide adaptée à l'alimentation d'un utilisateur victime. Étant donné que l'attaquant ne peut pas déterminer ou prédire la valeur du jeton CSRF d'un utilisateur, il ne peut pas construire une requête avec tous les paramètres nécessaires pour que l'application honore la requête.

    //     if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
    //         // récupération du nom de l'image
    //         $nomImage = $image->getName();
    //         // supression du fichier
    //         unlink($this->getParameter('images_directory') . '/' . $nomImage);

    //         // suppression dans base
    //         $em = $this->getDoctrine()->getManager();
    //         $em->remove($image);
    //         $em->flush();

    //         // réponse json
    //         return new JsonResponse(['success' => 1]);
    //     } else {
    //         return new JsonResponse((['error' => 'Token Invalid', 400]));
    //     }
    //     return $this->redirectToRoute('users_images_supprimer');
    // }

    // /**
    //  * @Route("/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
    //  */
    // public function deleteImage(Images $image, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);

    //     // On vérifie si le token est valide
    //     if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
    //         // On récupère le nom de l'image
    //         $nom = $image->getName();
    //         // On supprime le fichier
    //         unlink($this->getParameter('images_directory') . '/' . $nom);

    //         // On supprime l'entrée de la base
    //         $em = $this->getDoctrine()->getManager();
    //         $em->remove($image);
    //         $em->flush();

    //         // On répond en json
    //         return new JsonResponse(['success' => 1]);
    //     } else {
    //         return new JsonResponse(['error' => 'Token Invalide'], 400);
    //     }
    // }

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
