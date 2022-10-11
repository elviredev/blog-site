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
    <title>recherche</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include 'components/user_header.php' ?>
<!-- HEADER SECTION END -->

<section class="posts-grid">
    <h1 class="heading">Résultats de recherche</h1>

    <div class="box-container">
        <?php
        if (isset($_POST['search_btn']) or isset($_POST['search_item'])) {
            $search_item = $_POST['search_item'];
            // Affiche les articles de la recherche via le titre ou la catégory
            if (!empty($conn)) {
                $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE title LIKE '%{$search_item}%' OR category LIKE '%{$search_item}' AND status = ? ");
            }
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
                echo '<p class="empty">Aucun résultat n\'a été trouvé !</p>';
            }
        } else {
            echo '<p class="empty">Rechercher un article !</p>';
        }
        ?>
    </div>

</section>





<script src="./js/script.js"></script>
</body>
</html>
