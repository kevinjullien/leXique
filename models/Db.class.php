<?php

class Db

{

    private static $instance = null;
    private $_db;

    private static $invalidString = '<p>&nbsp;</p>';


    private function __construct()
    {
        set_error_handler(/**
         * @throws ErrorException
         */ function ($errno, $errstr, $errfile, $errline) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        try {
            if ($_SERVER['SERVER_NAME'] === "localhost")
                $ini = parse_ini_file(CONFIG_PATH . 'devConfig.ini');
            else
                $ini = parse_ini_file(CONFIG_PATH . 'config.ini');

            $this->_db = new PDO('mysql:host=' . $ini['db_host'] . ';dbname=' . $ini['db_name'] . ';charset=utf8mb4',
                                    $ini['db_login'], $ini['db_password']);
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (Exception | Error $e) {
            $err = date('d/m/Y H:i:s') . "\n" . substr($e, 0, 7500) . "\n\n";
            error_log("$err", 3, LOG_PATH . "errors.log");
            if ($_SERVER['SERVER_NAME'] !== "localhost")
                error_log("$err", 1, DEVMAIL);
            die('Erreur de connexion à la base de données.');
        }
        restore_error_handler();
    }

    # Pattern Singleton: Si une instance existe déjà, c'est elle qui est renvoyée
    public static function getInstance(): ?Db
    {
        if (is_null(self::$instance)) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    /**
     * Fetch an Utilisateur by its given username.
     *
     * @param $username
     * @return Utilisateur|null
     */
    public function select_utilisateur($username): ?Utilisateur
    {
        $query = 'SELECT pseudo, mot_de_passe, est_admin FROM lexique_utilisateurs WHERE pseudo = :username';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':username', $username);
        $ps->execute();

        $utilisateur = null;
        if ($ps->rowcount() != 0) {
            $row = $ps->fetch();
            $utilisateur = new Utilisateur($row->pseudo, $row->mot_de_passe, $row->est_admin);
        }

        return $utilisateur;
    }

    /**
     * Fetch every existing Utilisateur.
     *
     * @return array of Utilisateur
     */
    public function select_utilisateurs(): array
    {
        $query = 'SELECT pseudo, est_admin FROM lexique_utilisateurs ORDER BY 2, 1';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $utilisateurs = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $utilisateurs[] = new Utilisateur($row->pseudo, NULL, $row->est_admin);
            }
        }

        return $utilisateurs;
    }

    /**
     * Insert an Utilisateur.
     *
     * @param $username
     * @param $password
     * @param $isAdmin
     * @return bool
     */
    public function insert_utilisateur($username, $password, $isAdmin): bool
    {
        $query = 'INSERT INTO lexique_utilisateurs (pseudo, mot_de_passe, est_admin) 
                    VALUES (:username, :password, :isAdmin)';
        $ps = $this->_db->prepare($query);
        $ps->bindValue(':username', $username);
        $ps->bindValue(':password', $password);
        $ps->bindValue(':isAdmin', $isAdmin, PDO::PARAM_INT);

        return $ps->execute();
    }

    /**
     *  Fetch every Mot's libelle and definition
     *
     * @return array as array['libelle'] where the libelle is the index and the definition is the content
     */
    public function select_mots_and_def(): array
    {
        $query = 'SELECT libelle, definition FROM lexique_mots ORDER BY libelle';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $mots = array();
        $i = 0;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mots[$i] = array();
                $mots[$i]['libelle'] = $row->libelle;
                $mots[$i++]['definition'] = $row->definition;
            }
        }

        return $mots;
    }

    /**
     * Fetch the libelle and definition of every Mot with a valid definition.
     * NULL and '<p>&nbsp;</p>' are considered invalid.
     *
     * @return array of array containing 2 indexes: 'libelle' and 'definition'
     */
    public function select_mots_with_libelle_and_definition_with_valid_definition(): array
    {
        $query = 'SELECT libelle, definition FROM lexique_mots 
                    WHERE definition IS NOT NULL AND STRCMP(definition, :invalid) ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        $mots = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mots[$row->libelle] = $row->definition;
            }
        }

        return $mots;
    }

    /**
     * Insert every data contained in the given Mot.
     *
     * @param Mot $mot
     * @return bool
     * @throws Exception
     */
    public function insert_complete_mot(Mot $mot): bool
    {
        $mots = $this->select_mots_libelles();
        $champsLexicaux = $this->select_champs_lexicaux_intitules();
        $periodes = $this->select_periodes_noms();
        $siecles = $this->select_siecles();
        $typesDeVariants = $this->select_types_variants_orthographiques_libelles();

        if (in_array($mot->getLibelle(), $mots)) return false;

        $this->_db->beginTransaction();
        try {
            $this->insert_mot($mot->getLibelle(), $mot->getDefinition(), $mot->getIllustration());

            $mot->setId($this->select_mot_id_from_libelle($mot->getLibelle()));

            $this->insert_variants_orthographiques($mot, $typesDeVariants);

            $this->insert_antonymes($mot, $mots);

            $this->insert_synonymes($mot, $mots);

            $this->insert_mot_champsLexicaux($mot, $champsLexicaux);

            $this->insert_mot_periodes($mot, $periodes);

            $this->insert_mot_siecles($mot, $siecles);

            $this->insert_mot_references($mot);

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw new Exception($e);
        }
        return $this->_db->commit();
    }

    /**
     * Fetch every existing Mot's libelle.
     */
    public function select_mots_libelles(): array
    {
        $query = 'SELECT libelle FROM lexique_mots ORDER BY libelle';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $mots = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mots[] = $row->libelle;
            }
        }

        return $mots;
    }

    /**
     * Fetch every existing ChampLexical's intitule.
     *
     * @return array
     */
    public function select_champs_lexicaux_intitules(): array
    {
        $query = 'SELECT intitule FROM lexique_champs_lexicaux ORDER BY intitule';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $champsLexicaux = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $champsLexicaux[] = $row->intitule;
            }
        }

        return $champsLexicaux;
    }

    /**
     * Fetch every existing Periode's nom.
     *
     * @return array
     */
    public function select_periodes_noms(): array
    {
        $query = 'SELECT nom FROM lexique_periodes ORDER BY nom';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $periodes = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $periodes[] = $row->nom;
            }
        }

        return $periodes;
    }

    /**
     * Fetch every existing Siecle's numero.
     *
     * @return array
     */
    public function select_siecles(): array
    {
        $query = 'SELECT numero FROM lexique_siecles ORDER BY numero';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $siecles = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $siecles[] = $row->numero;
            }
        }

        return $siecles;
    }

    /**
     * Fetch every existing variant orthographique's libelle.
     *
     * @return array
     */
    public function select_types_variants_orthographiques_libelles(): array
    {
        $query = 'SELECT libelle AS type FROM lexique_variants_ortho_types ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $arr[] = $row->type;
            }
        }

        return $arr;
    }

    /**
     * Insert a Mot by its given Libelle and definition.
     * The definition may be null.
     *
     * @param $libelle
     * @param null $definition
     * @param null $illustration
     * @return void
     */
    private function insert_mot($libelle, $definition = NULL, $illustration = NULL): void
    {
        $query = 'INSERT INTO lexique_mots (libelle, definition, illustration) 
                    VALUES (:libelle, :definition, :illustration)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':libelle', $libelle);
        $ps->bindValue(':definition', $definition);
        $ps->bindValue(':illustration', $illustration);
        $ps->execute();

        $this->select_mot_id_from_libelle($libelle);
    }

    /**
     * Fetch a Mot's id by its given libelle.
     *
     * @param $libelle
     * @return int the Mot's id, -1 if it does not exist
     */
    private function select_mot_id_from_libelle($libelle): int
    {
        $query = 'SELECT id FROM lexique_mots WHERE libelle = :libelle;';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':libelle', $libelle);
        $ps->execute();

        $id = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $id = $row->id;
            }
        }

        return $id;
    }

    /**
     * Insert each variant type if necessary, then each variant according to its type and the given Mot.
     *
     * @param Mot $mot
     * @param array $existingTypes an array containing every existing types
     * @return void
     */
    private function insert_variants_orthographiques(Mot $mot, array $existingTypes): void
    {

        foreach ($mot->getVariantsOrthographiques() as $type => $values) {
            if (!in_array($type, $existingTypes)) {
                $query = 'INSERT INTO lexique_variants_ortho_types (libelle) VALUES (:libelle)';

                $ps = $this->_db->prepare($query);
                $ps->bindValue(':libelle', $type);
                $ps->execute();
            }
            $typeId = $this->select_variant_type_id_from_libelle($type);

            foreach ($values as $i => $variant) {
                $query = 'INSERT INTO lexique_variants_ortho (libelle, mot, type) VALUES (:libelle, :motId, :typeId)';

                $ps = $this->_db->prepare($query);
                $ps->bindValue(':libelle', $variant);
                $ps->bindValue(':motId', $mot->getId());
                $ps->bindValue(':typeId', $typeId);
                $ps->execute();
            }
        }
    }

    /**
     * Fetch a variant type's id by its given libelle.
     *
     * @param string $type
     * @return int
     */
    private function select_variant_type_id_from_libelle(string $type): int
    {
        $query = 'SELECT id FROM lexique_variants_ortho_types WHERE libelle = :libelle';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':libelle', $type);
        $ps->execute();

        $id = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $id = $row->id;
            }
        }

        return $id;
    }

    /**
     * Link every Mot as Antonyme given in a Mot.
     * If the Antonyme does not exist, it is created (inserted as a new Mot).
     *
     * @param Mot $mot
     * @param array $mots
     * @return void
     */
    private function insert_antonymes(Mot $mot, array $mots): void
    {
        foreach ($mot->getAntonymes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $mots)) {
                $this->insert_mot($w);
            }
            $this->insert_antonyme($mot->getId(), $this->select_mot_id_from_libelle($w));
        }
    }

    /**
     * Link a Mot and another Mot as Antonyme.
     *
     * @param int $motId
     * @param int $antonymeId
     * @return void
     */
    private function insert_antonyme(int $motId, int $antonymeId): void
    {
        $query = 'INSERT INTO lexique_antonymes (mot_a, mot_b) VALUES (:lib, :ant), (:ant, :lib)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lib', $motId);
        $ps->bindValue(':ant', $antonymeId);

        $ps->execute();
    }

    /**
     * Link every Mot as Synonyme given in a Mot.
     * If the Synonyme does not exist, it is created  (inserted as a new Mot).
     *
     * @param Mot $mot
     * @param array $mots
     * @return void
     */
    private function insert_synonymes(Mot $mot, array $mots): void
    {
        foreach ($mot->getSynonymes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $mots)) {
                $this->insert_mot($w);
            }
            $this->insert_synonyme($mot->getId(), $this->select_mot_id_from_libelle($w));
        }
    }

    /**
     * Link a Mot and another Mot as Synonyme.
     *
     * @param int $motId
     * @param int $synonymeId
     * @return void
     */
    private function insert_synonyme(int $motId, int $synonymeId): void
    {
        $query = 'INSERT INTO lexique_synonymes (mot_a, mot_b) VALUES (:lib, :ant), (:ant, :lib)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lib', $motId);
        $ps->bindValue(':ant', $synonymeId);

        $ps->execute();
    }

    /**
     * Link every ChampLexical given in a Mot.
     * If the ChampLexical does not exist, it is created (inserted).
     *
     * @param Mot $mot
     * @param array $champsLexicaux every existing ChampLexical
     * @return void
     */
    private function insert_mot_champsLexicaux(Mot $mot, array $champsLexicaux): void
    {
        foreach ($mot->getChampsLexicaux() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $champsLexicaux)) {
                $this->insert_champ_lexical($w);
            }
            $this->insert_mot_champ_lexical($mot->getId(), $this->select_champ_lexical_id_from_intitule($w));
        }
    }

    /**
     * Insert a ChampLexical.
     *
     * @param $intitule
     * @param null $description
     * @return void
     */
    private function insert_champ_lexical($intitule, $description = null): void
    {
        $query = 'INSERT INTO lexique_champs_lexicaux (intitule, description) VALUES (:intitule, :description)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':intitule', $intitule);
        $ps->bindValue(':description', $description);
        $ps->execute();
    }

    /**
     * Link a Mot and a ChampLexical.
     *
     * @param int $motId
     * @param int $champLexicalId
     * @return void
     */
    private function insert_mot_champ_lexical(int $motId, int $champLexicalId): void
    {
        $query = 'INSERT INTO lexique_vue_mot_champ_lexical (mot, champ_lexical) VALUES (:mot, :champLexical)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':champLexical', $champLexicalId);
        $ps->execute();
    }

    /**
     * Fetch a ChampLexical's id by its given intitule.
     *
     * @param $intitule
     * @return int the Periode's id, -1 if it does not exist
     */
    private function select_champ_lexical_id_from_intitule($intitule): int
    {
        $query = 'SELECT id FROM lexique_champs_lexicaux WHERE intitule = :intitule;';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':intitule', $intitule);
        $ps->execute();

        $id = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $id = $row->id;
            }
        }

        return $id;
    }

    /**
     * Link every Periode given in a Mot.
     * If the Periode does not exist, it is created (inserted).
     *
     * @param Mot $mot
     * @param array $periodes every existing Periode
     * @return void
     */
    private function insert_mot_periodes(Mot $mot, array $periodes): void
    {
        foreach ($mot->getPeriodes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $periodes)) {
                $this->insert_periode($w);
            }
            $this->insert_mot_periode($mot->getId(), $this->select_periode_id_from_nom($w));
        }
    }

    /**
     * Insert a Periode.
     *
     * @param $periode
     * @return void
     */
    private function insert_periode($periode): void
    {
        $query = 'INSERT INTO lexique_periodes (nom) VALUES (:nom)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':nom', $periode);
        $ps->execute();
    }

    /**
     * Link a Mot and a Periode.
     *
     * @param int $motId
     * @param int $periodeId
     * @return void
     */
    private function insert_mot_periode(int $motId, int $periodeId): void
    {
        $query = 'INSERT INTO lexique_vue_mot_periode (mot, periode) VALUES (:mot, :periode)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':periode', $periodeId);
        $ps->execute();
    }

    /**
     * Fetch a Periode's id by its given nom.
     *
     * @param $nom
     * @return int the Periode's id, -1 if it does not exist
     */
    private function select_periode_id_from_nom($nom): int
    {
        $query = 'SELECT id FROM lexique_periodes WHERE nom = :nom;';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':nom', $nom);
        $ps->execute();

        $id = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $id = $row->id;
            }
        }

        return $id;
    }

    /**
     * Link every Siecle given in a Mot.
     * If the Siecle does not exist, it is created.
     *
     * @param Mot $mot
     * @param array $siecles every existing siecle
     * @return void
     */
    private function insert_mot_siecles(Mot $mot, array $siecles): void
    {
        foreach ($mot->getSiecles() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $siecles)) {
                $this->insert_siecle($w);
            }
            $this->insert_mot_siecle($mot->getId(), $w);
        }
    }

    /**
     * Insert a Siecle.
     *
     * @param int $numero
     * @return void
     */
    private function insert_siecle(int $numero): void
    {
        $query = 'INSERT INTO lexique_siecles (numero) VALUES (:numero)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':numero', $numero);

        $ps->execute();
    }

    /**
     * Link a Mot and a Siecle.
     *
     * @param int $motId
     * @param int $siecle
     * @return void
     */
    private function insert_mot_siecle(int $motId, int $siecle): void
    {
        $query = 'INSERT INTO lexique_vue_mot_siecle (mot, siecle) VALUES (:mot, :siecle)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':siecle', $siecle);
        $ps->execute();
    }

    /**
     * Insert every Reference from a Mot.
     *
     * @param Mot $mot
     * @return void
     */
    private function insert_mot_references(Mot $mot): void
    {
        foreach ($mot->getReferences() as $i => $w) {
            if (empty($w)) continue;

            $query = 'INSERT INTO lexique_references_biblio (auteur, titre, editeur, lieu_edition, date_edition, pages, lien, document) 
                        VALUES (:auteur, :titre, :editeur, :lieu_edition, :date_edition, :pages, :lien, :document)';

            $ps = $this->_db->prepare($query);
            $ps->bindValue(':auteur', $w->getAuteur());
            $ps->bindValue(':titre', $w->getTitre());
            $ps->bindValue(':editeur', $w->getEditeur());
            $ps->bindValue(':lieu_edition', $w->getLieuEdition());
            $ps->bindValue(':date_edition', $w->getDateEdition());
            $ps->bindValue(':pages', $w->getPages());
            $ps->bindValue(':lien', $w->getLien());
            $ps->bindValue(':document', $w->getDocument());
            $ps->execute();


            $query = 'INSERT INTO lexique_vue_mot_reference (mot, reference) VALUES (:mot, :reference)';

            $ps = $this->_db->prepare($query);
            $ps->bindValue(':mot', $mot->getId());
            $ps->bindValue(':reference', $this->select_latest_inserted_reference_id());
            $ps->execute();
        }
    }

    /**
     * Fetch the latest inserted Reference's id.
     *
     * @return int the reference ID
     */
    private function select_latest_inserted_reference_id(): int
    {
        $query = 'SELECT id FROM lexique_references_biblio ORDER BY 1 DESC LIMIT 1';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $refId = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $refId = $row->id;
            }
        }

        return $refId;
    }

    /**
     * Update entirely a Mot by given the updated one.
     * The id should be there since the not-updated-yet Mot will be fetched for comparison.
     * The references are not going to be updated with this method, but only added as new references.
     *
     * @param Mot $updatedMot
     * @return bool
     * @throws Exception
     */
    public function update_complete_mot(Mot $updatedMot): bool
    {
        $mots = $this->select_mots_libelles();
        $champsLexicaux = $this->select_champs_lexicaux_intitules();
        $periodes = $this->select_periodes_noms();
        $siecles = $this->select_siecles();
        $typesDeVariants = $this->select_types_variants_orthographiques_libelles();
        $actualMot = $this->select_mot_by_id($updatedMot->getId());


        $this->_db->beginTransaction();
        try {
            $this->update_mot($actualMot, $updatedMot);

            $updatedMot->setId($actualMot->getId());

            $this->update_variants($actualMot, $updatedMot, $typesDeVariants);

            $this->update_antonymes($actualMot, $updatedMot, $mots);

            $this->update_synonymes($actualMot, $updatedMot, $mots);

            $this->update_mot_champsLexicaux($actualMot, $updatedMot, $champsLexicaux);

            $this->update_mot_periodes($actualMot, $updatedMot, $periodes);

            $this->update_mot_siecles($actualMot, $updatedMot, $siecles);

            $this->insert_mot_references($updatedMot);
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw new Exception($e);
        }
        return $this->_db->commit();
    }

    /**
     * Fetch a Mot based on its unique id and complete the id, libelle, definition and illustration.
     *
     * @param $id
     * @return Mot|null the Mot or NULL if it does not exist
     */
    public function select_mot_by_id($id): ?Mot
    {
        $query = 'SELECT id, libelle, definition, illustration FROM lexique_mots WHERE id = :id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(":id", $id);
        $ps->execute();

        return $this->complete_mot_after_select_on_lexique_mots($ps);
    }

    /**
     * Complete a word after having executed 'SELECT id, libelle, definition, illustration FROM lexique_mots ...'.
     *
     * @param $ps PDOStatement the prepared query after execution.
     *
     * @return Mot|null
     */
    private function complete_mot_after_select_on_lexique_mots(PDOStatement $ps): ?Mot
    {
        $mot = NULL;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mot = new Mot($row->libelle, $row->definition);
                $mot->setId($row->id);
                $mot->setIllustration($row->illustration);
            }
        }

        if (!empty($mot)) {
            $mot->setSynonymes($this->select_synonymes_libelles($mot->getId()));
            $mot->setAntonymes($this->select_antonymes_libelles($mot->getId()));
            $mot->setChampsLexicaux($this->select_mot_champs_lexicaux_intitules($mot->getId()));
            $mot->setPeriodes($this->select_mot_periodes_noms($mot->getId()));
            $mot->setSiecles($this->select_mot_siecles($mot->getId()));
            $mot->setReferences($this->select_mot_references($mot->getId()));
            $mot->setVariantsOrthographiques($this->select_variants_orthographiques_from_mot_id($mot->getId()));
        }
        return $mot;
    }

    /**
     * Select every existing Synonyme's libelle of a word by the given id.
     * If the id does not exist, an empty array is returned.
     *
     * @param int $id
     * @return array
     */
    public function select_synonymes_libelles(int $id): array
    {
        $query = 'SELECT m.libelle FROM lexique_mots m, lexique_synonymes s 
                    WHERE s.mot_a = :id AND m.id = s.mot_b ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $arr[] = $row->libelle;
            }
        }

        return $arr;
    }

    /**
     * Select every existing Antonyme's libelle of a word by the given id.
     * If the id does not exist, an empty array is returned.
     *
     * @param $id
     * @return array
     */
    public function select_antonymes_libelles($id): array
    {
        $query = 'SELECT m.libelle FROM lexique_mots m, lexique_antonymes a 
                    WHERE a.mot_a = :id AND m.id = a.mot_b ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $arr[] = $row->libelle;
            }
        }

        return $arr;
    }

    /**
     * Select every existing ChampLexical's intitule of a word by the given id.
     * If the id does not exist, an empty array is returned.
     *
     * @param $id
     * @return array
     */
    public function select_mot_champs_lexicaux_intitules($id): array
    {
        $query = 'SELECT cl.intitule FROM lexique_champs_lexicaux cl, lexique_vue_mot_champ_lexical vcl 
                    WHERE vcl.mot = :id AND vcl.champ_lexical = cl.id ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $arr[] = $row->intitule;
            }
        }

        return $arr;
    }

    /**
     * Select every existing Periode's nom of a word by the given id.
     * If the id does not exist, an empty array is returned.
     *
     * @param $id
     * @return array
     */
    public function select_mot_periodes_noms($id): array
    {
        $query = 'SELECT p.nom FROM lexique_periodes p, lexique_vue_mot_periode vp 
                    WHERE vp.mot = :id AND vp.periode = p.id ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $arr[] = $row->nom;
            }
        }

        return $arr;
    }

    /**
     * Select every existing Siecle of a word by the given id.
     * If the id does not exist, an empty array is returned.
     *
     * @param $id
     * @return array
     */
    public function select_mot_siecles($id): array
    {
        $query = 'SELECT s.numero FROM lexique_siecles s, lexique_vue_mot_siecle vs 
                    WHERE vs.mot = :id AND vs.siecle = s.numero ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $siecle = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $siecle[] = $row->numero;
            }
        }

        return $siecle;
    }

    /**
     * Select every existing Reference of a Mot by the given Mot's id.
     * If the id does not exist, an empty array is returned.
     *
     * @param $motId
     * @return array of Reference
     */
    public function select_mot_references($motId): array
    {
        $query = 'SELECT r.id, r.auteur, r.titre, r.editeur, r.lieu_edition, r.date_edition, r.pages, r.lien, r.document 
                    FROM lexique_references_biblio r, lexique_vue_mot_reference mr
                    WHERE mr.mot = :motId AND r.id = mr.reference
                    ORDER BY r.titre';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':motId', $motId);
        $ps->execute();

        $references = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $references[] = $this->constructReferenceFromRow($row);
            }
        }
        return $references;
    }

    /**
     * Construct a reference with a row coming from the database.
     * The row must contain the following columns: id, auteur, titre, editeur, lieu_edition, date_edition, pages, lien, document
     *
     * @param $row
     * @return Reference
     */
    private function constructReferenceFromRow($row): Reference
    {
        $reference = new Reference();
        $reference->setId($row->id);
        $reference->setAuteur($row->auteur);
        $reference->setTitre($row->titre);
        $reference->setEditeur($row->editeur);
        $reference->setLieuEdition($row->lieu_edition);
        $reference->setDateEdition($row->date_edition);
        $reference->setPages($row->pages);
        $reference->setLien($row->lien);
        $reference->setDocument($row->document);
        return $reference;
    }

    /**
     * Fetch the variants orthographiques of a Mot by its given id.
     *
     * @param int $id
     * @return array as $array['type'][] where each index lead to an array listing the variants' libelle
     */
    private function select_variants_orthographiques_from_mot_id(int $id): array
    {
        $query = 'SELECT vt.libelle AS type, vo.libelle AS variant 
                    FROM lexique_variants_ortho vo, lexique_variants_ortho_types vt 
                    WHERE vo.mot = :id  AND vo.type = vt.id ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $arr = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                if (empty($arr[$row->type])) $arr[$row->type] = array();
                $arr[$row->type][] = $row->variant;
            }
        }

        return $arr;
    }

    /**
     * Update a Mot's libelle and description.
     *
     * @param Mot $actualMot the Mot before being updated
     * @param Mot $updatedMot the updated Mot
     * @return void
     */
    private function update_mot(Mot $actualMot, Mot $updatedMot): void
    {
        if (strcmp($actualMot->getLibelle(), $updatedMot->getLibelle()) == 0
            && strcmp($actualMot->getDefinition(), $updatedMot->getDefinition()) == 0
            && empty($updatedMot->getIllustration())) {
            return;
        }

        $query = 'UPDATE lexique_mots SET libelle = :libelle, definition = :definition';
        if (!empty($updatedMot->getIllustration())) {
            $query = $query . ', illustration = :illustration';
        }
        $query = $query . ' WHERE id = :id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $updatedMot->getId());
        $ps->bindValue(':libelle', $updatedMot->getLibelle());
        $ps->bindValue(':definition', $updatedMot->getDefinition());
        if (!empty($updatedMot->getIllustration())) {
            $ps->bindValue(':illustration', $updatedMot->getIllustration());
        }
        $ps->execute();
    }

    private function update_variants(Mot $actualMot, Mot $updatedMot, array $typesDeVariants): void
    {
        foreach ($actualMot->getVariantsOrthographiques() as $type => $values) {
            foreach ($values as $i => $variant) {
                if (empty($updatedMot->getVariantsOrthographiques())
                    || empty($updatedMot->getVariantsOrthographiques()[$type])
                    || !in_array($variant, $updatedMot->getVariantsOrthographiques()[$type])) {
                    $this->remove_variant($actualMot->getId(), $variant, $type);
                }
            }
        }

        foreach ($updatedMot->getVariantsOrthographiques() as $type => $values) {
            if (!in_array($type, $typesDeVariants)) {
                $this->insert_variant_type($type);
            }
            foreach ($values as $i => $variant) {
                if (empty($actualMot->getVariantsOrthographiques())
                    || empty($actualMot->getVariantsOrthographiques()[$type])
                    || !in_array($variant, $actualMot->getVariantsOrthographiques()[$type])) {
                    $this->insert_variant($actualMot->getId(), $variant, $type);
                }
            }
        }
    }

    /**
     * Remove a variant with the given Mot's id|libelle, Variant's id|libelle and type's id|libelle
     *
     * @param mixed $mot the Mot's id(int) or libelle(string)
     * @param mixed $variant the variant's id(int) or libelle(string)
     * @param mixed $type the type's is(int) or libelle(string)
     * @return void
     */
    private function remove_variant($mot, $variant, $type): void
    {
        $query = 'DELETE FROM lexique_variants_ortho WHERE mot = :mot AND libelle = :variant AND type = :type';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', is_numeric($mot) ? $mot : $this->select_mot_id_from_libelle($mot));
        $ps->bindValue(':variant', is_numeric($variant) ? $variant : $this->select_variant_id_from_libelle($variant));
        $ps->bindValue(':type', is_numeric($type) ? $type : $this->select_variant_type_id_from_libelle($type));

        $ps->execute();
    }

    /**
     * Fetch a Variant's id by its given libelle.
     *
     * @param string $libelle the variant's libelle
     * @return int the variant's id, -1 if it does not exist
     */
    private function select_variant_id_from_libelle(string $libelle): int
    {
        $query = 'SELECT id FROM lexique_variants_ortho where libelle = :libelle';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':libelle', $libelle);
        $ps->execute();

        $id = -1;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $id = $row->libelle;
            }
        }

        return $id;
    }

    /**
     * Insert a variant type with the given libelle
     *
     * @param string $libelle
     * @return void
     */
    private function insert_variant_type(string $libelle): void
    {
        $query = 'INSERT INTO lexique_variants_ortho_types (libelle) VALUES (:libelle)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':libelle', $libelle);
        $ps->execute();
    }

    /**
     * Insert a Variant with its given Mot's id|libelle, Variant's libelle and type's id|libelle
     *
     * @param mixed $mot the Mot's id(int) or libelle(string)
     * @param string $variant the variant's libelle
     * @param mixed $type the type's is(int) or libelle(string)
     * @return void
     */
    private function insert_variant($mot, string $variant, $type): void
    {
        $query = 'INSERT INTO lexique_variants_ortho (libelle, type, mot) VALUES (:variant, :type, :mot)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', is_numeric($mot) ? $mot : $this->select_mot_id_from_libelle($mot));
        $ps->bindValue(':variant', $variant);
        $ps->bindValue(':type', is_numeric($type) ? $type : $this->select_variant_type_id_from_libelle($type));
        $ps->execute();
    }

    /**
     * Update the links between a Mot and multiple Antonyme by given the former Mot and the updated one.
     * New links will be created (new Mot created if necessary), and removed links will be removed.
     *
     * @param Mot $actualMot the Mot before being updated
     * @param Mot $updatedMot the updated Mot
     * @param array $mots every existing libelle
     * @return void
     */
    private function update_antonymes(Mot $actualMot, Mot $updatedMot, array $mots): void
    {
        foreach ($updatedMot->getAntonymes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $mots)) {
                $this->insert_mot($w);
                $this->insert_antonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            } else if (!in_array($w, $actualMot->getAntonymes())) {
                $this->insert_antonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            }
        }
        foreach ($actualMot->getAntonymes() as $i => $w) {
            if (!in_array($w, $updatedMot->getAntonymes())) {
                $this->remove_antonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            }
        }
    }

    /**
     * Remove a link between a Mot and another word as Antonyme.
     *
     * @param int $motId
     * @param int $antonymeId
     * @return void
     */
    private function remove_antonyme(int $motId, int $antonymeId): void
    {
        $query = 'DELETE FROM lexique_antonymes WHERE mot_a = :lib AND mot_b = :ant OR mot_b = :lib AND mot_a = :ant';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lib', $motId);
        $ps->bindValue(':ant', $antonymeId);

        $ps->execute();
    }

    /**
     * Update the links between a Mot and multiple Synonyme by given the former Mot and the updated one.
     * New links will be created (new Mot created if necessary), and removed links will be removed.
     *
     * @param Mot $actualMot the Mot before being updated
     * @param Mot $updatedMot the updated Mot
     * @param array $mots every existing libelle
     * @return void
     */
    private function update_synonymes(Mot $actualMot, Mot $updatedMot, array $mots): void
    {
        foreach ($updatedMot->getSynonymes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $mots)) {
                $this->insert_mot($w);
                $this->insert_synonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            } else if (!in_array($w, $actualMot->getSynonymes())) {
                $this->insert_synonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            }
        }
        foreach ($actualMot->getSynonymes() as $i => $w) {
            if (!in_array($w, $updatedMot->getSynonymes())) {
                $this->remove_synonyme($updatedMot->getId(), $this->select_mot_id_from_libelle($w));
            }
        }
    }

    /**
     * Remove a link between a Mot and another word as Synonyme.
     *
     * @param int $motId
     * @param int $synonymeId
     * @return void
     */
    private function remove_synonyme(int $motId, int $synonymeId): void
    {
        $query = 'DELETE FROM lexique_synonymes WHERE mot_a = :lib AND mot_b = :ant OR mot_b = :lib AND mot_a = :ant';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lib', $motId);
        $ps->bindValue(':ant', $synonymeId);

        $ps->execute();
    }

    /**
     * Update the links between a Mot and multiple ChampLexical by given the former Mot and the updated one.
     * New links will be created (new ChampLexical created if necessary), and removed links will be removed.
     *
     * @param Mot $actualMot
     * @param Mot $updatedMot
     * @param array $champsLexicaux
     * @return void
     */
    private function update_mot_champsLexicaux(Mot $actualMot, Mot $updatedMot, array $champsLexicaux): void
    {
        foreach ($updatedMot->getChampsLexicaux() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $champsLexicaux)) {
                $this->insert_champ_lexical($w);
                $this->insert_mot_champ_lexical($updatedMot->getId(), $this->select_champ_lexical_id_from_intitule($w));
            } else if (!in_array($w, $actualMot->getChampsLexicaux())) {
                $this->insert_mot_champ_lexical($updatedMot->getId(), $this->select_champ_lexical_id_from_intitule($w));
            }
        }
        foreach ($actualMot->getChampsLexicaux() as $i => $w) {
            if (!in_array($w, $updatedMot->getChampsLexicaux())) {
                $this->remove_mot_champ_lexical($updatedMot->getId(), $this->select_champ_lexical_id_from_intitule($w));
            }
        }
    }

    /**
     * Remove a link between a Mot and a ChampLexical.
     *
     * @param int $motId
     * @param int $champLexicalId
     * @return void
     */
    private function remove_mot_champ_lexical(int $motId, int $champLexicalId): void
    {
        $query = 'DELETE FROM lexique_vue_mot_champ_lexical WHERE mot = :mot AND champ_lexical = :champLexical';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':champLexical', $champLexicalId);

        $ps->execute();
    }

    /**
     * Update the links between a Mot and multiple Periode by given the former Mot and the updated one.
     * New links will be created (new Periode created if necessary), and removed links will be removed.
     *
     * @param Mot $actualMot
     * @param Mot $updatedMot
     * @param array $periodes
     * @return void
     */
    private function update_mot_periodes(Mot $actualMot, Mot $updatedMot, array $periodes): void
    {
        foreach ($updatedMot->getPeriodes() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $periodes)) {
                $this->insert_periode($w);
                $this->insert_mot_periode($updatedMot->getId(), $this->select_periode_id_from_nom($w));
            } else if (!in_array($w, $actualMot->getPeriodes())) {
                $this->insert_mot_periode($updatedMot->getId(), $this->select_periode_id_from_nom($w));
            }
        }
        foreach ($actualMot->getPeriodes() as $i => $w) {
            if (!in_array($w, $updatedMot->getPeriodes())) {
                $this->remove_mot_periode($updatedMot->getId(), $this->select_periode_id_from_nom($w));
            }
        }
    }

    /**
     * Remove a link between a Mot and a Periode.
     *
     * @param int $motId
     * @param int $periodeId
     * @return void
     */
    private function remove_mot_periode(int $motId, int $periodeId): void
    {
        $query = 'DELETE FROM lexique_vue_mot_periode WHERE mot = :mot AND periode = :periode';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':periode', $periodeId);

        $ps->execute();
    }

    /**
     * Update the links between a Mot and multiple Siecle by given the former Mot and the updated one.
     * New links will be created (new Siecle created if necessary), and removed links will be removed.
     *
     * @param Mot $actualMot
     * @param Mot $updatedMot
     * @param array $siecles
     * @return void
     */
    private function update_mot_siecles(Mot $actualMot, Mot $updatedMot, array $siecles): void
    {
        foreach ($updatedMot->getSiecles() as $i => $w) {
            if (empty($w)) continue;

            if (!in_array($w, $siecles)) {
                $this->insert_siecle($w);
                $this->insert_mot_siecle($updatedMot->getId(), $w);
            } else if (!in_array($w, $actualMot->getSiecles())) {
                $this->insert_mot_siecle($updatedMot->getId(), $w);
            }
        }
        foreach ($actualMot->getSiecles() as $i => $w) {
            if (!in_array($w, $updatedMot->getSiecles())) {
                $this->remove_mot_siecle($updatedMot->getId(), $w);
            }
        }
    }

    /**
     * Remove a link between a Mot and a Siecle.
     *
     * @param int $motId
     * @param int $siecleId
     * @return void
     */
    private function remove_mot_siecle(int $motId, int $siecleId): void
    {
        $query = 'DELETE FROM lexique_vue_mot_siecle WHERE mot = :mot AND siecle = :siecle';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':mot', $motId);
        $ps->bindValue(':siecle', $siecleId);

        $ps->execute();
    }

    /**
     * Fetch a random valid Mot.
     * NULL and '<p>&nbsp;</p>' are considered invalid.
     *
     * @return Mot|null the Mot or NULL if it does not exist
     */
    public function select_n_random_valid_mot_with_id_libelle_definition_and_illustration(int $n = 1): ?Mot
    {
        $query = 'SELECT id, libelle, definition, illustration FROM lexique_mots 
                    WHERE definition IS NOT NULL AND STRCMP(definition, :invalid) 
                    ORDER BY RAND() LIMIT ' . $n;

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        return $this->complete_mot_after_select_on_lexique_mots($ps);
    }

    /**
     * Fetch the whole list of Mot with every attribute filled if the data exists.
     *
     * @return array of Mots
     */
    public function select_complete_mots(): array
    {
        $query = 'SELECT id, libelle, definition, illustration FROM lexique_mots ORDER BY 2';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        return $this->complete_mots_after_select_on_lexique_mots($ps);
    }

    /**
     * Complete multiple Mots after having executed 'SELECT id, libelle, definition, illustration FROM lexique_mots ...'.
     *
     * @param $ps PDOStatement the prepared query after execution.
     *
     * @return array
     */
    private function complete_mots_after_select_on_lexique_mots(PDOStatement $ps): array
    {
        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mot = new Mot($row->libelle, $row->definition);
                $mot->setId($row->id);
                $mot->setIllustration($row->illustration);
                $mot->setSynonymes($this->select_synonymes_libelles($mot->getId()));
                $mot->setAntonymes($this->select_antonymes_libelles($mot->getId()));
                $mot->setChampsLexicaux($this->select_mot_champs_lexicaux_intitules($mot->getId()));
                $mot->setPeriodes($this->select_mot_periodes_noms($mot->getId()));
                $mot->setSiecles($this->select_mot_siecles($mot->getId()));
                $mot->setReferences($this->select_mot_references($mot->getId()));
                $mot->setVariantsOrthographiques($this->select_variants_orthographiques_from_mot_id($mot->getId()));
                $tab[] = $mot;
            }
        }
        return $tab;
    }

    /**
     * Fetch a Mot based on its unique libelle.
     *
     * @param $libelle
     * @return Mot|null the Mot or NULL if it does not exist
     */
    public function select_complete_mot_by_libelle($libelle): ?Mot
    {
        $query = 'SELECT id, libelle, definition, illustration FROM lexique_mots WHERE libelle = :libelle';

        $ps = $this->_db->prepare($query);
        $ps->bindValue("libelle", $libelle);
        $ps->execute();

        return $this->complete_mot_after_select_on_lexique_mots($ps);
    }

    /**
     * Update a reference with its id. The id has to exist in the database.
     *
     * @param Reference $updatedReference
     * @return bool
     * @throws Exception
     */
    public function update_reference(Reference $updatedReference): bool
    {

        $query = 'UPDATE lexique_references_biblio 
                  SET auteur = :auteur, titre = :titre, editeur = :editeur, lieu_edition = :lieuEdition, 
                      date_edition = :dateEdition, pages = :pages, lien = :lien';
        if (!empty($updatedReference->getDocument())) {
            $query = $query . ', document = :document';
        }
        $query = $query . ' WHERE id = :id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $updatedReference->getId());
        $ps->bindValue(':auteur', $updatedReference->getAuteur());
        $ps->bindValue(':titre', $updatedReference->getTitre());
        $ps->bindValue(':editeur', $updatedReference->getEditeur());
        $ps->bindValue(':lieuEdition', $updatedReference->getLieuEdition());
        $ps->bindValue(':dateEdition', $updatedReference->getDateEdition());
        $ps->bindValue(':pages', $updatedReference->getPages());
        $ps->bindValue(':lien', $updatedReference->getLien());
        if (!empty($updatedReference->getDocument())) {
            $ps->bindValue(':document', $updatedReference->getDocument());
        }
        return $ps->execute();
    }

    /**
     * Fetch a reference with the given ID.
     *
     * @param int $id
     * @return Reference the reference or NULL if it does not exist
     */
    public function select_reference_by_id(int $id): Reference
    {
        $query = 'SELECT id, auteur, titre, editeur, lieu_edition, date_edition, pages, lien, document 
                    FROM lexique_references_biblio WHERE id = :id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':id', $id);
        $ps->execute();

        $reference = NULL;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $reference = $this->constructReferenceFromRow($row);
            }
        }

        return $reference;
    }

    /**
     * Remove the document of a Reference (DB only -> the file is not touched).
     *
     * @param int $documentId
     * @return bool
     */
    public function remove_document_of_reference(int $documentId): bool
    {
        $query = 'UPDATE lexique_references_biblio SET document = NULL WHERE id = :documentId';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':documentId', $documentId);
        return $ps->execute();
    }

    /**
     * Remove the illustration of a Mot (DB only -> the file is not touched).
     *
     * @param int $motId
     * @return bool
     */
    public function remove_illustration_of_mot(int $motId): bool
    {
        $query = 'UPDATE lexique_mots SET illustration = NULL WHERE id = :motId';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':motId', $motId);
        return $ps->execute();
    }

    /**
     * Delete a Reference with its given id (DB only -> the file is not touched).
     *
     * @param $refId
     * @return bool
     */
    public function deleteReference($refId): bool
    {
        $this->_db->beginTransaction();
        try {
            $query = 'DELETE FROM lexique_vue_mot_reference WHERE reference = :refId';

            $ps = $this->_db->prepare($query);
            $ps->bindValue(':refId', $refId);
            $ps->execute();

            $query = 'DELETE FROM lexique_references_biblio WHERE id = :refId';

            $ps = $this->_db->prepare($query);
            $ps->bindValue(':refId', $refId);
            $ps->execute();
        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
        return $this->_db->commit();
    }

    /**
     * Select every Champ Lexical with id, intitule, description, and the linked Mot.
     *
     * @return array
     */
    public function select_complete_champs_lexicaux(): array
    {
        $query = 'SELECT id, intitule, description FROM lexique_champs_lexicaux ORDER BY 2';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $lex = new ChampLexical($row->intitule, $row->description);
                $lex->setId($row->id);
                $lex->setLinkedMots($this->select_mots_linked_with_champ_lexical($lex->getId()));

                $tab[] = $lex;
            }
        }

        return $tab;
    }

    /**
     * Fetch every Mot's id linked with a Champ Lexical.
     *
     * @param int $lexId the Champ Lexical's id
     * @return array
     */
    private function select_mots_linked_with_champ_lexical(int $lexId): array
    {
        $query = 'SELECT m.id FROM lexique_mots m, lexique_vue_mot_champ_lexical cl 
                    WHERE cl.champ_lexical = :lexId AND cl.mot = m.id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lexId', $lexId);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mot = $this->select_mot_by_id($row->id);
                $tab[] = $mot;
            }
        }

        return $tab;
    }

    /**
     * Select every valid Champ Lexical with id, intitule and description.
     * NULL and '<p>&nbsp;</p>' are considered invalid in the description.
     *
     * @return array
     */
    public function select_valid_champs_lexicaux_with_id_intitule_and_description(): array
    {
        $query = 'SELECT id, intitule, description FROM lexique_champs_lexicaux 
                    WHERE description IS NOT NULL AND STRCMP(description, :invalid) ORDER BY 2';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $lex = new ChampLexical($row->intitule, $row->description);
                $lex->setId($row->id);

                $tab[] = $lex;
            }
        }

        return $tab;
    }

    /**
     * Fetch a Champ Lexical by its given intitule.
     *
     * @param $intitule
     * @return ChampLexical|null
     */
    public function select_complete_champ_lexical_by_intitule($intitule): ?ChampLexical
    {
        $query = 'SELECT id, intitule, description FROM lexique_champs_lexicaux WHERE intitule = :intitule';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':intitule', $intitule);
        $ps->execute();

        $lex = NULL;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $lex = new ChampLexical($row->intitule, $row->description);
                $lex->setId($row->id);
                $lex->setLinkedMots($this->select_mots_linked_with_champ_lexical($lex->getId()));
            }
        }

        return $lex;
    }

    /**
     * Update every field of a Champ Lexical by the given Champ Lexical.
     * The ID shall not be NULL.
     *
     * @param ChampLexical $updatedLex
     * @return bool
     */
    public function update_champ_lexical(ChampLexical $updatedLex): bool
    {
        $query = 'UPDATE lexique_champs_lexicaux SET intitule = :intitule, description = :description WHERE id = :lexId';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':lexId', $updatedLex->getId());
        $ps->bindValue(':intitule', $updatedLex->getIntitule());
        $ps->bindValue(':description', $updatedLex->getDescription());
        return $ps->execute();
    }

    /**
     * Select every existing Periode with their id, nom, debut, fin and description.
     *
     * @return array
     */
    public function select_complete_periodes(): array
    {
        $query = 'SELECT id, nom, debut, fin, description FROM lexique_periodes ORDER BY 2';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $per = new Periode($row->nom, $row->description);
                $per->setId($row->id);
                $per->setDebut($row->debut);
                $per->setFin($row->fin);
                $per->setLinkedMots($this->select_mots_linked_with_periode($per->getId()));

                $tab[] = $per;
            }
        }

        return $tab;
    }

    /**
     * Fetch every Mot's id linked to a given Periode
     *
     * @param int $perId
     * @return array
     */
    private function select_mots_linked_with_periode(int $perId): array
    {
        $query = 'SELECT m.id FROM lexique_mots m, lexique_vue_mot_periode p WHERE p.periode = :perId AND p.mot = m.id';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':perId', $perId);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $mot = $this->select_mot_by_id($row->id);
                $tab[] = $mot;
            }
        }

        return $tab;
    }

    /**
     * Select every valid existing Periode with their id, nom, debut, fin and description.
     * NULL and '<p>&nbsp;</p>' are considered invalid in the description.
     *
     * @return array
     */
    public function select_periodes_with_id_nom_debut_fin_and_description(): array
    {
        $query = 'SELECT id, nom, debut, fin, description FROM lexique_periodes
                    WHERE (description IS NOT NULL AND STRCMP(description, :invalid))
                    OR debut IS NOT NULL OR fin IS NOT NULL ORDER BY 2';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $per = new Periode($row->nom, $row->description);
                $per->setId($row->id);
                $per->setDebut($row->debut);
                $per->setFin($row->fin);

                $tab[] = $per;
            }
        }

        return $tab;
    }

    /**
     * Fetch a complete Periode by its given nom.
     *
     * @param string $nom the Periode's nom
     * @return Periode|null
     */
    public function select_complete_periode_by_nom(string $nom): ?Periode
    {
        $query = 'SELECT id, nom, debut, fin, description FROM lexique_periodes WHERE nom = :nom';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':nom', $nom);
        $ps->execute();

        $per = NULL;
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $per = new Periode($row->nom, $row->description);
                $per->setId($row->id);
                $per->setDebut($row->debut);
                $per->setFin($row->fin);
                $per->setLinkedMots($this->select_mots_linked_with_periode($per->getId()));
            }
        }

        return $per;
    }

    /**
     * Update a Periode with the given Periode.
     * The ID should be in that Periode.
     *
     * @param Periode $updatedPer
     * @return bool
     */
    public function update_periode(Periode $updatedPer): bool
    {
        $query = 'UPDATE lexique_periodes SET nom = :nom, description = :description, debut = :debut, fin = :fin 
                    WHERE id = :perId';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':perId', $updatedPer->getId());
        $ps->bindValue(':nom', $updatedPer->getNom());
        $ps->bindValue(':description', $updatedPer->getDescription());
        $ps->bindValue(':debut', $updatedPer->getDebut());
        $ps->bindValue(':fin', $updatedPer->getFin());
        return $ps->execute();
    }

    /**
     * Fetch every valid ChampLexical.
     * NULL and '<p>&nbsp;</p>' are considered invalid in the description.
     *
     * @return array
     */
    public function select_valid_champs_lexicaux_with_intitule_and_description(): array
    {
        $query = 'SELECT intitule, description FROM lexique_champs_lexicaux 
                    WHERE description IS NOT NULL AND STRCMP(description, :invalid)';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $tab[$row->intitule] = $row->description;
            }
        }

        return $tab;
    }

    /**
     * Fetch every valid Periode.
     * NULL and '<p>&nbsp;</p>' are considered invalid in the description.
     *
     * @return array
     */
    public function select_valid_periodes_with_nom_debut_fin_and_description(): array
    {
        $query = 'SELECT nom, debut, fin, description FROM lexique_periodes 
                    WHERE (description IS NOT NULL AND STRCMP(description, :invalid))
                    OR (debut IS NOT NULL AND STRCMP(debut, :invalid) 
                    AND fin IS NOT NULL AND STRCMP(fin, :invalid))';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(':invalid', self::$invalidString);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $tab[$row->nom] = array();
                $tab[$row->nom]['description'] = $row->description;
                $tab[$row->nom]['debut'] = $row->debut;
                $tab[$row->nom]['fin'] = $row->fin;
            }
        }

        return $tab;
    }

    /**
     * Fetch every Variant with their type and linked Mot
     *
     * @return array as $array['variantLibelle in lowercase'][[mot][type][origin]] where the variant's libelle is the key in lowercase,
     * ['mot'] contains the Mot's libelle, ['origin'] contains the original variant with its case and [type] contains the variant type's libelle
     */
    public function select_every_variants_with_libelle_and_type(): array
    {
        $query = 'SELECT v.libelle AS variant, m.libelle AS mot, t.libelle AS type 
                    FROM lexique_variants_ortho v, lexique_variants_ortho_types t, lexique_mots m
                    WHERE v.mot = m.id AND v.type = t.id ORDER BY 1';

        $ps = $this->_db->prepare($query);
        $ps->execute();

        $tab = array();
        if ($ps->rowcount() != 0) {
            while ($row = $ps->fetch()) {
                $key = strtolower($row->variant);
                $tab[$key] = array();
                $tab[$key]['mot'] = $row->mot;
                $tab[$key]['type'] = $row->type;
                $tab[$key]['origin'] = $row->variant;
            }
        }

        return $tab;
    }

    /**
     * Check if the given libelle is already taken in the Mots table
     *
     * @param $libelle
     * @return bool
     */
    public function is_libelle_already_taken($libelle): bool
    {
        $query = 'SELECT * FROM lexique_mots WHERE libelle = :libelle';

        $ps = $this->_db->prepare($query);
        $ps->bindValue(":libelle", $libelle);
        $ps->execute();

        return $ps->rowcount() != 0;
    }
}