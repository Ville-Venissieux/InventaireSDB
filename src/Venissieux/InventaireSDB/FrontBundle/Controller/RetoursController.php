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

            //Soumission du formulaire : En cas de recherche de prets à partir d'un nom (cas 1) ou d'une validation des retours (cas 2)
            if ($form->isSubmitted()) {

                //Récupération des données du formulaire
                $data = $form->getData();

                //cas 2 : validation des retours
                if ($form->get('valider')->isClicked() && $form->isValid()) {

                    $logger->info('Validation des retours');

                    //On récupère la date de retour
                    $dateRetour = $data['dateRetour'];

                    $logger->info('Date de retour : ' . $dateRetour->format('Y-m-d'));

                    //Récupération de tous les paramètres de la requête HTTP pour déterminer les cases cochées
                    foreach ($request->request->all() as $keyParametreRetour => $valueParametreRetour) {

                        //On vérifie que le paramètre de retour correspond à une case à cocher
                        if (substr($keyParametreRetour, 0, 12) == 'retour_pret_') {

                            //Récupération de l'id du prêt concerné
                            $idPret = $valueParametreRetour;

                            $logger->info('Id du prêt concerné : ' . $idPret);

                            if (isset($idPret)) {

                                // modification d'un prêt existant : on récupère les données en BDD
                                $pret = $em->find('VenissieuxInventaireSDBFrontBundle:Pret', $idPret);

                                //Déclenchement d'une exception si le prêt n'existe pas
                                if (!$pret) {
                                    throw new NotFoundHttpException("Prêt non trouvé");
                                }

                                //Mise à jour de la date de retour
                                $pret->setDateRetour($dateRetour);

                                //Enregistrement en BDD
                                $em->persist($pret);
                                $em->flush();

                                $logger->info('Enregistrement du retour : ' . $idPret);
                            }
                        }
                    }
                }

                //cas 1 et cas 2 : Recherche des prêts concernant l'usager sélectionné
                $logger->info('Lancement d\'une recherche des prets');

                //Construction de la requête de recherche en fonction des critères saisis
                $qb = $em->createQueryBuilder();

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
                
                $logger->info('Premier affichage de la page des retours');

                //Premier affichage : aucun pret n'est affiché
                $prets = null;
            }

            //Affichage de la vue twig de liste des prets liés à l'utilisateur
            return $this->render('VenissieuxInventaireSDBFrontBundle:Retours:lister.html.twig', array('form' => $form->createView(), 'prets' => $prets));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
