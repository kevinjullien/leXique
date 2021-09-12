<section id="contenu">
    <h2>Page de connexion</h2>
    <br>
    <h4 class="blinking"><?php echo $message ?></h4>
    <div class="centre">
        <form action="/index.php?action=login" method="post">
            <!-- https://getbootstrap.com/docs/4.4/components/forms/ -->
            <label for="user">Utilisateur*</label>
            <div class="col-lg-2">
                <input type="text" name="user" id="user" value="<?php echo $userinput ?>"
                       aria-describedby="userhelp" required>
                <small id="userhelp" class="form-text text-muted">
                    <span class="blinking"><?php if (isset($logininfo['userMsg'])) echo $logininfo['userMsg'] ?></span>
                </small>
            </div>
            <label for="password">Mot de passe*</label>
            <div class="col-lg-2">
                <input type="password" name="password" id="password" aria-describedby="passwordhelp" required>
                <small id="passwordhelp" class="form-text text-muted">
                    <span class="blinking"><?php if (isset($logininfo['passwordMsg'])) echo $logininfo['passwordMsg'] ?></span>
                </small>
            </div>
            <br>
            <p class="smalltext">(*) champs à compléter</p>
            <br>
            <p>
                <button type="submit" class="btn btn-secondary btn-lg" value="Se connecter" name="userLogin">
                    Se connecter
                </button>
            </p>
        </form>
    </div>

</section>