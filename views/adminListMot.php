<section id="contenu">

    <a class="btn btn-default btn-sm" id="topBtn" title="Revenir en début de page">&#8593;</a>
    <section id="alphabetLinks" class="text-center"></section></i></a>
    <section id="alphabetLinks" class="text-center"></section>

    <section class="container-fluid">
        <form action="/index.php?action=adminList&scope=words" method="get">
            <input name="action" value="adminList" hidden>
            <input name="scope" value="words" hidden>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="submit"
                        class="btn btn-<?php if (!isset($_GET['filter']) || $_GET['filter'] === "all") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="all" name="filter">
                    Tous les mots
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "def") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="def" name="filter">
                    Mots sans définition
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "syn") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="syn" name="filter">
                    Mots sans synonymes
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "ant") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="ant" name="filter">
                    Mots sans antonymes
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "lex") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="lex" name="filter">
                    Mots sans champ lexical
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "cen") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="cen" name="filter">
                    Mots sans siècle
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "per") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="per" name="filter">
                    Mots sans période
                </button>
                <button type="submit"
                        class="btn btn-<?php if (isset($_GET['filter']) && $_GET['filter'] === "ref") echo 'primary'; else echo 'secondary'; ?> btn-lg"
                        value="ref" name="filter">
                    Mots sans Référence
                </button>
            </div>
        </form>
    </section>
    <br>
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
                <th>Synonymes</th>
                <th>Antonymes</th>
                <th>Champs lexicaux</th>
                <th>Périodes</th>
                <th>Siècles</th>
                <th>Nombre de références</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($mots) && !empty($mots)) {
                require_once(SCRIPTS_PATH . "removeAccents.php");
                $previousLetter = NULL;
                foreach ($mots as $i => $mot) {
                    $actualLetter = strtoupper(remove_accents($mot->getLibelle()))[0];
                    if ($actualLetter !== $previousLetter){
                        $previousLetter = $actualLetter;
                        echo '<tr class="letterDivision" id="' . $actualLetter . '" data-aos="flip-left"><th scope="row" colspan="9">' . $actualLetter . '</th></tr>';
                    }?>
                    <tr class="trays" <?php echo $i%2 == 0 ? 'data-aos="fade-up-right"' : 'data-aos="fade-up-left"' ?> data-data="<?php echo $mot->getLibelle() ?>">
                        <th scope="row"><?php echo $mot->getLibelle(); ?></th>
                        <td><?php echo $mot->getDefinition(); ?></td>
                        <td>
                            <?php foreach ($mot->getSynonymes() as $j => $w) {
                                echo $j == 0 ? $w : ', ' . $w;
                            } ?>
                        </td>
                        <td>
                            <?php foreach ($mot->getAntonymes() as $j => $w) {
                                echo $j == 0 ? $w : ', ' . $w;
                            } ?>
                        </td>
                        <td>
                            <?php foreach ($mot->getChampsLexicaux() as $j => $w) {
                                echo $j == 0 ? $w : ', ' . $w;
                            } ?>
                        </td>
                        <td>
                            <?php foreach ($mot->getPeriodes() as $j => $w) {
                                echo $j == 0 ? $w : ', ' . $w;
                            } ?>
                        </td>
                        <td>
                            <?php foreach ($mot->getSiecles() as $j => $w) {
                                echo $j == 0 ? $w : ', ' . $w;
                            } ?>
                        </td>
                        <td>
                            <?php echo count($mot->getReferences()) ?>
                        </td>
                        <td class="btnEndRowInTd">
                            <form action="/index.php?action=display" method="get">
                                <input name="action" value="display" hidden>
                                <input name="scope" value="words" hidden>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        value="<?php echo $mot->getLibelle() ?>"
                                        id="<?php echo $mot->getLibelle() ?>"
                                        name="word" hidden>
                                </button>
                            </form>
                            <form action="/index.php?action=adminData" method="get">
                                <input name="action" value="adminData" hidden>
                                <input name="scope" value="words" hidden>
                                <input name="subAction" value="edit" hidden>
                                <button type="submit" class="btn btn-secondary btn-sm"
                                        value="<?php echo $mot->getLibelle() ?>"
                                        name="word"
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