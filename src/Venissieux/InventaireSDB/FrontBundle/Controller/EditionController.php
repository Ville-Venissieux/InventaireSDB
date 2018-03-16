<?php

namespace Venissieux\InventaireSDB\FrontBundle\Controller;

use Venissieux\InventaireSDB\FrontBundle\Form\EditionType;
use Venissieux\InventaireSDB\FrontBundle\Utils\PdfGenerator;
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


                //Détermination du rapport à générer (articles ou mouvements d'article d'un usager)
                if ($data['historique']) {
                    //Génération du pdf des articles d'un usager avec historique de mouvement
                    PdfGenerator::generateHistoryExport($pdf, $categorie, $usager);
                } else {
                    //Génération du pdf des articles sans historique de mouvement
                    PdfGenerator::generateBasicExport($pdf, $categorie, $usager, $articles);
                }

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
