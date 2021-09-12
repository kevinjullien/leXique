<section id="contenu">

    <?php //var_dump($_SESSION) //debug?>

    <?php echo $texte ?? "Bienvenue" ?>
    <?php
    if (!empty($randomMot)) { ?>
        <br><br>
        <h2>Le mot du jour</h2>
        <br><br>
        <div class="container">
            <div class="row" id="motDuJour">
                <div class="col-xl-<?php echo empty($randomMot->getIllustration()) ? 12 : 6 ?> col-sm-12 motDuJourLibelle text-center align-middle">
                    <a class="focus-in-expand align-middle"
                       href="/index.php?action=display&scope=words&word=<?php echo $randomMot->getLibelle(); ?>">
                        <?php echo $randomMot->getLibelle(); ?></a>
                </div>
                <?php if (!empty($randomMot->getIllustration())) { ?>
                    <div class="col-xl-6 col-sm-12 motDuJourIllustration text-center align-middle">
                        <a class="slide-in-bck-br"
                           href="/index.php?action=display&scope=words&word=<?php echo $randomMot->getLibelle(); ?>">
                            <img class="slide-in-bck-br" src="<?php echo FILES_PATH . $randomMot->getIllustration() ?>" alt="Illustration du mot: <?php echo $randomMot->getLibelle(); ?>">
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="col-xl-12 slide-in-elliptic-left-fwd motDuJourDefinition text-center">
                    <?php $i = 500;
                    echo substr($randomMot->getDefinition(), 0, $i);
                    if (strlen($randomMot->getDefinition()) > $i) echo "[........]";
                    ?>
                </div>
            </div>
        </div>
    <?php }
    ?>
</section>

<?php //phpinfo(); //debug ?>
