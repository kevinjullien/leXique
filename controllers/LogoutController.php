<?php


class LogoutController
{
    public function run()
    {
        $_SESSION = array();

        session_destroy();

        header("Location: index.php");
        die();
    }

}