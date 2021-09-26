<section id="contenu">

    <a class="btn btn-default btn-sm" id="topBtn" title="Revenir en début de page"><i class='fas fa-arrow-up'></i></a>

    <section class="container-fluid">
        <input class="form-control" id="searchBar" type="text" placeholder="Rechercher">
    </section>
    <br>
    <section class="container-fluid">
        <table class="table" id="table">
            <thead>
            <tr>
                <th scope="col">Champ lexical</th>
                <th>Description</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <form action="/index.php?action=display" method="get">
                <input name="action" value="display" hidden>
                <input name="scope" value="lex" hidden>
                <?php
                $previousLetter = NULL;
                foreach ($champsLexicaux as $i => $lex) {
                    $actualLetter = strtoupper($lex->getIntitule()[0]);
                    if ($actualLetter !== $previousLetter){
                        $previousLetter = $actualLetter;
                        echo '<tr class="letterDivision" id="' . $actualLetter . '" data-aos="flip-left"><th scope="row" colspan="3">' . $actualLetter . '</th></tr>';
                    }?>
                    <tr class="trays" <?php echo $i%2 == 0 ? 'data-aos="fade-up-right"' : 'data-aos="fade-up-left"' ?> data-data="<?php echo $lex->getIntitule(); ?>">
                        <th scope="row"><?php echo $lex->getIntitule(); ?></th>
                        <td><?php $i = 350;
                            echo substr($lex->getDescription(), 0, $i);
                            if (strlen($lex->getDescription()) > $i) echo "[........]";
                            echo "<div hidden>" . $lex->getDescription() . "</div>" ?>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-primary btn-sm"
                                    value="<?php echo $lex->getIntitule() ?>"
                                    name="lex" id="<?php echo $lex->getIntitule() ?>"
                                    hidden>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </form>
            </tbody>
        </table>
    </section>
</section>
<script src="<?php echo SCRIPTS_PATH ?>searchBar.js"></script>
<script src="<?php echo SCRIPTS_PATH ?>listSelection.js"></script>
<script src="<?php echo SCRIPTS_PATH ?>goToTopButton.js"></script>
<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>