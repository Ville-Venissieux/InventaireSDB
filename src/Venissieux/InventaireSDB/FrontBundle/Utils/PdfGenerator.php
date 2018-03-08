<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Venissieux\InventaireSDB\FrontBundle\Utils;

/**
 * Ajoute l'utilisateur comme information de log
 *
 * @author slonchampt
 */
class PdfGenerator {

    /**
     * Génère un export contenant les articles suivant les filtres définis
     * @param type $pdf $pdf à générer
     * @param type $categorie filtre sur la catégorie
     * @param type $usager filtre sur l'usager
     * @param type $articles les articles
     */
    public static function generateBasicExport($pdf,$categorie,$usager,$articles) {

        //Création de l'export PDF
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
        $pdf->Cell(0, 20, $titre, 0, 0, 'C', 0);
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
    }

}
