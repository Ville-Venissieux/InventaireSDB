<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\EditionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controleur lié aux editions
 */
class EditionController extends Controller {

    /**
     * Lance l'écran des éditions
     * @param Request $request
     * @return type
     */
    public function listerAction(Request $request) {


        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Création du formulaire
            $form = $this->createForm(EditionType::class);

            //réception de la requête dans l'objet formulaire
            $form->handleRequest($request);

            if ($form->isSubmitted()) {


                $logger->info('Lancement d\'une édition');

                //Récupération des données du formulaire
                $data = $form->getData();

                //Construction de la requête de recherche des articles en fonction des critères saisis
                $qb = $em->createQueryBuilder();

                $qb->select('a')
                        ->from('VenissieuxInventaireSDBFrontBundle:Article', 'a')
                        ->orderBy('a.id', 'ASC');


                if (isset($data['categorie'])) {
                    $qb->leftJoin('a.categorie', 'c');
                    $qb->andWhere('c.id = :searchCategorie')->setParameter('searchCategorie', $data['categorie']->getId());
                    $logger->info('critère categorie : ' . $data['categorie']->getLibelle());
                }

                //Si on ne prend que les articles disponibles
                if ($data['disponible'] == '2') {

                    $sub = $em->createQueryBuilder();
                    $sub->select('p1');
                    $sub->from('VenissieuxInventaireSDBFrontBundle:Pret', 'p1');
                    $sub->andWhere('p1.article = a');
                    $sub->andWhere('p1.dateRetour is null');

                    //Ajout de la condition not exists
                    $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));

                    $logger->info('critère disponibilité : ' . $data['disponible']);
                }


                //Si on ne prend que les articles prêtés
                if ($data['disponible'] == '3') {
                    
                    //Un article est prêté si sa date de retour est absente
                    $qb->innerJoin('a.prets', 'p');
                    $qb->andWhere('p.dateRetour is null');

                    //Si on ne prends que les articles prêtés à un usager
                    if (isset($data['usager'])) {
                        $qb->innerJoin('p.usager', 'u');
                        $qb->andWhere('u.id = :searchUsager')->setParameter('searchUsager', $data['usager']->getId());
                        $logger->info('critère usager : ' . $data['usager']->getNomComplet());
                    }
                    
                    $logger->info('critère disponibilité : ' . $data['disponible']);
                }


                $query = $qb->getQuery();
                $articles = $query->getResult();



                //Création du rapport en HTML
                $html = $this->renderView('VenissieuxInventaireSDBFrontBundle:Edition:exportArticles.html.twig', array('articles' => $articles));
                //Création de l'export PDF
                $pdf = $this->get("white_october.tcpdf")->create();
                $pdf->SetAuthor('Ville de Vénissieux');
                $pdf->SetTitle('Articles ');
                $pdf->SetSubject('Export de l\'projet');
                $pdf->SetKeywords('Inventaire SDB, PDF, article');
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
                $pdf->SetMargins(15, 0, 15, true);
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                $pdf->AddPage("L");


                // Modification des paramètres mémoire de php en raison des ressources demandées par writeHTML
                $original_mem = ini_get('memory_limit');
                ini_set('memory_limit', '640M');
                ini_set('max_execution_time', 240); // 240 secondes (4 minutes)
                //Transformation du html en pdf
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->lastPage();

                // On remet la limite mémoire à son état initial
                ini_set('memory_limit', $original_mem);

                //Envoi du fichier pdf à télécharger
                $response = new Response($pdf->Output('Articles.pdf', 'D'));
                $response->headers->set('Content-Type', 'application/pdf');

                //  $logger->info('Export de l\'projet ' . $projet->getLibelle());

                return $response;
            }




            //Affichage de la vue twig de liste des editions
            return $this->render('VenissieuxInventaireSDBFrontBundle:Edition:exporter.html.twig', array('form' => $form->createView()));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

}
