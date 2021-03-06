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
                <th>Début</th>
                <th>Fin</th>
                <th>Description</th>
                <th>Nombre de mots liés</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($periodes) && !empty($periodes)) {
                require_once(SCRIPTS_PATH . "removeAccents.php");
                $previousLetter = NULL;
                foreach ($periodes as $i => $per) {
                    $actualLetter = strtoupper(remove_accents($per->getNom())[0]);
                    if ($actualLetter !== $previousLetter){
                        $previousLetter = $actualLetter;
                        echo '<tr class="letterDivision" id="' . $actualLetter . '" data-aos="flip-left"><th scope="row" colspan="6">' . $actualLetter . '</th></tr>';
                    }?>
                    <tr class="trays" <?php echo $i%2 == 0 ? 'data-aos="fade-up-right"' : 'data-aos="fade-up-left"' ?> data-data="<?php echo $per->getNom() ?>">
                        <th scope="row"><?php echo $per->getNom(); ?></th>
                        <td><?php echo $per->getDebut(); ?></td>
                        <td><?php echo $per->getFin(); ?></td>
                        <td><?php echo $per->getDescription(); ?></td>
                        <td><?php echo count($per->getLinkedMots()); ?></td>
                        <td class="btnEndRowInTd">
                            <form action="/index.php?action=display" method="get">
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <input name="action" value="display" hidden>
                                <input name="scope" value="per" hidden>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        value="<?php echo $per->getNom() ?>"
                                        id="<?php echo $per->getNom() ?>"
                                        name="per" hidden>
                                </button>
                            </form>
                            <form action="/index.php?action=adminData" method="get">
                                <input name="action" value="adminData" hidden>
                                <input name="scope" value="per" hidden>
                                <input name="subAction" value="edit" hidden>
                                <button type="submit" class="btn btn-secondary btn-sm"
                                        value="<?php echo $per->getNom() ?>"
                                        name="per"
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