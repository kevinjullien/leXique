<section id="contenu">

    <a class="btn btn-default btn-sm" id="topBtn" title="Revenir en début de page">&#8593;</a>
    <section id="alphabetLinks" class="text-center"></section></i></a>

    <section class="container-fluid">
        <input class="form-control" id="searchBar" type="text" placeholder="Rechercher">
    </section>
    <section class="container-fluid">
        <table class="table" id="table">
            <thead>
            <tr>
                <th scope="col">Intitulé</th>
                <th>Description</th>
                <th>Nombre de mots liés</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($champsLexicaux) && !empty($champsLexicaux)) {
                $previousLetter = NULL;
                foreach ($champsLexicaux as $i => $lex) {
                    $actualLetter = strtoupper($lex->getIntitule()[0]);
                    if ($actualLetter !== $previousLetter){
                        $previousLetter = $actualLetter;
                        echo '<tr class="letterDivision" id="' . $actualLetter . '" data-aos="flip-left"><th scope="row" colspan="4">' . $actualLetter . '</th></tr>';
                    }?>
                    <tr class="trays" <?php echo $i%2 == 0 ? 'data-aos="fade-up-right"' : 'data-aos="fade-up-left"' ?> data-data="<?php echo $lex->getIntitule() ?>">
                        <th scope="row"><?php echo $lex->getIntitule(); ?></th>
                        <td><?php echo $lex->getDescription(); ?></td>
                        <td><?php echo count($lex->getLinkedMots()); ?></td>
                        <td class="btnEndRowInTd">
                            <form action="/index.php?action=display" method="get">
                                <input name="action" value="display" hidden>
                                <input name="scope" value="lex" hidden>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        value="<?php echo $lex->getIntitule() ?>"
                                        id="<?php echo $lex->getIntitule() ?>"
                                        name="lex" hidden>
                                </button>
                            </form>
                            <form action="/index.php?action=adminData" method="get">
                                <input name="action" value="adminData" hidden>
                                <input name="scope" value="lex" hidden>
                                <input name="subAction" value="edit" hidden>
                                <button type="submit" class="btn btn-secondary btn-sm"
                                        value="<?php echo $lex->getIntitule() ?>"
                                        name="lex"
                                >Édition
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </section>
</section>
<?php require_once(VIEW_PATH . "scriptsCallsListings.php");