<?php

namespace Venissieux\InventaireSDB\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pret
 *
 * @ORM\Table(name="pret")
 * @ORM\Entity(repositoryClass="Venissieux\InventaireSDB\FrontBundle\Repository\PretRepository")
 */
class Pret
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_pret", type="date")
     */
    private $datePret;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_retour", type="date", nullable=true)
     */
    private $dateRetour;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Usager",inversedBy="prets")
     */
    private $usager;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Article",inversedBy="prets")
     */
    private $article;
    
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
     * Set datePret
     *
     * @param \DateTime $datePret
     *
     * @return Pret
     */
    public function setDatePret($datePret)
    {
        $this->datePret = $datePret;

        return $this;
    }

    /**
     * Get datePret
     *
     * @return \DateTime
     */
    public function getDatePret()
    {
        return $this->datePret;
    }

    /**
     * Set dateRetour
     *
     * @param \DateTime $dateRetour
     *
     * @return Pret
     */
    public function setDateRetour($dateRetour)
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }

    /**
     * Get dateRetour
     *
     * @return \DateTime
     */
    public function getDateRetour()
    {
        return $this->dateRetour;
    }

    /**
     * Set usager
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Usager $usager
     *
     * @return Pret
     */
    public function setUsager(\Venissieux\InventaireSDB\FrontBundle\Entity\Usager $usager = null)
    {
        $this->usager = $usager;

        return $this;
    }

    /**
     * Get usager
     *
     * @return \Venissieux\InventaireSDB\FrontBundle\Entity\Usager
     */
    public function getUsager()
    {
        return $this->usager;
    }

    /**
     * Set article
     *
     * @param \Venissieux\InventaireSDB\FrontBundle\Entity\Article $article
     *
     * @return Pret
     */
    public function setArticle(\Venissieux\InventaireSDB\FrontBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Venissieux\InventaireSDB\FrontBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
