<section id="contenu">
    <form action="/index.php?action=adminData&scope=lex&subAction=edit"
          method="post" id="dataForm">
        <!--suppress HtmlFormInputWithoutLabel -->
        <input name="id" value="<?php if (isset($chosenLex)) echo $chosenLex->getId() ?>" hidden>
        <!--suppress HtmlFormInputWithoutLabel -->
        <input name="lex" value="<?php if (isset($chosenLex)) echo $chosenLex->getIntitule() ?>" hidden>
        <section class="container">
            <!-- Intitule -->
            <div class="input-group col-lg-12">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="intitule">Intitulé</span>
                </div>
                <input type="text" class="form-control enterDontSubmit" aria-label="intitule"
                       aria-describedby="intitule"
                       name="intitule" maxlength="<?php echo $maxInputLength ?>"
                       id="intituleInput" <?php if (isset($chosenLex)) echo 'value="' . $chosenLex->getIntitule() . '"'; ?>
                       required>
            </div>

            <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
            <div class="description">
                <div class="input-group col-lg-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="description">Description</span>
                    </div>
                    <textarea class="form-control" aria-label="description" id="editor"
                              name="description" maxlength="<?php echo $maxTextareaLength ?>"
                              rows="10"><?php if (isset($chosenLex)) echo $chosenLex->getDescription(); ?></textarea>
                </div>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#editor'))
                        .catch(error => {
                            console.error(error);
                        });
                </script>
            </div>
            <br><br>
            <div class="container">
                <button id="validationBtn" class="btn btn-primary">Valider</button>
                <?php if (isset($chosenLex)) { ?>
                    <a class="btn btn-danger"
                       href="/index.php?action=display&scope=lex&lex=<?php echo $chosenLex->getIntitule(); ?>">Annuler</a>
                <?php } ?>
            </div>
        </section>
    </form>
</section>
<script src="<?php echo SCRIPTS_PATH ?>enterDontSubmit.js"></script>