<?php

namespace Venissieux\InventaireSDB\FrontBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Venissieux\InventaireSDB\FrontBundle\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_achat", type="date", nullable=true)
     */
    private $dateAchat;

    /**
     * @var int
     *
     * @ORM\Column(name="prix_achat", type="decimal", precision=19, scale=4,nullable=true)
     */
    private $prixAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=100, nullable=true)
     */
    private $fournisseur;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=500, nullable=true)
     */
    private $commentaire;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Categorie")
     */
    private $categorie;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Etat")
     */
    private $etat;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Pret",mappedBy="article",cascade={"remove"})
     */
    private $prets;

     public function __construct() {
        $this->prets = new ArrayCollection();
    }
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Article
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }
    
    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        //booléen de vérification 
        $emprunte = false;
        
        //boucle de recherche d'un pret sans date de retour
        foreach ($this->prets as $pret)
        {
            if ($pret->getDateRetour()==false)
            {
                 $emprunte = true;
                 break;
            } 
        }
        
        return $emprunte?"emprunté":"disponible";
            
    }
    

    /**
     * Set dateAchat
     *
     * @param \DateTime $dateAchat
     *
     * @return Article
     */
    public function setDateAchat($dateAchat)
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    /**
     * Get dateAchat
     *
     * @return \DateTime
     */
    public function getDateAchat()
    {
        return $this->dateAchat;
    }


    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return Article
     */
    public function setFournisseur($fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Article
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set categorie
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Categorie $categorie
     *
     * @return Article
     */
    public function setCategorie(\Venissieux\InventaireSDB\FrontBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \Venissieux\InventaireSDB\FrontBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set etat
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Etat $etat
     *
     * @return Article
     */
    public function setEtat(\Venissieux\InventaireSDB\FrontBundle\Entity\Etat $etat = null)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return \Venissieux\InventaireSDB\FrontBundle\Entity\Etat
     */
    public function getEtat()
    {
        return $this->etat;
    }
    

    /**
     * Set prixAchat
     *
     * @param string $prixAchat
     *
     * @return Article
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return string
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Add pret
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Pret $pret
     *
     * @return Article
     */
    public function addPret(\Venissieux\InventaireSDB\FrontBundle\Entity\Pret $pret)
    {
        $this->prets[] = $pret;

        return $this;
    }

    /**
     * Remove pret
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Pret $pret
     */
    public function removePret(\Venissieux\InventaireSDB\FrontBundle\Entity\Pret $pret)
    {
        $this->prets->removeElement($pret);
    }

    /**
     * Get prets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrets()
    {
        return $this->prets;
    }
}
