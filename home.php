<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}


?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>accueil</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER SECTION START -->
<?php include 'components/user_header.php'; ?>
<!-- HEADER SECTION END -->

<!-- HOME-GRID SECTION START -->
<section class="home-grid">

    <div class="box-container">

        <div class="box">
            <?php
            if (!empty($conn)) {
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                /** @var TYPE_NAME $user_id */
                $select_profile->execute([$user_id]);
                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                    // Afficher le total des commentaires postés par cet user
                    $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
                    $count_user_comments->execute([$user_id]);
                    $total_user_comments = $count_user_comments->rowCount();

                    // Afficher le total des likes postés par cet user
                    $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
                    $count_user_likes->execute([$user_id]);
                    $total_user_likes = $count_user_likes->rowCount();
                    ?>
                    <p>bienvenue <?= $fetch_profile['name'] ?></p>
                    <p>total commentaires : <?= $total_user_comments ?></p>
                    <p>total avis : <?= $total_user_likes ?></p>
                    <a href="update.php">modifier profil</a>
                    <div class="flex-btn">
                        <a href="login.php" class="option-btn">se connecter</a>
                        <a href="register.php" class="option-btn">s'enregistrer</a>
                    </div>
                    <a href="user_logout.php" class="delete-btn" onclick="return confirm('Se déconnecter du site ?')">se déconnecter</a>
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

        <div class="box">
            <p>catégories</p>
            <div class="flex-box">
                <a href="category.php?category=nature" class="links">nature</a>
                <a href="category.php?category=voyage" class="links">voyage</a>
                <a href="category.php?category=news" class="links">news</a>
                <a href="category.php?category=education" class="links">education</a>
                <a href="category.php?category=gaming" class="links">gaming</a>
                <a href="category.php?category=sports" class="links">sports</a>
                <a href="category.php?category=business" class="links">business</a>
                <a href="category.php?category=mode" class="links">mode</a>
                <a href="category.php?category=design" class="links">design</a>
                <a href="category.php?category=personnel" class="links">personnel</a>
                <a href="all_category.php" class="btn" >tout voir</a>

            </div>
        </div>

        <div class="box">
            <p>auteurs</p>
            <div class="flex-box">
                <?php
                    // Afficher les auteurs des posts sans doublon et maxi 10
                    $select_authors = $conn->prepare("SELECT DISTINCT name FROM `admin` LIMIT 10");
                    $select_authors->execute();
                    if($select_authors->rowCount() > 0) {
                        while ($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)) {
                ?>
                            <a href="author_posts.php?author=<?= $fetch_authors['name'] ?>" class="links"><?=
                                $fetch_authors['name'] ?></a>
                <?php
                        }
                    } else {
                        echo '<p class="empty">Aucun auteur n\'a été trouvé !</p>';
                    }
                ?>
                <a href="authors.php" class="btn" >tout voir</a>

            </div>
        </div>

    </div>

</section>
<!-- HOME-GRID SECTION END -->

<!-- HOME POSTS SECTION START -->
<section class="posts-grid">
    <h1 class="heading">derniers articles</h1>

    <div class="box-container">
        <?php
        // Affiche les articles actifs dans la limite de 6 maxi
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? LIMIT 6");
        $select_posts->execute(['active']);
        if($select_posts->rowCount() > 0) {
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                $post_id = $fetch_posts['id'];
                // Affiche les commentaires du post
                $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
                $count_post_comments->execute([$post_id]);
                $total_post_comments = $count_post_comments->rowCount();
                // Affiche les likes du post
                $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
                $count_post_likes->execute([$post_id]);
                $total_post_likes = $count_post_likes->rowCount();
        ?>
                <form action="" method="post" class="box">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id'] ?>">
                    <div class="admin">
                        <i class="fas fa-user"></i>
                        <div class="admin-info">
                            <a href="author_posts.php?author=<?= $fetch_posts['name'] ?>"><?= $fetch_posts['name'] ?></a>
                            <div><?= date('d-m-Y H:i', strtotime($fetch_posts['date'])) ?></div>
                        </div>
                    </div>
                    <?php
                        if($fetch_posts['image'] != '') {
                    ?>
                            <img src="uploaded_img/<?= $fetch_posts['image'] ?>" class="image" alt="">
                    <?php
                        }
                    ?>
                    <div class="title"><?= $fetch_posts['title'] ?></div>
                    <div class="content"><?= $fetch_posts['content'] ?></div>
                    <a href="view_post.php?post_id=<?= $post_id ?>" class="inline-btn">Voir plus</a>
                    <a href="category.php?category=<?= $fetch_posts['category'] ?>" class="category">
                        <i class="fas fa-tag"></i>
                        <span><?= $fetch_posts['category'] ?></span>
                    </a>
                    <div class="icons">
                        <a href="view_post.php?post_id=<?= $post_id ?>" >
                            <i class="fas fa-comment"></i>
                            <span><?= $total_post_comments ?></span>
                        </a>
                        <button type="submit" name="like_post">
                            <i class="fas fa-heart"></i>
                            <span><?= $total_post_likes ?></span>
                        </button>
                    </div>
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">Aucun article n\'a été trouvé !</p>';
        }
        ?>
    </div>

    <div style="margin-top: 2rem; text-align: center">
        <a href="posts.php" class="inline-btn">Voir tous les articles</a>
    </div>
</section>
<!-- HOME POSTS SECTION END -->


<!-- FOOTER SECTION START -->
<?php include 'components/footer.php'; ?>
<!-- FOOTER SECTION END -->


<script src="js/script.js"></script>
</body>
</html>