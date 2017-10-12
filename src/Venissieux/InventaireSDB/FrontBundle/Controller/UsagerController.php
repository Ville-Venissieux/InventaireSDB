<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\UsagerSearchType;
use Venissieux\InventaireSDB\FrontBundle\Form\UsagerType;
use Venissieux\InventaireSDB\FrontBundle\Entity\Usager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controleur lié aux usagers
 */
class UsagerController extends Controller {

    /**
     * Lance la recherche des usagers
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Création du formulaire
            $form = $this->createForm(UsagerSearchType::class);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            //Une recherche a été lancée
            if ($form->isSubmitted()) {

                $logger->info('Lancement d\'une recherche');

                //Récupération des données du formulaire
                $data = $form->getData();

                //Construction de la requête de recherche en fonction des critères saisis
                $qb = $em->createQueryBuilder();

                $qb->select('u')
                        ->from('VenissieuxInventaireSDBFrontBundle:Usager', 'u')
                        ->orderBy('u.nom', 'ASC');

               
                //Critère nom
                if ($data['Nom']) {
                    $qb->andWhere('UPPER(u.nom) LIKE UPPER(:searchNom)')
                       ->orWhere('UPPER(u.prenom) LIKE UPPER(:searchNom)')
                       ->setParameter('searchNom', '%' . $data['Nom'] . '%');
                    $logger->info('critère usager : ' . $data['Nom']);
                }
                

                //Lancement de la requête de recherche
                $query = $qb->getQuery();
                $usagers = $query->getResult();
            } else {
                //On recherche tous les usagers
                $usagers = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Usager')->findBy(array(), array('nom'=>'ASC'));
                $logger->info('Recherche de tous les usagers');
            }

            //Affichage de la vue twig de liste des actions
            return $this->render('VenissieuxInventaireSDBFrontBundle:Usager:lister.html.twig', array('form' => $form->createView(), 'usagers' => $usagers));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Ajout ou modification d'un usager
     * @param Request $request
     * @param type $id
     * @return RedirectResponse
     */
    public function editerAction(Request $request, $id = null) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Recherche de l'usager concerné
            if (isset($id)) {
                // modification d'un usager existant : on récupère les données en BDD
                $usager = $em->find('VenissieuxInventaireSDBFrontBundle:Usager', $id);

                //Déclenchement d'une exception si l'usager n'existe pas
                if (!$usager) {
                    throw new NotFoundHttpException("Usager non trouvé");
                }

                $logger->info('Modification de l\' usager ' . $usager->getPrenom().' '.$usager->getNom());
            } else {

                //nouvel usager
                $usager = new Usager();

                $logger->info('Saisie d\'un usager');
            }

            //Création du formulaire et association à l'objet concerné
            $form = $this->createForm(UsagerType::class, $usager);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            //Enregistrement en BDD lorsque le formulaire est valide
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($usager);
                $em->flush();

                $logger->info('Enregistrement de l\'usager ' . $usager->getPrenom().' '.$usager->getNom());

                //Renvoi vers la liste des usagers
                return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_usager_lister'));
            }

            //Affichage de la vue twig d'édition des usagers
            return $this->render('VenissieuxInventaireSDBFrontBundle:Usager:editer.html.twig', array('form' => $form->createView()));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    

    /**
     * Suppression d'un usager
     * @param type $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function supprimerAction(Request $request, $id) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Recherche du usager concerné
            $usager = $em->find('VenissieuxInventaireSDBFrontBundle:Usager', $id);

            //Déclenchement d'une exception si  le usager n'existe pas
            if (!$usager) {
                throw new NotFoundHttpException("Usager non trouvé");
            }

            $logger->info('Suppression de l\'usager ' . $usager->getPrenom().' '.$usager->getNom());

            //Suppression de l'usager en BDD
            $em->remove($usager);
            $em->flush();

            //Renvoi vers la liste des usagers
            return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_usager_lister'));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
