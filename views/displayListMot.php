<section id="contenu">

    <a class="btn btn-default btn-sm" id="topBtn" title="Revenir en début de page">&#8593;</a>
    <section id="alphabetLinks" class="text-center"></section></i></a>

    <section class="container-fluid">
        <input class="form-control" id="searchBar" type="text" placeholder="Rechercher">
    </section>
    <br>
    <section class="container-fluid">
        <table class="table" id="table">
            <thead>
            <tr>
                <th scope="col">Mot</th>
                <th>Définition</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <form action="/index.php?action=display" method="get">
                <input name="action" value="display" hidden>
                <input name="scope" value="words" hidden>
                <?php if (isset($mots) && !empty($mots)) {
                    require_once(SCRIPTS_PATH . "removeAccents.php");
                    $n = 0;
                    $previousLetter = NULL;
                    foreach ($mots as $libelle => $definition) {
                        $actualLetter = strtoupper(remove_accents($libelle))[0];
                        if ($actualLetter !== $previousLetter){
                            $previousLetter = $actualLetter;
                            echo '<tr class="letterDivision" id="' . $actualLetter . '" id="' . $actualLetter . '" data-aos="flip-left"><th scope="row" colspan="3">' . $actualLetter . '</th></tr>';
                        }?>
                        <tr class="trays" <?php echo $n++%2 == 0 ? 'data-aos="fade-up-right"' : 'data-aos="fade-up-left"' ?> data-data="<?php echo $libelle ?>">
                            <th scope="row"><?php echo $libelle; ?></th>
                            <td><?php $i = 350;
                                echo substr($definition, 0, $i);
                                if (strlen($definition) > $i) echo "[........]";
                                echo "<div hidden>" . $definition . "</div>" ?>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        value="<?php echo $libelle ?>"
                                        name="word" id="<?php echo $libelle ?>"
                                        hidden>
                                </button>
                            </td>
                        </tr>
                    <?php }
                } ?>
            </form>
            </tbody>
        </table>
    </section>
</section>
<?php require_once(VIEW_PATH . "scriptsCallsListings.php");