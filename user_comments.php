<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
}



// Modifier un commentaire
if(isset($_POST['edit_comment'])) {
    $edit_comment_id = htmlentities($_POST['edit_comment_id']);
    $edit_comment_box = htmlentities($_POST['edit_comment_box']);

    if (!empty($conn)) {
        $verify_edit_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ? ");
    }
    $verify_edit_comment->execute([$edit_comment_id, $edit_comment_box]);

    if($verify_edit_comment->rowCount() > 0) {
        $messages[] = 'Avis déja ajouté !';
    } else {
        $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
        $update_comment->execute([$edit_comment_box, $edit_comment_id]);
        $messages[] = 'L\'avis a été modifié !';
    }
}

// Supprimer un commentaire
if(isset($_POST['delete_comment'])) {
    $delete_comment_id = htmlentities($_POST['comment_id']);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->execute([$delete_comment_id]);
    $messages[] = 'L\'avis a été supprimé !';
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
            <a href="user_comments.php" class="inline-option-btn">Annuler</a>
        </form>
    </section>
    <?php
}
?>
<!-- EDIT COMMENT SECTION END -->

<!-- COMMENTS SECTION START -->
<section class="comments">

    <h1 class="heading">Vos avis</h1>

    <p class="comment-title">Vos avis sur ces articles</p>
    <div class="show-comments">
        <?php
        $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
        $select_comments->execute([$user_id]);
        if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
                ?>
                <div class="user-comments">
                    <?php
                    $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
                    // Récupérer l'id du post dans la table 'comments'
                    $select_posts->execute([$fetch_comments['post_id']]);
                    while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <div class="post-title"> Article : <span><?= $fetch_posts['title']; ?></span> <a href="view_post.php?post_id=<?= $fetch_posts['id']; ?>" >Voir l'article</a></div>
                        <?php
                    }
                    ?>
                    <div class="comment-box"><?= $fetch_comments['comment']; ?></div>
                    <form action="" method="POST" class="flex-btn">
                        <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
                        <button type="submit" class="inline-option-btn" name="open_edit_box">Modifier</button>
                        <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm
                        ('Supprimer cet avis?');">Supprimer</button>
                    </form>
                </div>
                <?php
            }
        }else{
            echo '<p class="empty">Pas de commentaires ajoutés pour l\'instant !</p>';
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
