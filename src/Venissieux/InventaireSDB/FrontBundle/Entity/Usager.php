<?php

namespace Venissieux\InventaireSDB\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usager
 *
 * @ORM\Table(name="usager")
 * @ORM\Entity(repositoryClass="Venissieux\InventaireSDB\FrontBundle\Repository\UsagerRepository")
 */
class Usager
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
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100)
     */
    private $prenom;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Pret",mappedBy="usager")
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
     * @return Usager
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Usager
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }
    
    /**
     * Get prénom + nom
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->prenom." ".$this->nom;
    }

    /**
     * Add pret
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Pret $pret
     *
     * @return Usager
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
