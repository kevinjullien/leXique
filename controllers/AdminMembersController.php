<?php

class adminMembersController
{
    private $_db;

    public function __construct(Db $db)
    {
        $this->_db = $db;
    }


    public function run()
    {
        if (empty($_SESSION) || !$_SESSION['admin']) {
            header("Location: index.php");
            die();
        }

        $userinput = '';
        $message = '';
        $logininfo = array();

        if (isset($_POST['ajoutMembre'])) {

            if (empty($_POST['utilisateur'])) {
                $logininfo['utilisateurmessage'] = 'Veuillez compléter le champ ci-dessus';
            } else {
                $userinput = $_POST['utilisateur'];
            }
            if (empty($_POST['password'])) {
                $logininfo['passwordmessage'] = 'Veuillez compléter le champ ci-dessus';
            }

            if ($this->_db->select_utilisateur($_POST['utilisateur']) != null)
                $message = "Utilisateur déjà présent dans la base de données";

            else if (!empty($_POST['utilisateur']) && !empty($_POST['password'])) {
                if ($this->insert_utilisateur()) {
                    $message = "Ajout bien effectué";
                    $userinput = '';
                } else {
                    $message = "Un problème est survenu (pseudo déjà présent dans la DB?)";
                }
            }
        } else if (isset($_POST['editer'])) {
            $selectionmembre = $this->_db->select_utilisateur($_POST['utilisateur']);
        }
        $membres = $this->_db->select_utilisateurs();


        require_once(VIEW_PATH . 'addEditMembers.php');
    }

    public function insert_utilisateur(): bool
    {
        $isadmin = ((isset($_POST['isadmin'])) ? 1 : 0);
        $password = password_hash($_POST['password'], CRYPT_ALGO); //password encryption
        return $this->_db->insert_utilisateur($_POST['utilisateur'], $password, $isadmin);
    }
}