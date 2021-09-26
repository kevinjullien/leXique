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
                $mots = $this->getMots();
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

    /**
     * Return a list of Mots, filtered if asked with $_GET['filter'].
     * If the filter is not set, it will return the whole list, as if it was set to 'all'.
     *
     * @return array of Mot
     * @throws CustomException if the filter is not as expected
     */
    private function getMots(): array
    {
        switch ($_GET['filter'] ?? "all") {
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
        if (empty($mots)) {
            throw new CustomException("Le filtre est invalide: " . $_GET["filter"]);
        }
        return $mots;
    }
}