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
    <title>auteur articles</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include 'components/user_header.php' ?>
<!-- HEADER SECTION END -->

<section class="authors">
    <h1 class="heading">auteurs</h1>

    <div class="box-container">

        <?php
            if(!empty($conn)) {
                // Afficher tous les auteurs d'articles de la table 'admin'
                $select_authors = $conn->prepare("SELECT * FROM `admin`");
                $select_authors->execute();
                if($select_authors->rowCount() > 0) {
                    // tant qu'il y a un enregistrement on récupère les champs de la table 'admin'
                    while ($fetch_author = $select_authors->fetch(PDO::FETCH_ASSOC)) {
                        // Récupère le champ id
                        $author_id = $fetch_author['id'];

                        // Afficher tous les posts d'un admin ayant le sattut active
                        $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
                        $count_admin_posts->execute([$author_id, 'active']);
                        // récupérer le nb total d'articles d'un auteur
                        $total_admin_posts = $count_admin_posts->rowCount();

                        // Afficher tous les likes d'un admin
                        $count_admin_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ? ");
                        $count_admin_likes->execute([$author_id]);
                        // récupérer le nb total de likes d'un auteur
                        $total_admin_likes = $count_admin_likes->rowCount();

                        // Afficher tous les commentaires d'un admin
                        $count_admin_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ? ");
                        $count_admin_comments->execute([$author_id]);
                        // récupérer le nb total de commentaires d'un auteur
                        $total_admin_comments = $count_admin_comments->rowCount();
        ?>
        <div class="box">
            <p>auteur : <span><?= $fetch_author['name'] ?></span></p>
            <p>total articles : <span><?= $total_admin_posts ?></span></p>
            <p>total likes : <span><?= $total_admin_likes ?></span></p>
            <p>total commentaires : <span><?= $total_admin_comments ?></span></p>
            <a href="author_posts.php?author=<?= $fetch_author['name'] ?>" class="btn">voir les articles</a>
        </div>
        <?php
                    }
                } else {
                    echo '<p class="empty">Aucun auteur n\'a été trouvé !</p>';
                }
            }
        ?>

    </div>

</section>



<script src="./js/script.js"></script>
</body>
</html>
