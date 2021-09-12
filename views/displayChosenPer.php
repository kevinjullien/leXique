<section id="contenu">
    <section class="row">
        <section
                class="col-xl-<?php echo empty($chosenPer->getDebut()) && empty($chosenPer->getFin()) ? 12 : 9 ?> col-lg-12">
            <h1><?php echo $chosenPer->getNom() ?></h1>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']) { ?>
                <form action="/index.php?action=adminData&scope=per&subAction=editPer&per=<?php echo $chosenPer->getNom() ?>"
                      method="get" id="editInPerDisplay" enctype="multipart/form-data">
                    <input name="action" value="adminData" hidden>
                    <input name="scope" value="per" hidden>
                    <input name="subAction" value="edit" hidden>
                    <input name="per" value="<?php echo $chosenPer->getNom() ?>" hidden>
                    <button type="submit" class="btn btn-info">Éditer
                    </button>
                </form>

            <?php } ?>
        </section>

        <?php if (!empty($chosenPer->getDebut()) || !empty($chosenPer->getFin())) { ?>
            <section class="col-xl-3">

                    <h2>Précisions</h2>
                    <div class="row">
                        <div class="col">
                            <h3>Début</h3>
                            <p><?php echo empty($chosenPer->getDebut()) ? $emptyDataText : $chosenPer->getDebut(); ?></p>
                        </div>
                        <div class="col">
                            <h3>Fin</h3>
                            <p><?php echo empty($chosenPer->getFin()) ? $emptyDataText : $chosenPer->getFin(); ?></p>
                        </div>
                    </div>

            </section>
        <?php } ?>

        <section class="col-xl-12">
            <h2>Description</h2>
            <?php echo '<p>' . empty($chosenPer->getDescription()) ? $emptyDataText : $chosenPer->getDescription() . '</p>'; ?>
        </section>





        <section class="col-xl-12">
            <h2>Les mots repris dans cette période</h2>
            <section class="row">
                <?php if (empty($chosenPer->getLinkedMots())) echo $emptyDataText;
                else  foreach ($chosenPer->getLinkedMots() as $i => $mot) {
                    $replacement = $mot->getLibelle();
                    if (array_key_exists($mot->getLibelle(), $mots)) {
                        $limit = 350;
                        $suffix = strlen($mot->getDefinition()) > $limit ? "[........]" : "";
                        $replacement = '<a data-toggle="tooltip" data-html="true" title="' . substr($mot->getDefinition(), 0, $limit) . $suffix . '" href="index.php?action=display&scope=words&word=' . $mot->getLibelle() . '">' . $mot->getLibelle() . '</a>';
                    }
                    echo "<p class='col'>" . $replacement . "</p>";
                } ?>
            </section>
        </section>
    </section>
    <br><br><br>
    <a class="btn btn-primary" href="/index.php?action=display&scope=per" role="button">Revenir à la liste</a>
</section>