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
                switch ($_GET['filter']??"all") {
                    case "def":
                        $mots = $this->_db->select_complete_mots_with_invalid_definition();
                        break;
                    case "syn":
                        $mots = $this->_db->select_complete_mots_with_no_synonyme();
                        break;
                    case "ant":
                        $mots = $this->_db->select_complete_mots_with_no_antonyme();
                        break;
                    case "lex":
                        $mots = $this->_db->select_complete_mots_with_no_champ_lexical();
                        break;
                    case "cen":
                        $mots = $this->_db->select_complete_mots_with_no_siecle();
                        break;
                    case "per":
                        $mots = $this->_db->select_complete_mots_with_no_periode();
                        break;
                    case "ref":
                        $mots = $this->_db->select_complete_mots_with_no_reference();
                        break;
                    case "all":
                        $mots = $this->_db->select_complete_mots();
                        break;
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