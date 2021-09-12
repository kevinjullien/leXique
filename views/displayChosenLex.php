<section id="contenu">
    <section class="row">
        <section class="col-xl-12">
            <h1><?php echo $chosenLex->getIntitule() ?></h1>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']) { ?>
            <form action="/index.php?action=adminData&scope=lex&subAction=editLex&lex=<?php echo $chosenLex->getIntitule() ?>"
                  method="get" id="editInLexDisplay" enctype="multipart/form-data">
                <input name="action" value="adminData" hidden>
                <input name="scope" value="lex" hidden>
                <input name="subAction" value="edit" hidden>
                <input name="lex" value="<?php echo $chosenLex->getIntitule() ?>" hidden>
                <button type="submit" class="btn btn-info">Éditer
                </button>
            </form>
        </section>
        <?php } ?>

        <section class="col-xl-12">
            <h2>Description</h2>
            <?php if (empty($chosenLex->getDescription())) echo $emptyDataText;
            else echo '<p>' . $chosenLex->getDescription() . '</p>'; ?>
        </section>

        <section class="col-xl-12">
            <h2>Les mots repris dans ce champ lexical</h2>
            <section class="row">
                <?php if (empty($chosenLex->getLinkedMots())) echo $emptyDataText;
                else  foreach ($chosenLex->getLinkedMots() as $i => $mot) {
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
    <a class="btn btn-primary" href="/index.php?action=display&scope=lex" role="button">Revenir à la liste</a>
</section>