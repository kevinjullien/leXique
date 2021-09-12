<section id="contenu">
    <div class="alert alert-danger" role="alert">
        La taille maximale des fichiers uploadés est encore à déterminer.<br>
        Les fichiers trop gros ne seront simplement pas uploadés.<br>
        Une vérification est donc nécessaire après chaque ajout et modification.
    </div>
    <form action="/index.php?action=adminData&scope=words&subAction=<?php echo isset($chosenMot) ? "edit" : "add" ?>"
          method="post" id="dataForm" enctype="multipart/form-data">
        <div class="container">
            <div class="row">
                <?php if (isset($chosenMot)) echo '<input name="id" value="' . $chosenMot->getId() . '" hidden>'; ?>
                <!-- mot -->
                <div class="input-group col-lg-<?php echo isset($chosenMot) ? 12 : 8 ?>">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="libelle">Libellé</span>
                    </div>
                    <input type="text" class="form-control enterDontSubmit" aria-label="libelle"
                           aria-describedby="libelle"
                           name="libelle" maxlength="<?php echo $maxInputLength ?>"
                           id="libelleInput" <?php if (isset($chosenMot)) echo 'value="' . $chosenMot->getLibelle() . '"'; ?>
                           required>
                </div>

                <!-- illustration -->
                <div class="input-group col-lg-<?php echo isset($chosenMot) ? 8 : 4 ?>">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="illustrationspan">Illustration</span>
                    </div>
                    <input type="hidden" name="MAX_FILE_SIZE" value="134217728">
                    <input class="form-control enterDontSubmit" type="file" id="illustration" name="illustration">
                </div>

                <?php if (isset($chosenMot)) { ?>
                <div class="input-group col-lg-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="illustrationToDelete" id="check0">
                        <label class="form-check-label" for="check0">Supprimer l'illustration existante</label>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Variants -->
        <div class="container">
            <div id="divVariants" data-name="variant" class="row">
                <datalist id="variantTypesOptions">
                    <?php foreach ($typesVariants

                    as $i => $type) { ?>
                    <option value="<?php echo $type ?>">
                        <?php } ?>
                </datalist>
                <?php if (isset($chosenMot)) {
                    $index = 1;
                    foreach ($chosenMot->getVariantsOrthographiques() as $type => $variants) {
                        foreach ($variants as $i => $variant) { ?>
                            <div class="col-md-4 border border-info rounded">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="variantType<?php echo $index ?>span">Type</span>
                                    </div>
                                    <input class="form-control variantTypes enterDontSubmit"
                                           list="variantTypesOptions"
                                           id="variantType<?php echo $index ?>"
                                           name="variantTypes[]" maxlength="<?php echo $maxInputLength ?>"
                                           value="<?php echo htmlspecialchars($type) ?>">
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="variant<?php echo $index ?>span">Variant</span>
                                    </div>
                                    <input class="form-control variants enterDontSubmit"
                                           id="variant<?php echo $index++ ?>"
                                           name="variants[]" maxlength="<?php echo $maxInputLength ?>"
                                           value="<?php echo htmlspecialchars($variant) ?>">
                                </div>
                            </div>
                        <?php }
                    }
                } ?>
                <div class="col border border-info rounded">
                    <div class="input-group">
                        <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="variantType<?php echo isset($chosenMot) && !empty($chosenMot->getVariantsOrthographiques()) ? $index : 1 ?>span">Type</span>
                        </div>
                        <input class="form-control variantTypes enterDontSubmit" list="variantTypesOptions"
                               id="variantType<?php echo isset($chosenMot) && !empty($chosenMot->getVariantsOrthographiques()) ? $index : 1 ?>"
                               name="variantTypes[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group">
                        <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="variant<?php echo isset($chosenMot) && !empty($chosenMot->getVariantsOrthographiques()) ? $index : 1 ?>span">Variant</span>
                        </div>
                        <input class="form-control variants enterDontSubmit"
                               id="variant<?php echo isset($chosenMot) && !empty($chosenMot->getVariantsOrthographiques()) ? $index : 1 ?>"
                               name="variants[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- description -->
        <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
        <div class="container definition">
            <div class="row">
                <div class="input-group col-lg-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="definition">Definition</span>
                    </div>
                    <textarea class="form-control" aria-label="definition" id="editor"
                              name="definition" maxlength="<?php echo $maxTextareaLength ?>"
                              rows="10"><?php if (isset($chosenMot)) echo $chosenMot->getDefinition(); ?></textarea>
                </div>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#editor'))
                        .catch(error => {
                            console.error(error);
                        });
                </script>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- champs lexicaux -->
                <div id="divChampsLexicaux" data-name="champLexical" class="col-lg-6">
                    <datalist id="champLexicalOptions">
                        <?php foreach ($champsLexicaux

                        as $i => $champLexical) { ?>
                        <option value="<?php echo $champLexical ?>">
                            <?php } ?>
                    </datalist>

                    <?php if (isset($chosenMot)) {
                        foreach ($chosenMot->getChampsLexicaux() as $i => $e) { ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"
                                      id="champLexical<?php echo $i + 1 ?>span">Champ lexical</span>
                                </div>
                                <input class="form-control champsLexicaux enterDontSubmit"
                                       list="champLexicalOptions"
                                       id="champLexical<?php echo $i + 1 ?>"
                                       name="champsLexicaux[]" maxlength="<?php echo $maxInputLength ?>"
                                       value="<?php echo htmlspecialchars($e) ?>">
                            </div>
                        <?php }
                    } ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"
                              id="champLexical<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getChampsLexicaux()) + 1 : 1 ?>span">Champ lexical</span>
                        </div>
                        <input class="form-control champsLexicaux enterDontSubmit" list="champLexicalOptions"
                               id="champLexical<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getChampsLexicaux()) + 1 : 1 ?>"
                               name="champsLexicaux[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>

                <!-- Périodes -->
                <div id="divPeriodes" data-name="periode" class="col-lg-4">
                    <datalist id="periodeOptions">
                        <?php foreach ($periodes

                        as $i => $periode) { ?>
                        <option value="<?php echo $periode ?>">
                            <?php } ?>
                    </datalist>
                    <?php if (isset($chosenMot)) {
                        foreach ($chosenMot->getPeriodes() as $i => $e) { ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="periode<?php echo $i + 1 ?>span">Période</span>
                                </div>
                                <input class="form-control periodes enterDontSubmit" list="periodeOptions"
                                       id="periode<?php echo $i + 1 ?>"
                                       name="periodes[]" maxlength="<?php echo $maxInputLength ?>"
                                       value="<?php echo htmlspecialchars($e) ?>">
                            </div>
                        <?php }
                    } ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"
                              id="periode<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getPeriodes()) + 1 : 1 ?>span">Période</span>
                        </div>
                        <input class="form-control periodes enterDontSubmit" list="periodeOptions"
                               id="periode<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getPeriodes()) + 1 : 1 ?>"
                               name="periodes[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>

                <!-- Siècles -->
                <div id="divSiecles" data-name="siecle" class="col-lg-2">
                    <datalist id="siecleOptions">
                        <?php foreach ($siecles

                        as $i => $siecle) { ?>
                        <option value="<?php echo $siecle ?>">
                            <?php } ?>
                    </datalist>
                    <?php if (isset($chosenMot)) {
                        foreach ($chosenMot->getSiecles() as $i => $e) { ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="siecle<?php echo $i + 1 ?>span">Siècle</span>
                                </div>
                                <input type="number"
                                       class="form-control siecles enterDontSubmit" list="siecleOptions"
                                       id="siecle<?php echo $i + 1 ?>"
                                       name="siecles[]" maxlength="<?php echo $maxInputLength ?>"
                                       value="<?php echo htmlspecialchars($e) ?>">
                            </div>
                        <?php }
                    } ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"
                              id="siecle<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getSiecles()) + 1 : 1 ?>span">Siècle</span>
                        </div>
                        <input type="number"
                               class="form-control siecles enterDontSubmit" list="siecleOptions"
                               id="siecle<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getSiecles()) + 1 : 1 ?>"
                               name="siecles[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Synonymes -->
                <div id="divSynonymes" data-name="synonyme" class="col-lg-6">
                    <datalist id="synonymeOptions">
                        <?php foreach ($mots

                        as $i => $synonyme) { ?>
                        <option value="<?php echo $synonyme['libelle'] ?>">
                            <?php } ?>
                    </datalist>
                    <?php if (isset($chosenMot)) {
                        foreach ($chosenMot->getSynonymes() as $i => $e) { ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="synonyme<?php echo $i + 1 ?>span">Synonyme</span>
                                </div>
                                <input class="form-control synonymes enterDontSubmit" list="synonymeOptions"
                                       id="synonyme<?php echo $i + 1 ?>"
                                       name="synonymes[]" maxlength="<?php echo $maxInputLength ?>"
                                       value="<?php echo htmlspecialchars($e) ?>">
                            </div>
                        <?php }
                    } ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"
                              id="synonyme<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getSynonymes()) + 1 : 1 ?>span">Synonyme</span>
                        </div>
                        <input class="form-control synonymes enterDontSubmit" list="synonymeOptions"
                               id="synonyme<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getSynonymes()) + 1 : 1 ?>"
                               name="synonymes[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>

                <!-- Antonymes -->
                <div id="divAntonymes" data-name="antonyme" class="col-lg-6">
                    <datalist id="antonymeOptions">
                        <?php foreach ($mots

                        as $i => $antonyme) { ?>
                        <option value="<?php echo $antonyme['libelle'] ?>">
                            <?php } ?>
                    </datalist>
                    <?php if (isset($chosenMot)) {
                        foreach ($chosenMot->getAntonymes() as $i => $e) { ?>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="antonyme<?php echo $i + 1 ?>span">Antonyme</span>
                                </div>
                                <input class="form-control antonymes enterDontSubmit" list="antonymeOptions"
                                       id="antonyme<?php echo $i + 1 ?>"
                                       name="antonymes[]" maxlength="<?php echo $maxInputLength ?>"
                                       value="<?php echo htmlspecialchars($e) ?>">
                            </div>
                        <?php }
                    } ?>
                    <div class="input-group">
                        <div class="input-group-prepend">
                        <span class="input-group-text"
                              id="antonyme<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getAntonymes()) + 1 : 1 ?>span">Antonyme</span>
                        </div>
                        <input class="form-control antonymes enterDontSubmit" list="antonymeOptions"
                               id="antonyme<?php echo (isset($chosenMot)) ? sizeof($chosenMot->getAntonymes()) + 1 : 1 ?>"
                               name="antonymes[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- Références -->
        <div class="container">
            <div class="col-lg-12">
                <h4>Références</h4>
            </div>
            <div id="divReferences" class="references">
                <div data-name="reference" class="form-row">
                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refTitre1span">Titre</span>
                        </div>
                        <input class="form-control refTitres enterDontSubmit" type="text" id="refTitre1"
                               name="titres[]"
                               maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refAuteur1span">Auteur</span>
                        </div>
                        <input class="form-control refAuteurs enterDontSubmit" type="text" id="refAuteur1"
                               name="auteurs[]"
                               maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refEditeur1span">Editeur</span>
                        </div>
                        <input class="form-control refEditeurs enterDontSubmit" type="text" id="refEditeur1"
                               name="editeurs[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refPage1span">Pages</span>
                        </div>
                        <input class="form-control refPages enterDontSubmit" type="text" id="refPage1"
                               name="pages[]"
                               maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refLieuEdition1span">Lieu d'édition</span>
                        </div>
                        <input class="form-control refLieuxEdition enterDontSubmit" type="text"
                               id="refLieuEdition1"
                               name="lieuxEdition[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refDateEdition1span">Date d'édition</span>
                        </div>
                        <input class="form-control refDatesEdition enterDontSubmit" type="text"
                               id="refDateEdition1"
                               name="datesEdition[]" maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refLien1span">Lien</span>
                        </div>
                        <input class="form-control refLiens enterDontSubmit" type="text" id="refLien1"
                               name="liens[]"
                               maxlength="<?php echo $maxInputLength ?>">
                    </div>

                    <div class="input-group col-xl-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="refFile1span">Document</span>
                        </div>
                        <input type="hidden" name="MAX_FILE_SIZE" value="134217728">
                        <input class="form-control refFiles" type="file" id="refFile1" name="references[]"
                               maxlength="<?php echo $maxInputLength ?>">
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="container">
            <button id="validationBtn" class="btn btn-primary">Valider</button>
            <?php if (isset($chosenMot)) { ?>
                <a class="btn btn-danger"
                   href="/index.php?action=display&scope=words&word=<?php echo $chosenMot->getLibelle(); ?>">Annuler</a>
            <?php } ?>
        </div>
        <br><br>
    </form>
    <?php if (isset($chosenMot) && sizeof($chosenMot->getReferences()) != 0) { ?>
        <div class="col-lg-12">
            <h4>Référence<?php if (count($chosenMot->getReferences()) > 1) echo 's' ?>
                existante<?php if (count($chosenMot->getReferences()) > 1) echo 's' ?></h4>
            <div class="alert alert-warning" role="alert">
                Attention, les modifications faites dans le formulaire ci-dessus doivent être validées avant de
                modifier
                les références ci-dessous.
                <br>
                Les modifications apportées dans les références existantes sont apportées une fois validation
                effectuée
                sur le popup.
            </div>

        </div>
        <?php foreach ($chosenMot->getReferences() as $i => $reference) {
            if ($i > 0) echo '<hr>' ?>
            <section class="row existingReferences" data-id="<?php echo $reference->getId() ?>">
                <section class="col-xl-3 col-md-6">
                    <h5>Titre</h5>
                    <p id="titre<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getTitre())) echo "N/A";
                        else echo htmlspecialchars($reference->getTitre()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Auteur</h5>
                    <p id="auteur<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getAuteur())) echo "N/A";
                        else echo htmlspecialchars($reference->getAuteur()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Editeur</h5>
                    <p id="editeur<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getEditeur())) echo "N/A";
                        else echo htmlspecialchars($reference->getEditeur()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Lieu d'édition</h5>
                    <p id="lieuEdition<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getLieuEdition())) echo "N/A";
                        else echo htmlspecialchars($reference->getLieuEdition()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Date d'édition</h5>
                    <p id="dateEdition<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getDateEdition())) echo "N/A";
                        else echo htmlspecialchars($reference->getDateEdition()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Nombre de pages</h5>
                    <p id="pages<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getPages())) echo "N/A";
                        else echo htmlspecialchars($reference->getPages()); ?>
                    </p>
                </section>
                <br><br>

                <section class="col-xl-3 col-md-6">
                    <h5>Lien</h5>
                    <p id="lien<?php echo $reference->getId() ?>">
                        <?php if (empty($reference->getLien())) echo "N/A";
                        else echo '<a href="' . htmlspecialchars($reference->getLien()) . '">' . htmlspecialchars($reference->getLien()) . "</a>"; ?>
                    </p>
                </section>
                <br><br>

                <?php if (!empty($reference->getDocument())) {
                    if (substr($reference->getDocument(), strlen($reference->getDocument()) - strlen(".pdf")) === ".pdf") { ?>
                        <iframe id="document<?php echo $reference->getId() ?>"
                                src="<?php echo FILES_PATH . $reference->getDocument() ?>" class="col-xl-3 col-md-6"
                        ></iframe>
                    <?php } else { ?>
                        <img src="<?php echo FILES_PATH . $reference->getDocument() ?>" alt="Document de la référence intitulée <?php echo $reference->getTitre(); ?>"
                             class="col-xl-3 col-md-6 border">
                    <?php }

                } ?>
                <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target=".modalRef<?php echo $reference->getId() ?>"
                        id="modalRef<?php echo $reference->getId() ?>" hidden>Large modal
                </button>

            </section>
            <div class="modal fade modalRef<?php echo $reference->getId() ?>" tabindex="-1" role="dialog"
                 aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Édition de référence</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="/index.php?action=adminData&subAction=edit$word=<?php echo $chosenMot->getLibelle() ?>"
                              method="post" id="dataForm" enctype="multipart/form-data">
                            <div class="modal-body">

                                <input name="editRef" value="true" hidden>
                                <input name="origin" value="<?php echo $chosenMot->getLibelle() ?>" hidden>
                                <input name="id" value="<?php echo $reference->getId(); ?>" hidden>
                                <div class="form-row">
                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refTitreSpan">Titre</span>
                                        </div>
                                        <input class="form-control refTitres enterDontSubmit" type="text"
                                               name="titre" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getTitre())) echo htmlspecialchars($reference->getTitre()); ?>"
                                               required>
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refAuteurSpan">Auteur</span>
                                        </div>
                                        <input class="form-control refAuteurs enterDontSubmit" type="text"
                                               name="auteur" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getAuteur())) echo htmlspecialchars($reference->getAuteur()); ?>">
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refEditeurSpan">Editeur</span>
                                        </div>
                                        <input class="form-control refEditeurs enterDontSubmit" id="refEditeur1"
                                               name="editeur" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getEditeur())) echo htmlspecialchars($reference->getEditeur()); ?>">
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refPageSpan">Pages</span>
                                        </div>
                                        <input class="form-control refPages enterDontSubmit" type="text"
                                               name="page" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getPages())) echo htmlspecialchars($reference->getPages()); ?>">
                                    </div>


                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="refLieuEditionSpan">Lieu d'édition</span>
                                        </div>
                                        <input class="form-control refLieuxEdition enterDontSubmit" type="text"
                                               name="lieuEdition" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getLieuEdition())) echo htmlspecialchars($reference->getLieuEdition()); ?>">
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="refDateEditionSpan">Date d'édition</span>
                                        </div>
                                        <input class="form-control refDatesEdition enterDontSubmit" type="text"
                                               name="dateEdition" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getDateEdition())) echo htmlspecialchars($reference->getDateEdition()); ?>">
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refLienSpan">Lieu</span>
                                        </div>
                                        <input class="form-control refLiens enterDontSubmit" type="text"
                                               name="lien" maxlength="<?php echo $maxInputLength ?>"
                                               value="<?php if (!empty($reference->getLien())) echo htmlspecialchars($reference->getLien()); ?>">
                                    </div>

                                    <div class="input-group col-xl-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="refFileSpan">Document</span>
                                        </div>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="134217728">
                                        <input class="form-control refFiles" type="file" name="reference">

                                    </div>
                                </div>
                                <?php if (!empty($reference->getDocument())) { ?>
                                    <div class="form-check">
                                        <input class="form-check-input refFilesToDelete" type="checkbox"
                                               name="documentToDelete" id="check1">
                                        <label class="form-check-label" for="check1">Supprimer le document
                                            existant</label>
                                    </div>
                                <?php } ?>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary col-sm-12"
                                        id="confirmRef<?php echo $reference->getId() ?>">Confirmer
                                </button>
                                <button type="button" class="btn btn-secondary col-sm-12" data-dismiss="modal">
                                    Fermer
                                </button>
                                <button class="btn btn-danger col-sm-12 deleteReferenceBtn" type="button"
                                        data-id="<?php echo $reference->getId() ?>">Supprimer la référence
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
    } ?>
</section>
<script src="<?php echo SCRIPTS_PATH ?>dataForm.js"></script>
