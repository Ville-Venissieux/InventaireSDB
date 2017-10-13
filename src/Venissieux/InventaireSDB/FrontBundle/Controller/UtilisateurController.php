<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\UtilisateurType;
use Venissieux\InventaireSDB\FrontBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controleur lié aux utilisateurs
 */
class UtilisateurController extends Controller {

    /**
     * Lance l'affichage de la liste des utilisateurs
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
                //On recherche tous les utilisateurs
                $utilisateurs = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Utilisateur')->findBy(array(), array('username'=>'ASC'));

                $logger->info('Recherche de tous les utilisateurs');
                
            //Affichage de la vue twig de liste des utilisateurs
            return $this->render('VenissieuxInventaireSDBFrontBundle:Utilisateur:lister.html.twig', array( 'utilisateurs' => $utilisateurs));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Ajout ou modification d'un utilisateur
     * @param Request $request
     * @param type $id
     * @return RedirectResponse
     */
    public function editerAction(Request $request, $id = null) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Recherche de l'utilisateur concerné
            if (isset($id)) {
                // modification d'un utilisateur existant : on récupère les données en BDD
                $utilisateur = $em->find('VenissieuxInventaireSDBFrontBundle:Utilisateur', $id);

                //Déclenchement d'une exception si l'utilisateur n'existe pas
                if (!$utilisateur) {
                    throw new NotFoundHttpException("Utilisateur non trouvé");
                }

                $logger->info('Modification de l\'utilisateur ' . $utilisateur->getUsername());
            } else {

                //nouvel utilisateur
                $utilisateur = new Utilisateur();

                $logger->info('Saisie d\'un nouvel utilisateur');
            }

            //Création du formulaire et association à l'objet concerné
            $form = $this->createForm(UtilisateurType::class, $utilisateur);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            //Enregistrement en BDD lorsque le formulaire est valide
            if ($form->isSubmitted() && $form->isValid()) {
                
                $utilisateur->setEmail($utilisateur->getUsername().'@ville-venissieux.fr');
                $utilisateur->setPlainPassword($utilisateur->getUsername());
                $utilisateur->setEnabled(true);
                $em->persist($utilisateur);
                $em->flush();

                $logger->info('Enregistrement de l\'utilisateur ' . $utilisateur->getUsername());

                //Renvoi vers la liste des utilisateurs
                return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_utilisateur_lister'));
            }

            //Affichage de la vue twig d'édition des utilisateurs
            return $this->render('VenissieuxInventaireSDBFrontBundle:Utilisateur:editer.html.twig', array('form' => $form->createView()));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    

    /**
     * Suppression d'un utilisateur
     * @param type $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function supprimerAction(Request $request, $id) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Recherche de l'utilisateur concerné
            $utilisateur = $em->find('VenissieuxInventaireSDBFrontBundle:Utilisateur', $id);

            //Déclenchement d'une exception si l'utilisateur n'existe pas
            if (!$utilisateur) {
                throw new NotFoundHttpException("Utilisateur non trouvé");
            }

            $logger->info('Suppression de l\'utilisateur ' . $utilisateur->getUsername());

            //Suppression de l'utilisateur en BDD
            $em->remove($utilisateur);
            $em->flush();

            //Renvoi vers la liste des utilisateurs
            return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_utilisateur_lister'));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
