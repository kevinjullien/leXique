<section id="contenu">
    <section class="row">
        <section
                class="col-xl-<?php echo empty($chosenMot->getIllustration()) && empty($chosenMot->getVariantsOrthographiques()) ? 12 : 9 ?> col-lg-12">
            <h1><?php echo $chosenMot->getLibelle() ?></h1>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']) { ?>
                <form action="/index.php?action=adminData&scope=words&subAction=edit&word=<?php echo $chosenMot->getLibelle() ?>"
                      method="get" id="editInWordDisplay" enctype="multipart/form-data">
                    <input name="action" value="adminData" hidden>
                    <input name="scope" value="words" hidden>
                    <input name="subAction" value="edit" hidden>
                    <input name="word" value="<?php echo $chosenMot->getLibelle() ?>" hidden>
                    <button type="submit" class="btn btn-info">Éditer
                    </button>
                </form>
            <?php } ?>

            <h2>Définition</h2>
            <?php if (empty($chosenMot->getDefinition())) echo $emptyDataText;
            else echo '<p>' . $chosenMot->getDefinition() . '</p>'; ?>
        </section>
        <?php if (!empty($chosenMot->getIllustration()) || !empty($chosenMot->getVariantsOrthographiques())) { ?>
            <hr>
            <section class="col-xl-3" id="illustration">
                <section class="row">
                    <?php if (!empty($chosenMot->getIllustration())) { ?>
                        <div class="col-xl-12  col-md-6">
                            <img src="<?php echo FILES_PATH . $chosenMot->getIllustration() ?>"
                                 alt="Illustration du mot: <?php echo $chosenMot->getLibelle() ?>"
                                 class="img-thumbnail rounded">
                        </div>
                    <?php } ?>
                    <?php if (!empty($chosenMot->getVariantsOrthographiques())) { ?>

                        <div id="variants" class="col-xl-12  col-md-6">
                            <h2>Variants orthographiques</h2>
                            <div class="row">
                                <?php foreach ($chosenMot->getVariantsOrthographiques() as $j => $values) { ?>
                                    <div class="col">
                                        <h3><?php echo $j ?></h3>
                                        <?php foreach ($values as $i => $variant) { ?>
                                            <p><?php echo $variant ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </section>
            </section>
        <?php } ?>
    </section>

    <section class="row">
        <section class="col-xl-4 col-md-6">
            <h2>Synonyme<?php if (count($chosenMot->getSynonymes()) > 1) echo 's' ?></h2>
            <?php if (empty($chosenMot->getSynonymes())) echo $emptyDataText;
            else foreach ($chosenMot->getSynonymes() as $j => $synonyme) {
                echo "<p> $synonyme </p>";
            } ?>
        </section>
        <br><br>

        <section class="col-xl-4 col-md-6">
            <h2>Antonyme<?php if (count($chosenMot->getAntonymes()) > 1) echo 's' ?></h2>
            <?php if (empty($chosenMot->getAntonymes())) echo $emptyDataText;
            else  foreach ($chosenMot->getAntonymes() as $j => $antonyme) {
                echo "<p> $antonyme </p>";
            } ?>
        </section>
        <br><br>

        <section class="col-xl-4 col-md-6">
            <h2><?php echo (count($chosenMot->getChampsLexicaux()) > 1) ? "Champs Lexicaux" : "Champ Lexical" ?></h2>
            <?php if (empty($chosenMot->getChampsLexicaux())) echo $emptyDataText;
            else  foreach ($chosenMot->getChampsLexicaux() as $j => $champLexical) {
                echo "<p> $champLexical </p>";
            } ?>
        </section>
        <br><br>

        <section class="col-xl-4 col-md-6">
            <h2>Période<?php if (count($chosenMot->getPeriodes()) > 1) echo 's' ?></h2>
            <?php if (empty($chosenMot->getPeriodes())) echo $emptyDataText;
            else  foreach ($chosenMot->getPeriodes() as $j => $periode) {
                echo "<p> $periode </p>";
            } ?>
        </section>
        <br><br>

        <section class="col-xl-4 col-md-6">
            <h2>Siècle<?php if (count($chosenMot->getSiecles()) > 1) echo 's' ?></h2>
            <?php if (empty($chosenMot->getSiecles())) echo $emptyDataText;
            else  foreach ($chosenMot->getSiecles() as $j => $siecle) {
                echo "<p>" . htmlspecialchars($siecle) . "</p>";
            } ?>
        </section>
        <br><br>

    </section>
    <br><br><br>
    <section class="row">
        <section class="col-xl-12">
            <h2>Référence<?php if (count($chosenMot->getReferences()) > 1) echo 's' ?></h2>
            <?php if (empty($chosenMot->getReferences())) echo $emptyDataText;
            else  foreach ($chosenMot->getReferences() as $j => $reference) {
                if ($j > 0) echo '<hr>' ?>
                <section class="row">
                    <section class="col-xl-3 col-md-6">
                        <h3>Titre</h3>
                        <p>
                            <?php if (empty($reference->getTitre())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getTitre()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Auteur</h3>
                        <p>
                            <?php if (empty($reference->getAuteur())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getAuteur()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Editeur</h3>
                        <p>
                            <?php if (empty($reference->getEditeur())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getEditeur()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Lieu d'édition</h3>
                        <p>
                            <?php if (empty($reference->getLieuEdition())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getLieuEdition()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Date d'édition</h3>
                        <p>
                            <?php if (empty($reference->getDateEdition())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getDateEdition()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Nombre de pages</h3>
                        <p>
                            <?php if (empty($reference->getPages())) echo $emptyDataText;
                            else echo htmlspecialchars($reference->getPages()); ?>
                        </p>
                    </section>
                    <br><br>

                    <section class="col-xl-3 col-md-6">
                        <h3>Lien</h3>
                        <p>
                            <?php if (empty($reference->getLien())) echo $emptyDataText;
                            else echo '<a target="_blank" href="' . htmlspecialchars($reference->getLien()) . '">' . htmlspecialchars($reference->getLien()) . "</a>"; ?>
                        </p>
                    </section>
                </section>
                <br><br>
                <?php if (!empty($reference->getDocument())) {
                    if (substr($reference->getDocument(), strlen($reference->getDocument()) - strlen(".pdf")) === ".pdf") { ?>
                        <section class="embed-responsive embed-responsive-16by9">
                            <iframe src="<?php echo FILES_PATH . $reference->getDocument() ?>"
                                    class="embed-responsive-item"></iframe>
                        </section>
                    <?php } else { ?>
                        <section class="text-center">
                            <img src="<?php echo FILES_PATH . $reference->getDocument() ?>"
                                 alt="Document de la référence intitulée <?php echo $reference->getTitre() ?>"
                                 class="img-fluid border rounded">
                        </section>
                    <?php }
                } ?>
            <?php } ?>
        </section>
    </section>

    <br><br><br>
    <a class="btn btn-primary" href="/index.php?action=display&scope=words" role="button">Revenir à la liste</a>
</section>