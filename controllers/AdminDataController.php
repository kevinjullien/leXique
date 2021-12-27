<?php

class adminDataController
{
    private $_db;
    private $_phpFileUploadErrors = array(
        0 => 'Aucune erreur (ceci ne devrait donc jamais s\'afficher :-)).',
        1 => 'Le fichier téléversé à dépasse la limite upload_max_filesize dans php.ini (-> serveur).',
        2 => 'Le fichier téléversé à dépasse la limite MAX_FILE_SIZE spécifiée dans le formulaire HTML (-> dev).',
        3 => 'Le fichier téléversé n\'a été que partiellement téléversé.',
        4 => 'Aucun fichier fourni.',
        6 => 'Dossier temporaire manquant.',
        7 => 'Erreur lors de l\'écriture sur le disque.',
        8 => 'Une extension php a arrêté le processus.',
    );
    private $_error = NULL;

    public function __construct(Db $db)
    {
        $this->_db = $db;
    }

    /**
     * @throws CustomException
     */
    public function run()
    {
        $maxInputLength = "250";
        $maxTextareaLength = "65000";
        if (empty($_SESSION['auth']) || !$_SESSION['admin']) {
            header("Location: index.php");
            die();
        }

        if (isset($_POST['editRef'])) { // -> sending the reference edition form
            $this->onReferenceEdition();
        } else if (isset($_POST['intitule'])) {// -> sending the edition lex form
            $this->onChampLexicalEdition();
        } else if (isset($_POST['nom'])) {// -> sending the edition per form
            $this->onPeriodeEdition();
        } else if (isset($_GET['scope']) && $_GET['scope'] === "lex"
            && isset($_GET['subAction']) && $_GET['subAction'] === "edit") {// -> displaying the Lex edition form
            $chosenLex = $this->_db->select_complete_champ_lexical_by_intitule($_GET['lex']);
            if (empty($chosenLex)) throw new CustomException("AdminDataController: champ lexical '". $_GET['lex'] . "' invalide", 400);
        } else if (isset($_GET['scope']) && $_GET['scope'] === "per"
            && isset($_GET['subAction']) && $_GET['subAction'] === "edit") {// -> displaying the Pe edition form
            $chosenPer = $this->_db->select_complete_periode_by_nom($_GET['per']);
            if (empty($chosenPer)) throw new CustomException("AdminDataController: période '" . $_GET['per'] . "' invalide", 400);
        } else if (isset($_POST['libelle'])) {// -> sending the completed word form (add or edit)
            $this->onMotAdditionOrEdition();
        } else { // displaying the Mot form
            $mots = $this->_db->select_mots_and_def();
            if (isset($_GET['subAction'])) {
                if ($_GET['subAction'] == "edit") {
                    if (isset($_GET['word'])) {
                        $champsLexicaux = $this->_db->select_champs_lexicaux_intitules();
                        $periodes = $this->_db->select_periodes_noms();
                        $siecles = $this->_db->select_siecles();
                        $typesVariants = $this->_db->select_types_variants_orthographiques_libelles();
                        $chosenMot = $this->_db->select_complete_mot_by_libelle($_GET['word']);
                        if (empty($chosenMot)) throw new CustomException("AdminDataController: mot '" . $_GET['word'] . "' invalide", 400);
                    } else {
                        throw new CustomException("AdminDataController: aucun mot fourni pour l'édition", 400);
                    }
                } else if ($_GET['subAction'] == "add") {
                    $champsLexicaux = $this->_db->select_champs_lexicaux_intitules();
                    $periodes = $this->_db->select_periodes_noms();
                    $siecles = $this->_db->select_siecles();
                    $typesVariants = $this->_db->select_types_variants_orthographiques_libelles();
                } else {
                    throw new CustomException("AdminDataController: subAction '" . $_GET['subAction'] . "' invalide", 400);
                }
            } else {
                throw new CustomException("AdminDataController: subAction absent", 400);
            }
        }
        require_once(VIEW_PATH . 'addEditDataForm.php');
    }

    /**
     * @throws CustomException
     * @throws Exception
     */
    private function onReferenceEdition(): void
    {
        if (isset($_POST['deleteReference'])) {
            $this->_db->deleteReference($_POST['id']);
        } else {
            $referenceFromInput = $this->fetchReference();
            $referenceFromDB = $this->_db->select_reference_by_id($referenceFromInput->getId());
            $this->_db->update_reference($referenceFromInput);

            if (!empty($referenceFromDB->getDocument())) {
                // Here, if a new document was in the form, the DB is updated.
                // If not, the document has not been changed.
                if (!empty($referenceFromInput->getDocument())) {
                    // If a new document has been sent, the former one is deleted from the server
                    $this->deleteFileFromServer($referenceFromDB->getDocument());
                } else if (isset($_POST['documentToDelete'])) {
                    // Else if the form asked to delete the document,
                    // the DB is updated and the document is deleted from the server
                    $this->_db->remove_document_of_reference($referenceFromInput->getId());
                    $this->deleteFileFromServer($referenceFromDB->getDocument());
                }
                // If no document were provided and the deletion of the actual document is not asked, nothing is done
            }
        }
        if (!empty($this->_error)) {
            throw new CustomException("AdminDataController: " . $this->_error);
        } else {
            header("Location: index.php?action=display&scope=words&word=" . $_POST['origin']);
            die();
        }
    }

    /**
     * After a POST, fetch every data about one reference
     * Create a Reference, set every available (!empty) data
     * Empty filenames are considered as a NULL document
     *
     * @return Reference
     */
    private function fetchReference(): Reference
    {
        $ref = new Reference();
        $ref->setId($_POST['id']);
        $ref->setTitre(empty($_POST['titre']) ? NULL : $_POST['titre']);
        $ref->setAuteur(empty($_POST['auteur']) ? NULL : $_POST['auteur']);
        $ref->setEditeur(empty($_POST['editeur']) ? NULL : $_POST['editeur']);
        $ref->setPages(empty($_POST['page']) ? NULL : $_POST['page']);
        $ref->setLieuEdition(empty($_POST['lieuEdition']) ? NULL : $_POST['lieuEdition']);
        $ref->setDateEdition(empty($_POST['dateEdition']) ? NULL : $_POST['dateEdition']);
        if (!empty($_POST['lien'])) {
            if ($this->startsWith($_POST['lien'], "http"))
                $ref->setLien($_POST['lien']);
            else
                $ref->setLien("http://" . $_POST['lien']);
        }
        $ref->setDocument($this->uploaded_image_treatment('reference'));

        return $ref;
    }

    private function startsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * Treat an uploaded image after a POST
     * Adds a timestamp before the file's name
     * Places the renamed file in IMAGE_PATH
     *
     * @param $path string the path as $_FILES['$path']
     * @param $index int the index in $_FILES['$path']['tmp_name' | 'name']['$index']. < 0 if no index
     * @return string
     */
    private function uploaded_image_treatment(string $path, int $index = -1): ?string
    {
        $err = $index === -1 ? $_FILES["$path"]['error'] : $_FILES["$path"]['error']["$index"];
        if ($err !== 0) {
            if ($err !== 4)
                $this->_error = $this->_phpFileUploadErrors[$index === -1 ? $_FILES["$path"]['error'] : $_FILES["$path"]['error']["$index"]];
            return NULL;
        }
        $uploadTime = str_replace('.', '_', microtime(true));
        $origin = $index >= 0 ? $_FILES["$path"]['tmp_name']["$index"] : $_FILES["$path"]['tmp_name'];
        $newName = $index >= 0 ? $uploadTime . "_" . basename($_FILES["$path"]['name']["$index"]) : $uploadTime . "_" . basename($_FILES["$path"]['name']);
        $destination = FILES_PATH . $newName;
        move_uploaded_file($origin, $destination);

        if (strcmp($path, "illustration") == 0) {
            $tmpName = $this->resizeImage($destination);
            $this->deleteFileFromServer($newName);
            $newName = $tmpName;
        }

        return $newName;
    }

    /**
     * Image resizing for the illustrations.
     * The given file shall be deleted and a new one named illustration_{$libelle} is generated.
     * If this filename already exist, it is simply replaced.
     *
     * @param $file string file to rename and resize
     * @return string|null the new filename
     */
    function resizeImage(string $file): ?string
    {
        $image_prop = getimagesize($file);
        $image_type = $image_prop[2];
        $fileName = "illustration_" . $_POST['libelle'];
        if ($image_type == IMAGETYPE_JPEG) {
            $image_id = imagecreatefromjpeg($file);
            $layer = $this->resize($image_id, $image_prop[0], $image_prop[1]);
            $fileName = $fileName . ".jpg";
            imagejpeg($layer, $fileName);
        } elseif ($image_type == IMAGETYPE_GIF) {
            $image_id = imagecreatefromgif($file);
            $layer = $this->resize($image_id, $image_prop[0], $image_prop[1]);
            $fileName = $fileName . ".gif";
            imagegif($layer, $fileName);
        } elseif ($image_type == IMAGETYPE_PNG) {
            $image_id = imagecreatefrompng($file);
            $layer = $this->resize($image_id, $image_prop[0], $image_prop[1]);
            $fileName = $fileName . ".png";
            imagepng($layer, $fileName);
        }
        $destination = FILES_PATH . $fileName;

        if (is_file($destination)) {
            $this->deleteFileFromServer($destination);
        }
        rename($fileName, $destination);

        return $fileName;
    }

    private function resize($image_id, $width, $height)
    {
        $baseSize = 400;
        if ($width >= $height) {
            $new_width = $width * ($baseSize / $width);
            $new_height = $height * ($baseSize / $width);
        } else {
            $new_width = $width * ($baseSize / $height);
            $new_height = $height * ($baseSize / $height);
        }
        $layer = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($layer, $image_id, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        return $layer;
    }

    /**
     * @param string $fileName
     */
    private function deleteFileFromServer(string $fileName): void
    {
        $path = str_replace("/", "\\", getcwd() . "\\" . FILES_PATH . $fileName);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function onChampLexicalEdition(): void
    {
        $updatedLex = new ChampLexical($_POST['intitule'], $_POST['description']);
        $updatedLex->setId($_POST['id']);
        $this->_db->update_champ_lexical($updatedLex);
        header("Location: index.php?action=display&scope=lex&lex=" . $_POST['intitule']);
        die();
    }

    private function onPeriodeEdition(): void
    {
        $updatedPer = new Periode($_POST['nom'], $_POST['description']);
        $updatedPer->setId($_POST['id']);
        $updatedPer->setDebut($_POST['debut']);
        $updatedPer->setFin($_POST['fin']);
        $this->_db->update_periode($updatedPer);
        header("Location: index.php?action=display&scope=per&per=" . $_POST['nom']);
        die();
    }

    /**
     * @throws CustomException
     * @throws Exception
     */
    private function onMotAdditionOrEdition(): void
    {
        $motFrominput = new Mot($_POST['libelle'], $_POST['definition']);
        $motFrominput->setSynonymes($this->cleanArrayFromEmptyElements($_POST['synonymes']));
        $motFrominput->setAntonymes($this->cleanArrayFromEmptyElements($_POST['antonymes']));
        $motFrominput->setChampsLexicaux($this->cleanArrayFromEmptyElements($_POST['champsLexicaux']));
        $motFrominput->setSiecles($this->cleanArrayFromEmptyElements($_POST['siecles']));
        $motFrominput->setPeriodes($this->cleanArrayFromEmptyElements($_POST['periodes']));
        $motFrominput->setReferences($this->fetchReferences());
        if ($this->is_uploaded_file_an_image('illustration'))
            $motFrominput->setIllustration($this->uploaded_image_treatment('illustration'));
        $motFrominput->setVariantsOrthographiques($this->fetchVariantsOrthographiques());
        if ($_GET['subAction'] == "add") {
            if ($this->_db->is_libelle_already_taken($_POST['libelle'])) throw new CustomException("Le libellé choisi est déjà pris: " . $motFrominput->getLibelle());
            $this->_db->insert_complete_mot($motFrominput);
        } else if ($_GET['subAction'] == "edit") {
            $motFrominput->setId($_POST['id']);
            $motFromDb = $this->_db->select_mot_by_id($motFrominput->getId());
            if (empty($motFromDb)) throw new CustomException("Le mot n'existe pas", 400);

            if (!empty($motFrominput->getIllustration()) && !empty($motFromDb->getIllustration())) {
                $this->deleteFileFromServer($motFromDb->getIllustration());
            }
            $this->_db->update_complete_mot($motFrominput);
            if (empty($motFrominput->getIllustration())) {
                if (isset($_POST['illustrationToDelete'])) {
                    $this->_db->remove_illustration_of_mot($motFrominput->getId());
                    $this->deleteFileFromServer($motFromDb->getIllustration());
                } else if (!empty($motFromDb->getIllustration()) && $motFromDb->getLibelle() !== $motFrominput->getLibelle()) {
                    $destination = "illustration_" . $motFrominput->getLibelle() . ".png";
                    if (rename(FILES_PATH . $motFromDb->getIllustration(), FILES_PATH . $destination)) {
                        $motFrominput->setIllustration($destination);
                        $this->_db->update_mot_illustration($motFrominput);
                    }
                }
            }
        }
        if (!empty($this->_error)) {
            throw new CustomException("AdminDataController: " . $this->_error);
        } else {
            header("Location: index.php?action=display&scope=words&word=" . $motFrominput->getLibelle());
            die();
        }
    }

    /**
     * Return a new array containing only !empty elements from the given array.
     *
     * @param $array array the given array filled with strings or NULL elements
     * @return array
     */
    private function cleanArrayFromEmptyElements(array $array): array
    {
        $tab = [];
        foreach ($array as $i => $w) {
            if (!empty($w)) $tab[] = $w;
        }
        return $tab;
    }

    /**
     * After a POST, fetch every data about each reference submitted
     * Create a Reference, set every available (!empty) data and put it into an array
     * Empty filenames or errors are simply not treated
     *
     * @return array of Reference
     */
    private function fetchReferences(): array
    {
        $arr = array();

        foreach ($_FILES['references']['name'] as $i => $fileName) {
            if (!empty($_POST['titres'][$i])) {
                $ref = new Reference();
                $ref->setTitre(empty($_POST['titres'][$i]) ? NULL : $_POST['titres'][$i]);
                $ref->setAuteur(empty($_POST['auteurs'][$i]) ? NULL : $_POST['auteurs'][$i]);
                $ref->setEditeur(empty($_POST['editeurs'][$i]) ? NULL : $_POST['editeurs'][$i]);
                $ref->setPages(empty($_POST['pages'][$i]) ? NULL : $_POST['pages'][$i]);
                $ref->setLieuEdition(empty($_POST['lieuxEdition'][$i]) ? NULL : $_POST['lieuxEdition'][$i]);
                $ref->setDateEdition(empty($_POST['datesEdition'][$i]) ? NULL : $_POST['datesEdition'][$i]);
                if (!empty($_POST['liens'][$i])) {
                    if ($this->startsWith($_POST['liens'][$i], "http"))
                        $ref->setLien($_POST['liens'][$i]);
                    else
                        $ref->setLien("http://" . $_POST['liens'][$i]);
                }
                $ref->setDocument($this->uploaded_image_treatment('references', $i));
                $arr[] = $ref;
            }
        }
        return $arr;
    }

    /**
     * Checks if the uploaded file is a valid image.
     * Valid is considered .jpg or .png
     *
     * @param $path string the path
     * @param int $index
     * @return bool
     */
    private function is_uploaded_file_an_image(string $path, int $index = -1): bool
    {
        if (empty($_FILES["$path"]['tmp_name'])) return false;
        if ($index === -1) {
            $imageInfo = getimagesize($_FILES["$path"]['tmp_name']);
            return ($_FILES["$path"]['type'] == 'image/jpeg' && $imageInfo['mime'] == 'image/jpeg')
                || ($_FILES["$path"]['type'] == 'image/png' && $imageInfo['mime'] == 'image/png')
                || ($_FILES["$path"]['type'] == 'image/gif' && $imageInfo['mime'] == 'image/gif');
        }
        $imageInfo = getimagesize($_FILES["$path"]['tmp_name']["$index"]);
        return ($_FILES["$path"]['type']["$index"] == 'image/jpeg' && $imageInfo['mime'] == 'image/jpeg')
            || ($_FILES["$path"]['type']["$index"] == 'image/png' && $imageInfo['mime'] == 'image/png')
            || ($_FILES["$path"]['type']["$index"] == 'image/gif' && $imageInfo['mime'] == 'image/gif');
    }

    /**
     * After a POST, fetch every data about each variants submitted
     * Empty types or corresponding variants are simply not treated
     *
     * @return array
     */
    private function fetchVariantsOrthographiques(): array
    {
        $arr = array();
        foreach ($_POST["variantTypes"] as $i => $type) {
            if (empty($type) || empty($_POST['variants'][$i]))
                continue;

            if (empty($arr[$type])) $arr[$type] = array();
            $arr[$type][] = $_POST['variants'][$i];
        }

        return $arr;
    }

    private function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        return substr($haystack, strlen($haystack) - $length) === $needle;
    }
}