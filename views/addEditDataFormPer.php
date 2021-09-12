<section id="contenu">
    <form action="/index.php?action=adminData&scope=per&subAction=edit"
          method="post" id="dataForm">
        <!--suppress HtmlFormInputWithoutLabel -->
        <input name="id" value="<?php if (isset($chosenPer)) echo $chosenPer->getId() ?>" hidden>
        <input name="per" value="<?php if (isset($chosenPer)) echo $chosenPer->getNom() ?>" hidden>
        <section class="container">
            <!-- Intitule -->
            <div class="input-group col-lg-12">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="nom">Nom</span>
                </div>
                <input type="text" class="form-control enterDontSubmit" aria-label="nom"
                       aria-describedby="nom"
                       name="nom" maxlength="<?php echo $maxInputLength ?>"
                       id="nomInput" <?php if (isset($chosenPer)) echo 'value="' . $chosenPer->getNom() . '"'; ?>
                       required>
            </div>

            <!-- Debut -->
            <div class="input-group col-lg-12">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="debut">Début</span>
                </div>
                <input type="text" class="form-control enterDontSubmit" aria-label="debut"
                       aria-describedby="debut"
                       name="debut" maxlength="<?php echo $maxInputLength ?>"
                       id="debutInput" <?php if (isset($chosenPer)) echo 'value="' . $chosenPer->getDebut() . '"'; ?> >
            </div>

            <!-- Fin -->
            <div class="input-group col-lg-12">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="fin">Fin</span>
                </div>
                <input type="text" class="form-control enterDontSubmit" aria-label="nom"
                       aria-describedby="fin"
                       name="fin" maxlength="<?php echo $maxInputLength ?>"
                       id="finInput" <?php if (isset($chosenPer)) echo 'value="' . $chosenPer->getFin() . '"'; ?> >
            </div>

            <!-- Description -->
            <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
            <div class="description">
                <div class="input-group col-lg-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="description">Description</span>
                    </div>
                    <textarea class="form-control" aria-label="description" id="editor"
                              name="description" maxlength="<?php echo $maxTextareaLength ?>"
                              rows="10"><?php if (isset($chosenPer)) echo $chosenPer->getDescription(); ?></textarea>
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
                <?php if (isset($chosenPer)) { ?>
                    <a class="btn btn-danger"
                       href="/index.php?action=display&scope=per&per=<?php echo $chosenPer->getNom(); ?>">Annuler</a>
                <?php } ?>
            </div>
        </section>
    </form>
</section>
<script src="<?php echo SCRIPTS_PATH ?>dataForm.js"></script>