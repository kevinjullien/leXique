<?php

class LoginController
{
    private $_db;


    public function __construct($_db)
    {
        $this->_db = $_db;
    }

    public function run()
    {
        $userinput = '';
        $message = '';
        $logininfo = array();

        if (!empty($_SESSION['auth'])) {
            header("Location: index.php");
            die();
        }

        if (isset($_POST['userLogin'])) {

            if (empty($_POST['user'])) {
                $logininfo['userMsg'] = 'Veuillez compléter le champ ci-dessus';
            }
            else {
                $userinput = $_POST['user'];
            }

            if (empty($_POST['password'])) {
                $logininfo['passwordMsg'] = 'Veuillez compléter le champ ci-dessus';

            } else {
                $user = $this->_db->select_utilisateur($_POST['user']);

                if ($user != NULL) {
                    if (password_verify($_POST['password'], $user->getMotDePasse())) {

                            $_SESSION['auth'] = true;
                            $_SESSION['username'] = $user->getPseudo();

                        $msg = date('d/m/Y H:i:s') . "\nConnection: " . $user->getPseudo() . "\n\n";
                        error_log("$msg", 3, LOG_PATH . "connections.log");
                            if($user->estAdmin() == 1)
                                $_SESSION['admin'] = true;
                            else
                                $_SESSION['admin'] = false;
                            header("Location: index.php?action=home");
                            die();
                    }
                    else {
                        $msg = date('d/m/Y H:i:s') . "\nConnection type a: " . $_POST['user'] . " // " . $_POST['password'] . "\n" . "remote addr: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
                        error_log("$msg", 3, LOG_PATH . "connectionsErrors.log");
                        $message = 'Utilisateur ou mot de passe invalide';
                    }
                }
                else{
                    $msg = date('d/m/Y H:i:s') . "\nConnection type b: " . $_POST['user'] . " // " . $_POST['password'] . "\n" . "remote addr: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
                    error_log("$msg", 3, LOG_PATH . "connectionsErrors.log");
                    $message = 'Utilisateur ou mot de passe invalide';
                }
            }
        }

        require_once(VIEW_PATH . 'login.php');
    }

}