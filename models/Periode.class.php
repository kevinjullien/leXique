<?php

class Periode
{
    private $_id;
    private $_nom;
    private $_debut;
    private $_fin;
    private $_description;
    private $_linkedMots;

    /**
     * @param string $_nom
     * @param string|null $_description
     */
    public function __construct(string $_nom, string $_description = NULL)
    {
        $this->_nom = $_nom;
        $this->_description = $_description;
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
     * @return string
     */
    public function getNom(): string
    {
        return $this->_nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->_nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getDebut()
    {
        return $this->_debut;
    }

    /**
     * @param mixed $debut
     */
    public function setDebut($debut): void
    {
        $this->_debut = $debut;
    }

    /**
     * @return mixed
     */
    public function getFin()
    {
        return $this->_fin;
    }

    /**
     * @param mixed $fin
     */
    public function setFin($fin): void
    {
        $this->_fin = $fin;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->_description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->_description = $description;
    }

    /**
     * @return mixed
     */
    public function getLinkedMots()
    {
        return $this->_linkedMots;
    }

    /**
     * @param mixed $linkedMots
     */
    public function setLinkedMots($linkedMots): void
    {
        $this->_linkedMots = $linkedMots;
    }


}