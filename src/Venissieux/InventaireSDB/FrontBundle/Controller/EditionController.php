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

                //Nécessaire pour déterminer si la requete conserne un usager et ou une categorie
                $usager = null;
                $categorie = null;

                //Construction de la requête de recherche des articles en fonction des critères saisis
                $qb = $em->createQueryBuilder();

                $qb->select('a')
                        ->from('VenissieuxInventaireSDBFrontBundle:Article', 'a')
                        ->orderBy('a.id', 'ASC');

                //Filtre sur la catégorie
                if (isset($data['categorie'])) {
                    $categorie = $data['categorie'];

                    $qb->leftJoin('a.categorie', 'c');
                    $qb->andWhere('c.id = :searchCategorie')->setParameter('searchCategorie', $data['categorie']->getId());

                    $logger->info('critère categorie : ' . $data['categorie']->getLibelle());
                }

                //Filtre sur les articles disponibles
                if ($data['disponible'] == '2') {

                    $sub = $em->createQueryBuilder();
                    $sub->select('p1');
                    $sub->from('VenissieuxInventaireSDBFrontBundle:Pret', 'p1');
                    $sub->andWhere('p1.article = a');
                    $sub->andWhere('p1.dateRetour is null');
                    $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));

                    $logger->info('critère disponibilité : ' . $data['disponible']);
                }


                //Filtre sur les articles prêtés
                if ($data['disponible'] == '3') {

                    //Un article est prêté si sa date de retour est absente
                    $qb->innerJoin('a.prets', 'p');
                    $qb->andWhere('p.dateRetour is null');

                    //Filtre sur les articles prêtés à un usager
                    if (isset($data['usager'])) {
                        $usager = $data['usager'];
                        $qb->innerJoin('p.usager', 'u');
                        $qb->andWhere('u.id = :searchUsager')->setParameter('searchUsager', $data['usager']->getId());

                        $logger->info('critère usager : ' . $data['usager']->getNomComplet());
                    }

                    $logger->info('critère disponibilité : ' . $data['disponible']);
                }

                //Exécution de la requête
                $query = $qb->getQuery();
                $articles = $query->getResult();


                //Création de l'export PDF
                $pdf = $this->get("white_october.tcpdf")->create();
                $pdf->SetAuthor('Ville de Vénissieux');
                $pdf->SetTitle('Articles ');
                $pdf->SetSubject('Export des articles');
                $pdf->SetKeywords('Inventaire SDB, PDF, article');
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
                $pdf->SetMargins(15, 0, 15, true);
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                $pdf->AddPage("L");

                //Titre

                $pdf->SetTextColor(0, 110, 153);
                $pdf->SetDrawColor(255, 255, 255);
                $titre = 'Liste des articles';
                if (isset($categorie)) {
                  $titre .= ' de la catégorie ' . $categorie->getLibelle();
                }
                if (isset($usager)) {
                  $titre .= ' détenus par ' . $usager->getNomComplet();
                }
                
                $titre .= ' au ' . date('d/m/Y');
                $pdf->Cell(0, 20,$titre , 0, 0, 'C', 0);
                $pdf->Ln();






                //Gestion des Font
                $pdf->SetFillColor(0, 110, 153);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(0, 110, 153);
                $pdf->SetLineWidth(0.3);



                //Entête
                $pdf->SetFont('helveticaB', '', 8);
                $pdf->Cell(10, 7, 'Id', 0, 0, 'C', 1);
                $pdf->Cell(60, 7, 'Nom', 0, 0, 'C', 1);
                $pdf->Cell(30, 7, 'Catégorie', 0, 0, 'C', 1);
                $pdf->Cell(20, 7, 'Année d\'achat', 0, 0, 'C', 1);
                $pdf->Cell(20, 7, 'Statut', 0, 0, 'C', 1);
                $pdf->Cell(20, 7, 'Etat', 0, 0, 'C', 1);
                $pdf->Cell(120, 7, 'Commentaire', 0, 0, 'C', 1);
                $pdf->Ln();



                $pdf->SetTextColor(0, 110, 153);
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0);

                //Alternance des couleurs de fond des lignes 
                $alternate = true;

                foreach ($articles as $article) {

                    //Gestion des couleurs de fond alternées 
                    $alternate ? $pdf->SetFillColor(255, 255, 255) : $pdf->SetFillColor(221, 245, 255);
                    $alternate = !$alternate;

                    //Création d'une ligne 
                    $pdf->SetFont('helveticaB', '', 8);
                    $pdf->Cell(10, 7, $article->getId(), 0, 0, 'C', 1);
                    //On tronque à 25 caractères pour ne pas dépasser les limites de la cellule
                    $pdf->Cell(60, 7, mb_strimwidth($article->getNom(), 0, 25, "...", "UTF-8"), 0, 0, 'C', 1);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->Cell(30, 7, $article->getCategorie() ? $article->getCategorie()->getLibelle() : '', 0, 0, 'C', 1);
                    $pdf->Cell(20, 7, $article->getDateAchat() ? $article->getDateAchat()->format('Y') : '', 0, 0, 'C', 1);
                    $pdf->Cell(20, 7, $article->getStatut(), 0, 0, 'C', 1);
                    $pdf->Cell(20, 7, $article->getEtat() ? $article->getEtat()->getLibelle() : '', 0, 0, 'C', 1);
                    //On tronque à 70 caractères pour ne pas dépaaser les limites de la cellule
                    $pdf->Cell(120, 7, mb_strimwidth($article->getCommentaire(), 0, 70, "...", "UTF-8"), 0, 0, 'C', 1);
                    $pdf->Ln();
                }

                //Ajout d'un commentaire sur l'usager si existant
                if (isset($usager)) {
                    $pdf->Ln();
                    $pdf->Cell(15, 7, 'Commentaire', 0, 0, 'C', 0);
                    $pdf->MultiCell(250, 7, $usager->getCommentaire(), 0, 'C', 0);
                }


                $pdf->lastPage();


                //Envoi du fichier pdf à télécharger
                $response = new Response($pdf->Output('Articles.pdf', 'D'));
                $response->headers->set('Content-Type', 'application/pdf');

                $logger->info('Edition des articles terminée');

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
