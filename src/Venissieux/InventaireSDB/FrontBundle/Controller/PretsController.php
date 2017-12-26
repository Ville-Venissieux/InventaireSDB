<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\PretType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venissieux\InventaireSDB\FrontBundle\Entity\Pret;
use Venissieux\InventaireSDB\FrontBundle\Entity\Etat;

/**
 * Controleur lié aux prêts
 */
class PretsController extends Controller {

    /**
     * Lance la recherche des prêts
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');


        try {
            //Création du formulaire
            $form = $this->createForm(PretType::class);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            //Recherche des prêts concernant l'usager sélectionné
            $logger->info('Lancement d\'une recherche des prets');

            
            if ($form->isSubmitted()) {
                //Récupération des données du formulaire
                $data = $form->getData();
            } else {
                //Premier affichage : le formulaire affiche les prêts en cours du premier usager par ordre alphabétique
                $usager = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Usager')->findOneBy(array(), array('nom' => 'ASC'));
                $form->get('usager')->setData($usager);
                $data['usager'] = $usager;
            }
            
            
            //Construction de la requête de recherche des prêts en cours de l'usager
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
            
            



            //Validation des prêts / retours si le bouton Valider a été cliqué
            if ($form->get('valider')->isClicked() && $form->isValid()) {
                

                //Liste des ID d'articles prêtés à l'usager après opération de prêts / retours
                $IDListeApres = explode(";", $request->request->all()["hidListeResultatsArticlesEmprunts"], -1);
                
                
                 //Liste des ID d'articles prêtés à l'usager avant opération de prêts / retours
                $IDListeAvant = explode(";", $request->request->all()["hidListeResultatsArticlesInitiaux"], -1);


                //Récupération de la date de prêt et de l'usager
                $datePret = $data['dateOperation'];
                $usagerPret = $data['usager'];

                //Enregistrement des changements en BDD
                
                //Traitement des nouveaux prêts (on compare chaque article prêté actuellement à la liste des prêts antérieurs)
                foreach ($IDListeApres as $IDApres) {
                    if (!in_array($IDApres, $IDListeAvant)) {
                        intval($IDApres);
                        $ajouterpret = new Pret();
                        $ajouterpret->setDatePret($datePret);
                        $ajouterpret->setUsager($usagerPret);
                        $article = $em->find('VenissieuxInventaireSDBFrontBundle:Article', $IDApres);
                        $ajouterpret->setArticle($article);
                        $em->persist($ajouterpret);
                        $em->flush();
                    }
                }
                
                //Traitement des retours (on compare chaque article prêté antérieurement à la liste actuelle des prêts)
                foreach ($IDListeAvant as $IDAvant) {
                    if (!in_array($IDAvant, $IDListeApres)) {
                        $editerpret = $em->getRepository('VenissieuxInventaireSDBFrontBundle:Pret')->findOneBy(array('article' => $IDAvant, 'dateRetour' => NULL));
                        $editerpret->setDateRetour($data['dateOperation']);
                        $em->persist($editerpret);
                        $em->flush();
                    }
                }
            }

            //Lancement de la requête de recherche des prêts (suite ou non à mise à jour) 
            $query = $qb->getQuery();
            $prets = $query->getResult();



            //Affichage de la vue twig de liste des prets
            return $this->render('VenissieuxInventaireSDBFrontBundle:Prets:lister.html.twig', array('form' => $form->createView(), 'prets' => $prets));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

   /**
    * pagination de la liste des articles disponibles (AJAX)
    * @param Request $request
    * @return Response
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

            //Récupération des id des articles empruntés depuis le champ caché
            $listeArticlesEmpruntes = array();
            $articlesEmpruntes = $request->get('articlesEmpruntes');
            if (!empty($articlesEmpruntes)) {
                $listeArticlesEmpruntes = explode(';', $articlesEmpruntes, -1);
            }


            //Récupération des id des articles empruntés initialement depuis le champ caché puis détermination des articles retournés
            $listeArticlesRetournes = array();
            $articlesEmpruntesInitiaux = $request->get('articlesEmpruntesInitiaux');
            if (!empty($articlesEmpruntesInitiaux)) {
                $listeArticlesEmpruntesInitiaux = explode(';', $articlesEmpruntesInitiaux, -1);
                //Comparaison avec la liste des articles empruntés pour déterminer les articles initiaux retournés
                foreach ($listeArticlesEmpruntesInitiaux as $articleEmprunteInitial) {
                    if (!in_array($articleEmprunteInitial, $listeArticlesEmpruntes)) {
                        array_push($listeArticlesRetournes, $articleEmprunteInitial);
                    }
                }
            }

            $sortColumn = $request->get('columns')[$request->get('order')[0]['column']]['data'];
            $sortDirection = $request->get('order')[0]['dir'];

            //Lancement de la recherche pour les articles disponibles
            $articles = $this->getDoctrine()->getRepository('VenissieuxInventaireSDBFrontBundle:Article')->search(
                    $filters, $start, $length, $sortColumn, $sortDirection, true, $listeArticlesEmpruntes, $listeArticlesRetournes
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

    /**
     * Pagination de la liste des articles (AJAX)
     * @param Request $request
     * @return Response
     */
    public function modifierEtatAction(Request $request) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');
        
        //Libellé de l'état à afficher
        $libelleNouvelEtat = '';

        try {

            //Recherche de l'article à modifier
            $article = $em->find('VenissieuxInventaireSDBFrontBundle:Article', $request->get('idArticle'));

            //On vérifie que l'article possède un état
            if ($article->getEtat() !== null) {
                //Dans le cas d'une amélioration de l'état de l'article
                if ($request->get('ameliorer') == 'true') {
                    //On vérifie que l'article n'est pas à l'état excellent (on ne pourrait pas améliorer son état dans ce cas)
                    if ($article->getEtat()->getId() < Etat::EXCELLENT) {

                        //Définition du nouvel état
                        $nouvelEtat = $em->find('VenissieuxInventaireSDBFrontBundle:Etat', $article->getEtat()->getId() + 1);
                        $article->setEtat($nouvelEtat);
                        $logger->info('Modification de l\' article  n° ' . $article->getId() . ' avec l\'état ' . $nouvelEtat->getLibelle());

                        //Enregistrement de l'article en BDD
                        $em->persist($article);
                        $em->flush();
                        $libelleNouvelEtat = $nouvelEtat->getLibelle();
                        $logger->info('Enregistrement de l\'article n° ' . $article->getId());
                    }
                } else {
                    
                    //On vérifie que l'article n'est pas à l'état moyen (on ne pourrait pas dégrader son état dans ce cas)
                    if ($article->getEtat()->getId() > Etat::MOYEN) {

                        //Définition du nouvel état
                        $nouvelEtat = $em->find('VenissieuxInventaireSDBFrontBundle:Etat', $article->getEtat()->getId() - 1);
                        $article->setEtat($nouvelEtat);
                        $logger->info('Modification de l\' article  n° ' . $article->getId() . ' avec l\'état ' . $nouvelEtat->getLibelle());

                        
                        //Enregistrement de l'article en BDD
                        $em->persist($article);
                        $em->flush();
                        $libelleNouvelEtat = $nouvelEtat->getLibelle();
                        $logger->info('Enregistrement de l\'article n° ' . $article->getId());
                    }
                }
            }

            
            return new Response($libelleNouvelEtat, 200, ['Content-Type' => 'text/plain']);
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
