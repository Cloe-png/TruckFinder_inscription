<div class="card">
    <h2 style="color: var(--turquoise);">Connexion</h2>
    <form action="index.php?action=do_login" method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <p style="margin-top: 15px; text-align: center;">
        Pas encore de compte ? <a href="index.php?action=register">S'inscrire</a>
    </p>
</div>
