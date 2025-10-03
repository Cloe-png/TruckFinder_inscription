<div class="card">
    <h2>Inscription (Food Truck)</h2>
    <form action="index.php?action=do_register" method="post">
        <input type="text" name="nom" placeholder="Nom du gérant" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <input type="text" name="nom_foodtruck" placeholder="Nom du food truck" required>
        <input type="text" name="type_cuisine" placeholder="Type de cuisine" required>
        <input type="text" name="num_tel" placeholder="Numéro de téléphone" required>

        <button type="submit" class="btn">S'inscrire</button>
    </form>
    <p style="margin-top: 15px;">
        Déjà inscrit ? <a href="index.php?action=login">Se connecter</a>
    </p>
</div>
