<nav class="navbar fixed-bottom" id="footer">
    <section class="mx-auto order-0">
        &copy; 2020-<?php echo getdate()['year'] ?> by '&Alpha; to &omega;'
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === DEVUSERNAME) echo "<br>$time ms" ?>
    </section>
</nav>

<!-- ########## Boostrap ########## -->

<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

<!-- ########## End Bootstrap ########## -->

<!-- Bootstrap tooltip activation -->
<script type="application/javascript">$(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })</script>

<!-- AOS https://michalsnik.github.io/aos/ -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>

</body>

</html>