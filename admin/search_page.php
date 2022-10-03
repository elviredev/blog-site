<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

// Supprimer un article suite à une recherche
if(isset($_POST['delete'])) {
    $delete_id = htmlentities($_POST['post_id']);

    if(!empty($conn)) {
        // Supprimer image
        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $select_image->execute([$delete_id]);
        $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
        if($fetch_image['image'] != '') {
            unlink('../uploaded_img/'.$fetch_image['image']);
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

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>recherche</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php' ?>
<!-- HEADER SECTION END -->

<section class="show-posts">
    <h1 class="heading">Rechercher</h1>

    <form action="search_page.php" method="post" class="search-form">
        <input type="text" name="search_item" placeholder="Rechercher un article..." maxlength="100" required>
        <button type="submit" class="fas fa-search" name="search_btn"></button>
    </form>

    <div class="box-container">
        <?php
        if(!empty($conn)) {
            // Si un mot est saisi par utilisateur ou formulaire soumis
            if(isset($_POST['search_item']) or isset($_POST['search_btn'])) {
                $search_item = $_POST['search_item'];
                // Afficher les posts de l'admin
                $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND title LIKE '%{$search_item}%'");
                $select_posts->execute([$admin_id]);
                // s'il y a des enregistrements en BDD
                if($select_posts->rowCount() > 0) {
                    // Tant qu'il y a des enregistrements, on récupère les champs des posts
                    while ($fetch_post = $select_posts->fetch(PDO::FETCH_ASSOC)) {
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
                            <input type="hidden" name="post_id" value="<?= $post_id ?>">
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
                            <a href="read_post.php?post_id=<?= $post_id ?>" class="btn">Voir article</a>
                        </form>
                        <?php
                    }
                } else {
                    echo '<p class="empty">Aucun article trouvé !</p>';
                }
            }
        }
        ?>

    </div>
</section>





<script src="../js/admin_script.js"></script>
</body>
</html>
