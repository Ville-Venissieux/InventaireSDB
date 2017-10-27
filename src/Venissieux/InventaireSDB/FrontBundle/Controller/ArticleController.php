<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\ArticleType;
use Venissieux\InventaireSDB\FrontBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controleur lié aux articles
 */
class ArticleController extends Controller {

    /**
     * Lance la recherche des articles
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository et du logger
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Affichage de la vue twig de liste des articles
            return $this->render('VenissieuxInventaireSDBFrontBundle:Article:lister.html.twig');
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Ajout ou modification d'un articles
     * @param Request $request
     * @param type $id
     * @return RedirectResponse
     */
    public function editerAction(Request $request, $id = null) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {
            //Recherche de l'article concerné
            if (isset($id)) {
                // modification d'un article existant : on récupère les données en BDD
                $article = $em->find('VenissieuxInventaireSDBFrontBundle:Article', $id);

                //Déclenchement d'une exception si l'article n'existe pas
                if (!$article) {
                    throw new NotFoundHttpException("Article non trouvé");
                }

                $logger->info('Modification de l\' article  n° ' . $article->getId());
            } else {

                //nouvel article
                $article = new Article();

                $logger->info('Saisie d\'un article');
            }

            //Création du formulaire et association à l'objet concerné
            $form = $this->createForm(ArticleType::class, $article);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            //Enregistrement en BDD lorsque le formulaire est valide
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($article);
                $em->flush();

                $logger->info('Enregistrement de l\'article n° ' . $article->getId());

                //Renvoi vers la liste des articles
                return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_article_lister'));
            }

            //Affichage de la vue twig d'édition des articles
            return $this->render('VenissieuxInventaireSDBFrontBundle:Article:editer.html.twig', array('form' => $form->createView()));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Suppression d'un article
     * @param type $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function supprimerAction(Request $request, $id) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Recherche du article concerné
            $article = $em->find('VenissieuxInventaireSDBFrontBundle:Article', $id);

            //Déclenchement d'une exception si  l'article n'existe pas
            if (!$article) {
                throw new NotFoundHttpException("Article non trouvé");
            }

            $logger->info('Suppression de l\'article ' . $article->getNom());

            //Suppression de l'article en BDD
            $em->remove($article);
            $em->flush();

            //Renvoi vers la liste des articles
            return new RedirectResponse($this->container->get('router')->generate('venissieux_inventaire_SDB_front_article_lister'));
        } catch (Exception $e) {
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
                    $filters, $start, $length, $sortColumn, $sortDirection,false,null,null
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
                    'statut' => $article->getStatut(),
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
