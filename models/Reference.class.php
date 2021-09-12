<?php


class Reference
{
    private $_id;
    private $_auteur;
    private $_titre;
    private $_editeur;
    private $_lieuEdition;
    private $_dateEdition;
    private $_pages;
    private $_lien;
    private $_document;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getAuteur()
    {
        return $this->_auteur;
    }

    /**
     * @param mixed $auteur
     */
    public function setAuteur($auteur): void
    {
        $this->_auteur = $auteur;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->_titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre): void
    {
        $this->_titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getEditeur()
    {
        return $this->_editeur;
    }

    /**
     * @param mixed $editeur
     */
    public function setEditeur($editeur): void
    {
        $this->_editeur = $editeur;
    }

    /**
     * @return mixed
     */
    public function getLieuEdition()
    {
        return $this->_lieuEdition;
    }

    /**
     * @param mixed $lieuEdition
     */
    public function setLieuEdition($lieuEdition): void
    {
        $this->_lieuEdition = $lieuEdition;
    }

    /**
     * @return mixed
     */
    public function getDateEdition()
    {
        return $this->_dateEdition;
    }

    /**
     * @param mixed $dateEdition
     */
    public function setDateEdition($dateEdition): void
    {
        $this->_dateEdition = $dateEdition;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages): void
    {
        $this->_pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getLien()
    {
        return $this->_lien;
    }

    /**
     * @param mixed $lien
     */
    public function setLien($lien): void
    {
        $this->_lien = $lien;
    }

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document): void
    {
        $this->_document = $document;
    }


}