<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

/** @var TYPE_NAME $fetch_profile */

// Si pas d'article à modifier
if(!isset($_GET['post_id'])) {
    header('location:view_posts.php');
} else {
    // Récupérer le post_id de l'article à modifier via la methode GET
    $get_id = $_GET['post_id'];
}

// Supprimer un article
if(isset($_POST['delete'])) {
    $delete_id = htmlentities($_POST['post_id']);

    if(!empty($conn)) {
        // Supprimer image
        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $select_image->execute([$delete_id]);
        // fetch retourne un booleen dans un tableau donc vérifier si le champs est définit sinon retourner une chaine vide
        $fetch_imageRes = $select_image->fetch(PDO::FETCH_ASSOC);
        $fetch_image = isset($fetch_imageRes['image']) ? $fetch_imageRes['image'] : '';
        if($fetch_image != '') {
            unlink('../uploaded_img/'.$fetch_image);
        }
        // Supprimer commentaires
        $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
        $delete_comments->execute([$delete_id]);
        // Supprimer likes
        $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE post_id = ?");
        $delete_likes->execute([$delete_id]);
        // Supprimer l'article
        $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
        $delete_post->execute([$delete_id]);

        $messages[] = 'Article supprimé !';
    }
}

// Supprimer un commentaire
if(isset($_POST['delete_comment'])) {
    $comment_id = htmlentities($_POST['comment_id']);
    $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
    $delete_comment->execute([$comment_id]);
    $messages[] = 'Commentaire supprimé !';
}

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
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<!-- SECTION READ POST START -->
<section class="read-post">
    <h1 class="heading">lire cet article</h1>
    <?php
    // Afficher le post à lire
    $select_post = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND admin_id = ?");
    $select_post->execute([$get_id, $admin_id]);
    // s'il y a des enregistrements en BDD
    if($select_post->rowCount() > 0) {
        // Tant qu'il y a des enregistrements, on récupère les champs du post
        while ($fetch_post = $select_post->fetch(PDO::FETCH_ASSOC)) {
            // recupère l'ID du post
            $post_id = $fetch_post['id'];

            // Affiche les commentaires de chaque post
            $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            // compte le nb total de commentaires pour le post
            $total_post_comments = $count_post_comments->rowCount();

            // Affiche les likes de chaque post
            $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            // compte le nb total de likes pour le post
            $total_post_likes = $count_post_likes->rowCount();
            ?>
            <form action="" method="post" class="box">
                <div class="status" style="background: <?php if($fetch_post['status'] == 'active'){echo 'limegreen';}else{echo 'coral';} ?>"><?= $fetch_post['status']?></div>
                <!-- Image start -->
                <?php
                if($fetch_post['image'] != '') {
                    ?>
                    <img src="../uploaded_img/<?= $fetch_post['image'] ?>" alt="<?= $fetch_post['image'] ?>" class="image">
                    <?php
                }
                ?>
                <!-- Image end -->
                <div class="post-title"><?= $fetch_post['title'] ?></div>
                <div class="post-content"><?= $fetch_post['content'] ?></div>
                <div class="icons">
                    <div>
                        <i class="fas fa-comment"></i>
                        <span><?= $total_post_comments ?></span>
                    </div>
                    <div>
                        <i class="fas fa-heart"></i>
                        <span><?= $total_post_likes ?></span>
                    </div>
                </div>
                <div class="flex-btn">
                    <a href="edit_post.php?post_id=<?= $post_id ?>" class="option-btn">Modifier</a>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Voulez-vous supprimer cet article ?')">supprimer</button>
                </div>
                <div class="post-category">
                    <i class="fas fa-tag"></i>
                    <span><?= $fetch_post['category'] ?></span>
                </div>
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">Pas d\'articles ajoutés pour l\'instant !</p>';
    }
    ?>
</section>
<!-- SECTION READ POST END -->

<!-- SECTION COMMENTS START -->
<section class="comments">
    <p class="comment-title">commentaires</p>

    <div class="box-container">
        <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
            $select_comments->execute([$get_id]);
            if($select_comments->rowCount() > 0) {
                while ($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="box">
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
<!-- SECTION COMMENTS END -->


<script src="../js/admin_script.js"></script>
</body>
</html>
