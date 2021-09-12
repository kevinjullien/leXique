<?php

class Mot
{
    private $_id;
    private $_libelle;
    private $_illustration;
    private $_definition;
    private $_variants_orthographiques;
    private $_synonymes;
    private $_antonymes;
    private $_champsLexicaux;
    private $_periodes;
    private $_siecles;
    private $_references;

    /**
     * @param string $libelle
     * @param string|null $definition
     */
    public function __construct(string $libelle, ?string $definition)
    {
        $this->_libelle = $libelle;
        $this->_definition = $definition;
    }

    /**
     * @return mixed
     */
    public function getId(): string
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
    public function getLibelle(): string
    {
        return $this->_libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle): void
    {
        $this->_libelle = $libelle;
    }

    /**
     * @return mixed
     */
    public function getIllustration()
    {
        return $this->_illustration;
    }

    /**
     * @param mixed $illustration
     */
    public function setIllustration($illustration): void
    {
        $this->_illustration = $illustration;
    }

    /**
     * @return string|null
     */
    public function getDefinition(): ?string
    {
        return $this->_definition;
    }

    /**
     * @param mixed $definition
     */
    public function setDefinition($definition): void
    {
        $this->_definition = $definition;
    }

    /**
     * @return array with types as index and array as value, containing the different values
     */
    public function getVariantsOrthographiques(): array
    {
        return $this->_variants_orthographiques;
    }

    /**
     * @param array $variants_orthographiques with types as index and array as value, containing the different values
     */
    public function setVariantsOrthographiques(array $variants_orthographiques): void
    {
        $this->_variants_orthographiques = $variants_orthographiques;
    }

    /**
     * @return array
     */
    public function getSynonymes(): array
    {
        return $this->_synonymes;
    }

    /**
     * @param array $synonymes
     */
    public function setSynonymes(array $synonymes): void
    {
        $this->_synonymes = $synonymes;
    }

    /**
     * @return array
     */
    public function getAntonymes(): array
    {
        return $this->_antonymes;
    }

    /**
     * @param array $antonymes
     */
    public function setAntonymes(array $antonymes): void
    {
        $this->_antonymes = $antonymes;
    }

    /**
     * @return array
     */
    public function getChampsLexicaux(): array
    {
        return $this->_champsLexicaux;
    }

    /**
     * @param array $champsLexicaux
     */
    public function setChampsLexicaux(array $champsLexicaux): void
    {
        $this->_champsLexicaux = $champsLexicaux;
    }

    /**
     * @return array
     */
    public function getPeriodes(): array
    {
        return $this->_periodes;
    }

    /**
     * @param array $periodes
     */
    public function setPeriodes(array $periodes): void
    {
        $this->_periodes = $periodes;
    }

    /**
     * @return array
     */
    public function getSiecles(): array
    {
        return $this->_siecles;
    }

    /**
     * @param array $siecles
     */
    public function setSiecles(array $siecles): void
    {
        $this->_siecles = $siecles;
    }

    /**
     * @return array
     */
    public function getReferences(): array
    {
        return $this->_references;
    }

    /**
     * @param array $references
     */
    public function setReferences(array $references): void
    {
        $this->_references = $references;
    }

}