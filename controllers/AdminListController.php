<?php

class AdminListController
{
    private $_db;

    public function __construct(Db $db)
    {
        $this->_db = $db;
    }


    public function run()
    {
        if (empty($_SESSION['auth']) || !$_SESSION['admin']) {
            header("Location: index.php");
            die();
        }

        if (isset($_GET['scope'])) {
            if ($_GET['scope'] === "words") {
                //TODO améliorer pour adapter la requête à la DB selon la demande, et donc alléger la quantité de données transférées
                $mots = $this->_db->select_complete_mots();

                if (isset($_GET['filter']) && !empty($_GET['filter']) && $_GET['filter'] !== "all") {
                    $newList = array();
                    switch ($_GET['filter']) {
                        case "def":
                            foreach ($mots as $i => $mot) {
                                if (empty($mot->getDefinition()) || !strcmp($mot->getDefinition(), "<p>&nbsp;</p>")) $newList[] = $mot;
                            }
                            break;
                        case "syn":
                            foreach ($mots as $mot) {
                                if (count($mot->getSynonymes()) == 0) $newList[] = $mot;
                            }
                            break;
                        case "ant":
                            foreach ($mots as $mot) {
                                if (count($mot->getAntonymes()) == 0) $newList[] = $mot;
                            }
                            break;
                        case "lex":
                            foreach ($mots as $mot) {
                                if (count($mot->getChampsLexicaux()) == 0) $newList[] = $mot;
                            }
                            break;
                        case "cen":
                            foreach ($mots as $mot) {
                                if (count($mot->getSiecles()) == 0) $newList[] = $mot;
                            }
                            break;
                        case "per":
                            foreach ($mots as $mot) {
                                if (count($mot->getPeriodes()) == 0) $newList[] = $mot;
                            }
                            break;
                        case "ref":
                            foreach ($mots as $mot) {
                                if (count($mot->getReferences()) == 0) $newList[] = $mot;
                            }
                            break;
                    }
                    $mots = $newList;
                }
                require_once(VIEW_PATH . 'adminListMot.php');
            } else if ($_GET['scope'] === "lex") {
                $champsLexicaux = $this->_db->select_complete_champs_lexicaux();
                require_once(VIEW_PATH . 'adminListLex.php');
            } else if ($_GET['scope'] === "per") {
                $periodes = $this->_db->select_complete_periodes();
                require_once(VIEW_PATH . 'adminListPer.php');
            }
        } else {
            require_once(VIEW_PATH . 'errorPage.php');
        }
    }
}