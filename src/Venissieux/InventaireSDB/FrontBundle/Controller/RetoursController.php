<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\RetoursSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controleur lié aux retours de prêts
 */
class RetoursController extends Controller {

    /**
     * Lance la recherche des retours
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Création du formulaire
            $form = $this->createForm(RetoursSearchType::class);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);


            //Une recherche a été lancée
            if ($form->isSubmitted()) {
                
                
                //Récupération des données du formulaire
                $data = $form->getData();

                //Si le bouton valider est cliqué dans le cas d'un retour
                if ($form->get('valider')->isClicked()) {
                    $logger->info('Validation du retour');

                    //On récupère la date de retour
                    
                    $dateRetour = $data['dateRetour'];

                    dump($dateRetour);

                    //lister tous les paramètres de la requête HTTP
                    foreach ($request->request->all() as $keyParametreRetour => $valueParametreRetour) {
                        //On vérifie que le paramètre de retour correspond à une case à cocher
                        if (substr($keyParametreRetour, 0, 12) == 'retour_pret_') {

                            //Mise à jour en BDD
                            $idRetour = $valueParametreRetour;
                            if (isset($idRetour)) {
                                // modification d'un prêt existant : on récupère les données en BDD
                                $pret = $em->find('VenissieuxInventaireSDBFrontBundle:Pret', $idRetour);

                                //Déclenchement d'une exception si le prêt n'existe pas
                                if (!$pret) {

                                    throw new NotFoundHttpException("Prêt non trouvé");
                                }
                                //Mise à jour de la date de retour
                                $pret->setDateRetour($dateRetour);
                                //Enregistrement en BDD
                                $em->persist($pret);
                                $em->flush();

                                //$logger->info('Modification du projet ' . $projet->getLibelle());
                            }
                        }
                    }

                }

                $logger->info('Lancement d\'une recherche des prets');
                

                //Construction de la requête de recherche en fonction des critères saisis
                $qb = $em->createQueryBuilder();

                //Critère usager
                if ($data['usager']) {
                    $qb->select('p')
                            ->from('VenissieuxInventaireSDBFrontBundle:Pret', 'p')
                            ->orderBy('p.datePret', 'ASC');
                    $qb->andWhere('p.dateRetour is null');
                    $qb->andWhere('p.usager = :idUsager')
                            ->setParameter('idUsager', $data['usager']->getId());

                    $logger->info('critère idUsager : ' . $data['usager']->getId());
                }


                //Lancement de la requête de recherche
                $query = $qb->getQuery();
                $prets = $query->getResult();
            } else {
                //On recherche tous les usagers
                /*  $usagers = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Usager')->findBy(array(), array('Recherche'=>'ASC'));

                  $logger->info('Recherche de tous les usagers'); */

                $prets = null;
            }

            //Affichage de la vue twig de liste des prets
            return $this->render('VenissieuxInventaireSDBFrontBundle:Retours:lister.html.twig', array('form' => $form->createView(), 'prets' => $prets));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
