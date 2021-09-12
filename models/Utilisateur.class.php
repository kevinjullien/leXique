<?php


class Utilisateur
{
    private $_pseudo;
    private $_mot_de_passe;
    private $_est_admin;


    public function __construct($pseudo, $mot_de_passe, $est_admin)
    {
        $this->_pseudo = $pseudo;
        $this->_mot_de_passe = $mot_de_passe;
        $this->_est_admin = $est_admin;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->_pseudo;
    }

    /**
     * @return mixed
     */
    public function getMotDePasse()
    {
        return $this->_mot_de_passe;
    }

    /**
     * @return mixed
     */
    public function estAdmin()
    {
        return $this->_est_admin;
    }


}