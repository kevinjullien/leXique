<?php

class DisplayController

{
    private $_db;

    public function __construct(Db $db)
    {
        $this->_db = $db;
    }

    /**
     * @throws CustomException
     */
    public function run()
    {
        $emptyDataText = "<span style='color: lightgrey'>Aucune donnée</span>";
        if (isset($_GET['scope'])) {
            if ($_GET['scope'] === "words") {
                $mots = $this->_db->select_mots_with_libelle_and_definition_with_valid_definition();
                if (isset($_GET['word'])) {
                    $chosenMot = $this->_db->select_complete_mot_by_libelle($_GET['word']);
                    if (empty($chosenMot)) throw new CustomException("Display: mot '" . $_GET['word'] . "' invalide", 400);
                    $this->addLinksToKnownWordsInWordDescription($mots, $chosenMot);
                    $champsLexicaux = $this->_db->select_valid_champs_lexicaux_with_intitule_and_description();
                    $this->addLinksToValidChampsLexicaux($champsLexicaux, $chosenMot);
                    $periodes = $this->_db->select_valid_periodes_with_nom_debut_fin_and_description();
                    $this->addLinksToValidPeriodes($periodes, $chosenMot);
                    $this->addLinksToSynonymesAndAntonymes($mots, $chosenMot);
                    require_once(VIEW_PATH . 'displayChosenMot.php');
                } else {
                    require_once(VIEW_PATH . 'displayListMot.php');
                }
            } else if ($_GET['scope'] === "lex") {
                $champsLexicaux = $this->_db->select_valid_champs_lexicaux_with_id_intitule_and_description();
                if (isset($_GET['lex'])) {
                    $chosenLex = $this->_db->select_complete_champ_lexical_by_intitule($_GET['lex']);
                    if (empty($chosenLex)) throw new CustomException("Display: champ lexical '" . $_GET['lex'] . "' invalide", 400);
                    $mots = $this->_db->select_mots_with_libelle_and_definition_with_valid_definition();
                    require_once(VIEW_PATH . 'displayChosenLex.php');
                } else {
                    require_once(VIEW_PATH . 'displayListLex.php');
                }
            } else if ($_GET['scope'] === "per") {
                $periodes = $this->_db->select_periodes_with_id_nom_debut_fin_and_description();
                if (isset($_GET['per'])) {
                    $chosenPer = $this->_db->select_complete_periode_by_nom($_GET['per']);
                    if (empty($chosenPer)) throw new CustomException("Display: Période '" . $_GET['per'] . "' invalide", 400);
                    $mots = $this->_db->select_mots_with_libelle_and_definition_with_valid_definition();
                    require_once(VIEW_PATH . 'displayChosenPer.php');
                } else {
                    require_once(VIEW_PATH . 'displayListPer.php');
                }
            } else {
                throw new CustomException("Display: scope '" . $_GET['scope'] . "' invalide", 400);
            }
        } else {
            throw new CustomException("Display: scope absent", 400);
        }

    }

    /**
     * Check if the definition of the given Mot contains any Mot in the given array (as $array['libelle'])
     * If any is found, edit it to be a link to the Mot's page + putting a bit of the Mot's definition in a tooltip
     *
     * @param array $motsList
     * @param Mot $chosenMot
     */
    private function addLinksToKnownWordsInWordDescription(array $motsList, Mot $chosenMot): void
    {
        // previous valid (but probably incomplete) regex attempt: /([a-zàâçéèêëîïôûùüÿæœ0-9\-]+)|(\s+)|([²³&|@#€µ£%\"'+*=~.,:;?!<>\/\\\(){}\[\]\-_]+)/i
        $pattern = "/([a-zàâçéèêëîïôûùüÿæœ0-9\-]+)|(\s+)|(?![a-zàâçéèêëîïôûùüÿæœ0-9\-]+)/i";
        $splittedDefinition = preg_split($pattern, $chosenMot->getDefinition(), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $variants = $this->_db->select_every_variants_with_libelle_and_type();
        $newTab = array();
        $mots = $this->motsToLowerKey($motsList);
        foreach ($splittedDefinition as $i => $tok) {
            $token = strtolower($tok);
            if (array_key_exists($token, $mots)) {
                if ($token === strtolower($chosenMot->getLibelle())){
                    $newTab[] = $tok;
                    continue;
                }
                $limit = 350;
                $suffix = strlen($mots[$token]['definition']) > $limit ? "[........]" : "";
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr($mots[$token]['definition'], 0, $limit) . $suffix . '" href="index.php?action=display&scope=words&word=' . htmlspecialchars($mots[$token]['origin']) . '">' . htmlspecialchars($tok) . '</a>';
                $newTab[] = $replacement;
            } else if (array_key_exists($token, $variants)) {
                if ($token == strtolower($chosenMot->getLibelle())){
                    $newTab[] = $tok;
                    continue;
                }
                $limit = 350;
                $suffix = strlen($mots[$variants[$token]['mot']]['definition']) > $limit ? "[........]" : "";
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr('<u>Variant <i>' . $variants[$token]['type'] . '</i> de <b>' . $variants[$token]['mot'] . "</b>:</u><br>" .$mots[$variants[$token]['mot']]['definition'], 0, $limit) . $suffix . '" href="index.php?action=display&scope=words&word=' . htmlspecialchars($variants[$token]['mot']) . '">' . htmlspecialchars($tok) . '</a>';
                $newTab[] = $replacement;
            }
            else {
                $newTab[] = $tok;
            }
        }
        $chosenMot->setDefinition(implode("", $newTab));
    }

    private function addLinksToValidChampsLexicaux(array $validChampsLexicaux, ?Mot $chosenMot)
    {
        $newTab = array();
        foreach ($chosenMot->getChampsLexicaux() as $i => $lex) {
            if (array_key_exists($lex, $validChampsLexicaux)) {
                $limit = 350;
                $suffix = strlen($validChampsLexicaux[$lex]) > $limit ? "[........]" : "";
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr($validChampsLexicaux[$lex], 0, $limit) . $suffix . '" href="index.php?action=display&scope=lex&lex=' . htmlspecialchars($lex) . '">' . htmlspecialchars($lex) . '</a>';
                $newTab[] = $replacement;
            } else if (!empty($_SESSION['admin']) && $_SESSION['admin']) {
                $newTab[] = '<a class="adminLink" href="index.php?action=display&scope=lex&lex=' . htmlspecialchars($lex) . '">' . htmlspecialchars($lex) . "</a>";
            } else {
                $newTab[] = $lex;
            }
        }
        $chosenMot->setChampsLexicaux($newTab);
    }

    private function addLinksToValidPeriodes(array $validPeriodes, ?Mot $chosenMot)
    {
        $newTab = array();
        foreach ($chosenMot->getPeriodes() as $i => $per) {
            if (array_key_exists($per, $validPeriodes)) {
                $limit = 350;
                $suffix = strlen($validPeriodes[$per]['description']) > $limit ? "[........]" : "";
                $prefix = '';
                if (!empty($validPeriodes[$per]['debut']) || !empty($validPeriodes[$per]['fin'])) {
                    $prefix = '<p>Début: ' . $validPeriodes[$per]['debut'] . '<br>Fin: ' . $validPeriodes[$per]['fin'] . '</p>';
                }
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . $prefix . substr($validPeriodes[$per]['description'], 0, $limit) . $suffix . '" href="index.php?action=display&scope=per&per=' . htmlspecialchars($per) . '">' . htmlspecialchars($per) . '</a>';
                $newTab[] = $replacement;
            } else if (!empty($_SESSION['admin']) && $_SESSION['admin']) {
                $newTab[] = '<a class="adminLink" href="index.php?action=display&scope=per&per=' . htmlspecialchars($per) . '">' . htmlspecialchars($per) . "</a>";
            } else {
                $newTab[] = $per;
            }
        }
        $chosenMot->setPeriodes($newTab);
    }

    private function addLinksToSynonymesAndAntonymes(array $mots, ?Mot $chosenMot)
    {
        $newTab = array();
        foreach ($chosenMot->getSynonymes() as $i => $synonyme) {
            if (array_key_exists($synonyme, $mots)) {
                $limit = 350;
                $suffix = strlen($mots[$synonyme]) > $limit ? "[........]" : "";
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr($mots[$synonyme], 0, $limit) . $suffix . '" href="index.php?action=display&scope=words&word=' . htmlspecialchars($synonyme) . '">' . htmlspecialchars($synonyme) . '</a>';
                $newTab[] = $replacement;
            } else if (!empty($_SESSION['admin']) && $_SESSION['admin']) {
                $newTab[] = '<a class="adminLink" href="index.php?action=display&scope=words&word=' . htmlspecialchars($synonyme) . '">' . htmlspecialchars($synonyme) . "</a>";
            } else {
                $newTab[] = $synonyme;
            }
        }
        $chosenMot->setSynonymes($newTab);

        $newTab = array();
        foreach ($chosenMot->getAntonymes() as $i => $antonyme) {
            if (array_key_exists($antonyme, $mots)) {
                $limit = 350;
                $suffix = strlen($mots[$antonyme]) > $limit ? "[........]" : "";
                $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr($mots[$antonyme], 0, $limit) . $suffix . '" href="index.php?action=display&scope=words&word=' . htmlspecialchars($antonyme) . '">' . htmlspecialchars($antonyme) . '</a>';
                $newTab[] = $replacement;
            } else if (!empty($_SESSION['admin']) && $_SESSION['admin']) {
                $newTab[] = '<a class="adminLink" href="index.php?action=display&scope=words&word=' . htmlspecialchars($antonyme) . '">' . htmlspecialchars($antonyme) . "</a>";
            } else {
                $newTab[] = $antonyme;
            }
        }
        $chosenMot->setAntonymes($newTab);
    }

    /**
     * Replace the actual table $tab[key] containing the definition as $tab[keyToLowerCase][[origin][definition]]
     * where the key is the former key to lower case, [origin] is the original key and [definition] is the definition
     *
     * @param array $motsList
     * @return array
     */
    private function motsToLowerKey(array $motsList): array
    {
        $tab = array();
        foreach ($motsList as $mot => $definition){
            $key = strtolower($mot);
            $tab[$key] = array();
            $tab[$key]['origin'] = $mot;
            $tab[$key]['definition'] = $definition;
        }
        return $tab;
    }
}