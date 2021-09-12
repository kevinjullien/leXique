<section id="contenu">
    <h2>Page des membres</h2>
    <br>
    <br><br>
    <h4 class="blinking"><?php echo $message ?></h4>
    <div class="centre">
        <form action="/index.php?action=adminMembers" method="post">

            <!-- https://getbootstrap.com/docs/4.4/components/forms/ -->

            <label for="utilisateur">Utilisateur*</label>
            <div>
                <input type="text" name="utilisateur" id="utilisateur" value="<?php echo $userinput ?>"
                       aria-describedby="utilisateurhelp" required>
                <small id="utilisateurhelp" class="form-text text-muted">
                    <span class="blinking"><?php if (isset($logininfo['utilisateurmessage'])) echo $logininfo['utilisateurmessage'] ?></span>
                </small>
            </div>
            <br>
            <label for="password">Mot de passe*</label>
            <div>
                <input type="password" name="password" id="password" aria-describedby="passwordhelp" required>
                <small id="passwordhelp" class="form-text text-muted">
                    <span class="blinking"><?php if (isset($logininfo['passwordmessage'])) echo $logininfo['passwordmessage'] ?></span>
                </small>
            </div>

            <label for="passwordControl">Contrôle mot de passe*</label>
            <div>
                <input type="password" name="passwordControl" id="passwordControl" required>
            </div>
            <br>
            <label for="isadmin">Administrateur</label>
            <div>
                <input type="checkbox" name="isadmin" id="isadmin">
            </div>

            <br>
            <p class="smalltext">(*) champs à compléter<br>Le bouton se déverrouille une fois les mots de passes
                complétés et identiques</p>
            <br>
            <p>
                <!--                <input type="submit" value="Ajouter un membre" name="ajoutMembre" id="send">-->
                <button type="submit" class="btn btn-primary btn-lg" value="Ajouter un membre" name="ajoutMembre"
                        id="addMemberBtn" disabled>
                    Ajouter membre
                </button>
            </p>

        </form>
    </div>
    <br><br><br><br>
    <section>
        <?php if (isset($selectionmembre)) { ?>
            Gestion du membre à venir
        <?php } ?>
        <h3>Liste des membres:</h3> (Seuls les membres non administrateurs peuvent être édités)
        <br><br>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Utilisateur</th>
                <th>Administrateur</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <form action="/index.php?action=adminMembers" method="post">


                <?php foreach ($membres as $i => $membre) { ?>

                    <tr>
                        <td><?php echo $membre->getPseudo() ?></td>
                        <td><?php echo(($membre->estAdmin() == 1) ? "Oui" : "Non") ?></td>
                        <td>
                            <?php if ($membre->estAdmin() == 0) { ?>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        value="<?php echo $membre->getPseudo ?>"
                                        name="editer">Éditer
                                </button>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                }
                ?>
            </form>
            </tbody>
        </table>
    </section>
</section>

<script src="<?php echo SCRIPTS_PATH ?>memberPasswordControl.js"></script>
