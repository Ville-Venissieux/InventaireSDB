<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\PretsSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

        try 
        {
            //Affichage de la vue twig de liste des prets
            return $this->render('VenissieuxInventaireSDBFrontBundle:Prets:lister.html.twig');
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
