<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\PretType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controleur lié aux prêts
 */
class PretsController extends Controller 
{

    /**
     * Lance la recherche des prêts
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) 
    {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');
        
        
        
        
        
        //Création du formulaire
            $form = $this->createForm(PretType::class);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);
            
            //Recherche des prêts concernant l'usager sélectionné
            $logger->info('Lancement d\'une recherche des prets');
            
            //Soumission du formulaire : En cas de recherche de prets à partir d'un nom (cas 1) ou d'une validation des retours (cas 2)
            if ($form->isSubmitted()) {
                //Récupération des données du formulaire
                $data = $form->getData();
            } else {
                //Premier affichage
                $usager = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Usager')->findOneBy(array(), array('nom' => 'ASC'));
                $form->get('usager')->setData($usager);
                $data['usager'] = $usager;
            }
            

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

            

//            //cas 2 : validation des retours
//            if ($form->get('valider')->isClicked() && $form->isValid()) {
//
//                $logger->info('Validation des retours');
//
//                //On récupère la date de retour
//                $dateRetour = $data['dateRetour'];
//
//                $logger->info('Date de retour : ' . $dateRetour->format('Y-m-d'));
//
//                //Récupération de tous les paramètres de la requête HTTP pour déterminer les cases cochées
//                foreach ($request->request->all() as $keyParametreRetour => $valueParametreRetour) {
//
//                    //On vérifie que le paramètre de retour correspond à une case à cocher
//                    if (substr($keyParametreRetour, 0, 12) == 'retour_pret_') {
//
//                        //Récupération de l'id du prêt concerné
//                        $idPret = $valueParametreRetour;
//
//                        $logger->info('Id du prêt concerné : ' . $idPret);
//
//                        if (isset($idPret)) {
//
//                            // modification d'un prêt existant : on récupère les données en BDD
//                            $pret = $em->find('VenissieuxInventaireSDBFrontBundle:Pret', $idPret);
//
//                            //Déclenchement d'une exception si le prêt n'existe pas
//                            if (!$pret) {
//                                throw new NotFoundHttpException("Prêt non trouvé");
//                            }
//
//                            //Mise à jour de la date de retour
//                            $pret->setDateRetour($dateRetour);
//
//                            //Enregistrement en BDD
//                            $em->persist($pret);
//                            $em->flush();
//
//                            $logger->info('Enregistrement du retour : ' . $idPret);
//                        }
//                    }
//                }
//            }

            

            //Lancement de la requête de recherche
            $query = $qb->getQuery();
            $prets = $query->getResult();
        
        
        

        try 
        {
            //Affichage de la vue twig de liste des prets
            return $this->render('VenissieuxInventaireSDBFrontBundle:Prets:lister.html.twig', array('form' => $form->createView(), 'prets' => $prets));
        } 
        catch (Exception $e) 
        {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }
    /**
     * pagination de la liste des articles (AJAX)
     * @return Response JSON
     */
    public function paginerAction(Request $request) {
        
//Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Récupération des paramètres de la requête HTTP
            
            $length = $request->get('length');
            $length = $length && ($length != -1) ? $length : 0;

            $start = $request->get('start');
            $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];

            $sortColumn = $request->get('columns')[$request->get('order')[0]['column']]['data'];
            $sortDirection = $request->get('order')[0]['dir'];

            //Lancement de la recherche
            $articles = $this->getDoctrine()->getRepository('VenissieuxInventaireSDBFrontBundle:Article')->search(
                    $filters, $start, $length, $sortColumn, $sortDirection
            );

            //Création du tableau de données nécessaire pour la réponse HTTP
            $output = array(
                'data' => array(),
                'recordsFiltered' => count($this->getDoctrine()->getRepository('VenissieuxInventaireSDBFrontBundle:Article')->search($filters, 0, false)),
                'recordsTotal' => count($this->getDoctrine()->getRepository('VenissieuxInventaireSDBFrontBundle:Article')->search(array(), 0, false))
            );

            foreach ($articles as $article) {

                $logger->info('article ' . $article->getNom());


                $output['data'][] = [
                    'id' => $article->getId(),
                    'nom' => $article->getNom(),
                    'categorie' => is_null($article->getCategorie()) ? '' : $article->getCategorie()->getLibelle(),
                    'dateAchat' => is_null($article->getDateAchat()) ? '' : $article->getDateAchat()->format('Y'),
                    'etat' => is_null($article->getEtat()) ? '' : $article->getEtat()->getLibelle(),
                    'commentaire' => mb_strimwidth($article->getCommentaire(), 0, 50, "...")
                ];
            }

            return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
            
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }
}
