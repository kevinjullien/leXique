<?php

class HomeController
{
    private $_randomMot;
    private $_db;

    public function __construct(Db $db, ?string $randomMot)
    {
        $this->_randomMot = $randomMot;
        $this->_db = $db;
    }


    public function run()
    {
        $texte = 'Bonjour!<br>
                Le site est actuellement en cours de construction. <br>
                Vous pouvez faire preuve de patience ou consulter les oracles pour connaître les changements à venir.<br>';
        $randomMot = $this->_db->select_mot_by_id($this->_randomMot);
        require_once(VIEW_PATH . 'home.php');
    }

}