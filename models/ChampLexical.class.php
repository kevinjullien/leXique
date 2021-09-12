<?php

class ChampLexical
{
    private $_id;
    private $_intitule;
    private $_description;
    private $_linkedMots;

    /**
     * @param string $intitule
     * @param string|null $description
     */
    public function __construct(string $intitule, string $description = NULL)
    {
        $this->_intitule = $intitule;
        $this->_description = $description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getIntitule(): string
    {
        return $this->_intitule;
    }

    /**
     * @param string $intitule
     */
    public function setIntitule(string $intitule): void
    {
        $this->_intitule = $intitule;
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
    public function setLinkedMots(array $linkedMots): void
    {
        $this->_linkedMots = $linkedMots;
    }


}