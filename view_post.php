<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if(isset($_GET['post_id'])) {
    $get_id = $_GET['post_id'];
} else {
    $get_id = '';
}

@include 'components/like_post.php';

// Ajouter un avis
if(isset($_POST['add_comment'])) {
    $admin_id = htmlentities($_POST['admin_id']);
    $user_name = htmlentities($_POST['user_name']);
    $comment = htmlentities($_POST['comment']);

    if(!empty($conn)) {
        $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ? AND admin_id = ? AND user_id = ? AND user_name = ? AND comment = ?");
        $verify_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);

        if($verify_comment->rowCount() > 0) {
            $messages[] = 'Avis déja ajouté !';
        } else {
            $insert_comment = $conn->prepare("INSERT INTO `comments` (post_id, admin_id, user_id, user_name, comment) VALUES (?,?,?,?,?)");
            $insert_comment->execute([$get_id, $admin_id, $user_id, $user_name, $comment]);
            $messages[] = 'Nouvel avis ajouté !';
        }
    }
}

// Modifier un commentaire
if(isset($_POST['edit_comment'])) {
    $edit_comment_id = htmlentities($_POST['edit_comment_id']);
    $edit_comment_box = htmlentities($_POST['edit_comment_box']);

    $verify_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ? ");
    $verify_edit_comment->execute([$edit_comment_id, $edit_comment_box]);

    if($verify_edit_comment->rowCount() > 0) {
        $messages[] = 'Commentaire déja ajouté !';
    } else {
        $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
        $update_comment->execute([$edit_comment_box, $edit_comment_id]);
        $messages[] = 'L\' avis a été modifié !';
    }
}

// Supprimer un commentaire
if(isset($_POST['delete_comment'])) {
    $delete_comment_id = htmlentities($_POST['comment_id']);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->execute([$delete_comment_id]);
    $messages[] = 'L\' avis a été supprimé !';
}



/** @var TYPE_NAME $fetch_profile */

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>lire article</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include 'components/user_header.php' ?>
<!-- HEADER SECTION END -->

<!-- Open Edit box (modifier un commentaire) -->
<?php
if(isset($_POST['open_edit_box'])) {
    $comment_id = htmlentities($_POST['comment_id']);
?>
<!-- EDIT COMMENT SECTION START -->
<section class="edit-comment-box" style="padding-bottom: 0;">
    <?php
        // Afficher le commentaire depuis son id (récupéré en input:hidden)
        $select_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
        $select_edit_comment->execute([$comment_id]);
        $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
    ?>
    <form action="" method="post">
    <h3>Modifier votre avis</h3>

        <input type="hidden" name="edit_comment_id" value="<?= $fetch_edit_comment['id'] ?>">
        <textarea name="edit_comment_box" placeholder="Ecrire votre avis" maxlength="1000" cols="30" rows="10"
                  class="comment-box" required><?= $fetch_edit_comment['comment'] ?></textarea>
        <input type="submit" name="edit_comment" class="inline-btn" value="Modifier">
        <a href="view_post.php?post_id=<?= $get_id ?>" class="inline-option-btn">Annuler</a>
    </form>
</section>
<?php
}
?>
<!-- EDIT COMMENT SECTION END -->

<!-- READ POST SECTION START -->
<section class="read-post">
    <h1 class="heading">Lire cet article</h1>

        <?php
        // Affiche les articles de la recherche via le titre ou la catégory
        $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND status = ?");
        $select_posts->execute([$get_id, 'active']);
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

                // Affiche les likes du post et de l'user
                $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
                $confirm_likes->execute([$user_id, $post_id]);
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
                    <div class="icons">
                        <div>
                            <i class="fas fa-comment"></i>
                            <span><?= $total_post_comments ?></span>
                        </div>

                        <button type="submit" name="like_post">
                            <i class="fas fa-heart" style="<?php if($confirm_likes->rowCount() > 0){echo 'color:var(--red)';} ?>"></i>
                            <span><?= $total_post_likes ?></span>
                        </button>
                    </div>
                    <a href="category.php?category=<?= $fetch_posts['category'] ?>" class="category">
                        <i class="fas fa-tag"></i>
                        <span><?= $fetch_posts['category'] ?></span>
                    </a>
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">Aucun article n\'a été trouvé !</p>';
        }

        ?>

</section>
<!-- READ POST SECTION END -->

<!-- COMMENTS SECTION START -->
<section class="comments" style="padding-top: 0">

    <p class="comment-title">Ajouter un avis</p>

    <?php
        // Si user authentifié
        if($user_id != '') {
            // Afficher le post via son ID récupéré en GET et son statut
            $select_admin_id = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND status = ?");
            $select_admin_id->execute([$get_id, 'active']);
            // Récupérer les champs de la table 'posts' pour pouvoir afficher l'admin_id dans le form (champ hidden)
            $fetch_admin_id = $select_admin_id->fetch(PDO::FETCH_ASSOC);
    ?>
    <form action="" method="post" class="add-comment">
        <input type="hidden" name="admin_id" value="<?= $fetch_admin_id['admin_id'] ?>">
        <input type="hidden" name="user_name" value="<?= $fetch_profile['name'] ?>">
        <p>
            <i class="fas fa-user"></i>
            <a href="update.php"><?= $fetch_profile['name'] ?></a>
        </p>
        <textarea name="comment" id="comment" maxlength="1000" cols="30" rows="10" class="comment-box" placeholder="Ecrivez votre avis..." required></textarea>
        <input type="submit" name="add_comment" value="Ajouter un avis" class="inline-btn">
    </form>
    <?php
        } else {
    ?>
    <div class="add-comment">
        <p>Connectez-vous pour ajouter ou modifier un avis</p>
        <div class="flex-btn">
            <a href="login.php" class="inline-option-btn">se connecter</a>
            <a href="register.php" class="inline-option-btn">s'inscrire</a>
        </div>
    </div>
    <?php
        }
    ?>

    <p class="comment-title">Avis utilisateurs</p>

    <div class="show-comments">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
        $select_comments->execute([$get_id]);
        if($select_comments->rowCount() > 0) {
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="user-comments" <?php if($fetch_comments['user_id'] == $user_id) {echo 'style="order:-1;"';}; ?>>
            <div class="user">
                <i class="fas fa-user"></i>
                <div class="user-info">
                    <p><?= $fetch_comments['user_name'] ?></p>
                    <div><?= $fetch_comments['date'] ?></div>
                </div>
            </div>
            <div class="comment-box" <?php if($fetch_comments['user_id'] == $user_id) {echo 'style="color:var(--white); background:var(--black)
            "';}; ?>><?=
                $fetch_comments['comment'] ?></div>
            <?php
                if($fetch_comments['user_id'] == $user_id) {
            ?>
                    <form action="" method="post" class="flex-btn">
                        <input type="hidden" name="comment_id" value="<?= $fetch_comments['id'] ?>">
                        <input type="submit" name="open_edit_box" value="Modifier avis"
                               class="inline-option-btn">
                        <input type="submit" name="delete_comment" value="Supprimer avis"
                               class="inline-delete-btn" onclick="return confirm('Supprimer cet avis ?');">
                    </form>
            <?php
                }
            ?>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">Pas d\' avis ajoutés pour l\'instant !</p>';
        }
        ?>
    </div>


</section>
<!-- COMMENTS SECTION END -->

<!-- FOOTER SECTION START -->
<?php include 'components/footer.php'; ?>
<!-- FOOTER SECTION END -->



<script src="./js/script.js"></script>
</body>
</html>
