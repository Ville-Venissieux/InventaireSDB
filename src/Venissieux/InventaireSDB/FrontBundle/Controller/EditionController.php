<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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


        //Appel du logger
        $logger = $this->get('logger');

        try {
            //Affichage de la vue twig de liste des editions
            return $this->render('VenissieuxInventaireSDBFrontBundle:Edition:exporter.html.twig');
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }
    
    
    
    /**
     * Export PDF des articles
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function exporterAction(Request $request) {

        //Appel du repository
        $em = $this->container->get('doctrine')->getManager();
        $logger = $this->get('logger');

        try {

            //Recherche de l'projet concernée
           // $projet = $em->find('VenissieuxSuiviMandatFrontBundle:Projet', $id);

//            //Déclenchement d'une exception si l'projet n'existe pas
//            if (!$projet) {
//                throw new NotFoundHttpException("Projet non trouvé");
//            }


            //Création du rapport en HTML
           // $html = $this->renderView('VenissieuxInventaireSDBFrontBundle:Projet:export.html.twig', array('a' => $projet));

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
            $pdf->AddPage();

            //Transformation du html en pdf
            $pdf->writeHTML("Bonjour je m'appelle John Wayne", true, false, true, false, '');
            $pdf->lastPage();

            //Envoi du fichier pdf à télécharger
            $response = new Response($pdf->Output('Articles.pdf', 'D'));
            $response->headers->set('Content-Type', 'application/pdf');

          //  $logger->info('Export de l\'projet ' . $projet->getLibelle());

            return $response;
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('Erreur', 'Veuillez contacter votre administrateur');
            $logger->error($e->getMessage());
        }
    }

    
    
}
