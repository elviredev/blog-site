<?php
if(isset($messages)){
    foreach($messages as $message){
        echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}

?>

<header class="header">
    <section class="flex">
        <a href="home.php" class="logo">BlogElv.</a>

        <form action="search.php" method="post" class="search-form">
            <input type="text" name="search_item" placeholder="Rechercher les articles..." maxlength="100" required>
            <button type="submit" name="search_btn" class="fas fa-search"></button>
        </form>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <nav class="navbar">
            <a href="home.php"><i class="fas fa-angle-right"></i><span>accueil</span></a>
            <a href="posts.php"><i class="fas fa-angle-right"></i><span>articles</span></a>
            <a href="all_category.php"><i class="fas fa-angle-right"></i><span>catégories</span></a>
            <a href="authors.php"><i class="fas fa-angle-right"></i><span>auteurs</span></a>
            <a href="login.php"><i class="fas fa-angle-right"></i><span>s'identifier</span></a>
            <a href="register.php"><i class="fas fa-angle-right"></i><span>s'enregistrer</span></a>
        </nav>

        <div class="profile">
            <?php
            if (!empty($conn)) {
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                /** @var TYPE_NAME $user_id */
                $select_profile->execute([$user_id]);
                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
                    <p><?= $fetch_profile['name'] ?></p>
                    <a href="update.php" class="btn">modifier profil</a>
                    <div class="flex-btn">
                        <a href="login.php" class="option-btn">se connecter</a>
                        <a href="register.php" class="option-btn">s'enregistrer</a>
                    </div>
                    <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('Se déconnecter ' +
                     'du site ?')">se
                        déconnecter</a>
            <?php
                } else {
            ?>
                    <p>Merci de vous connecter !</p>
                    <div class="flex-btn">
                        <a href="login.php" class="option-btn">s'identifier</a>
                        <a href="register.php" class="btn">s'enregistrer</a>
                    </div>
            <?php
                }
            }
            ?>
        </div>

    </section>
</header>