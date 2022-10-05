<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

if (!empty($conn)) {
// Supprimer un commentaire
    if (isset($_POST['delete_comment'])) {
        $comment_id = htmlentities($_POST['comment_id']);
        $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
        $delete_comment->execute([$comment_id]);
        $messages[] = 'Commentaire supprimé !';
    }
}

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>commentaires</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="comments">
    <h1 class="heading">Tous les commentaires</h1>
    <p class="comment-title">commentaires</p>

    <div class="box-container">
        <?php

        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
        $select_comments->execute([$admin_id]);
        if($select_comments->rowCount() > 0) {
            while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <?php
                    // Récupérer le post_id dans la table `comments`
                    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
                    $select_posts->execute([$fetch_comments['post_id']]);
                    while ($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <div class="post-title">
                            <span>Article : </span><?= $fetch_post['title'] ?>
                            <a href="read_post.php?post_id=<?= $fetch_post['id'] ?>">Lire cet article</a>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="user">
                        <i class="fas fa-user"></i>
                        <div class="user-info">
                            <span><?= $fetch_comments['user_name'] ?></span>
                            <div><?= date('d-m-Y à H:i', strtotime($fetch_comments['date'])) ?></div>
                        </div>
                    </div>
                    <div class="text"><?= $fetch_comments['comment'] ?></div>
                    <!-- Formulaire pour supprimer le commentaire -->
                    <form action="" method="post" class="icons">
                        <input type="hidden" name="comment_id" value="<?= $fetch_comments['id'] ?>">
                        <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm
                    ('Voulez-vous supprimer ce commentaire ?')"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">Cet article n\'a pas encore de commentaire(s).</p>';
        }
        ?>
    </div>
</section>



<script src="../js/admin_script.js"></script>
</body>
</html>
