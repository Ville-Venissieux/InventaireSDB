<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Venissieux\InventaireSDB\FrontBundle\Utils;

use Venissieux\InventaireSDB\FrontBundle\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Ajoute l'utilisateur comme information de log
 *
 * @author slonchampt
 */
class UserProcessor {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Ajoute l'utilisateur comme information de log
     * @param type $record
     * @return string
     */
    public function processRecord($record) {

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof Utilisateur) {
                $record['username'] = $user->getUsername();
            } else {
                $record['username'] = 'Utilisateur inconnu';
            }
        } else {
            $record['username'] = 'Utilisateur inconnu';
        }

        return $record;
    }

}
