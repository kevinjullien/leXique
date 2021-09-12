    <?php if (isset($_GET['scope']) && $_GET['scope'] === "words") {
        require_once(VIEW_PATH . 'addEditDataFormMot.php');
    } else if (isset($_GET['scope']) && $_GET['scope'] === "lex") {
        require_once(VIEW_PATH . 'addEditDataFormLex.php');
    }else if (isset($_GET['scope']) && $_GET['scope'] === "per") {
        require_once(VIEW_PATH . 'addEditDataFormPer.php');
    }
