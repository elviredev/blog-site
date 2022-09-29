<?php
@include '../components/connect.php';

session_start();

if (isset($_SESSION)) {
    $admin_id = $_SESSION['admin_id'];
}
if(!isset($admin_id)) {
    header('location:admin_login.php');
    // $_SESSION['admin_id']=1;
}

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>admin panel</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<!-- DASHBOARD SECTION START -->
<section class="dashboard">
    <h1 class="heading">tableau de bord</h1>
    <div class="box-container">
        <div class="box">
            <h3>Bienvenue</h3>
            <p><?php echo $fetch_profile ?></p>
            <a href="update_profile.php" class="btn">modifier profil</a>
        </div>

        <div class="box">
            <?php
                $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = :id");
                $select_posts->bindValue(":id", $admin_id, PDO::PARAM_INT);
                $select_posts->execute();
                $number_of_posts = $select_posts->rowCount();
            ?>
            <h3><?php echo $number_of_posts ?></h3>
            <p>articles ajoutés</p>
            <a href="add_posts.php" class="btn">ajouter nouvel article</a>
        </div>

        <div class="box">
            <?php
            $select_active_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $select_active_posts->execute([$admin_id, 'active']);
            $number_of_active_posts = $select_active_posts->rowCount();
            ?>
            <h3><?php echo $number_of_posts ?></h3>
            <p>articles actifs</p>
            <a href="view_posts.php" class="btn">voir les articles</a>
        </div>

        <div class="box">
            <?php
            $select_desactive_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND status = ?");
            $select_desactive_posts->execute([$admin_id, 'desactive']);
            $number_of_desactive_posts = $select_desactive_posts->rowCount();
            ?>
            <h3><?php echo $number_of_posts ?></h3>
            <p>articles non actifs</p>
            <a href="view_posts.php" class="btn">voir les articles</a>
        </div>

        <div class="box">
            <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $number_of_users = $select_users->rowCount();
            ?>
            <h3><?php echo $number_of_users ?></h3>
            <p>total utilisateurs</p>
            <a href="users_accounts.php" class="btn">voir les utilisateurs</a>
        </div>

        <div class="box">
            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin`");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount();
            ?>
            <h3><?php echo $number_of_admins ?></h3>
            <p>total administrateurs</p>
            <a href="admin_accounts.php" class="btn">voir les administrateurs</a>
        </div>

        <div class="box">
            <?php
            $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
            $select_comments->execute([$admin_id]);
            $number_of_comments = $select_comments->rowCount();
            ?>
            <h3><?php echo $number_of_comments ?></h3>
            <p>total commentaires</p>
            <a href="comments.php" class="btn">voir les commentaires</a>
        </div>

        <div class="box">
            <?php
            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE admin_id = ?");
            $select_likes->execute([$admin_id]);
            $number_of_likes = $select_likes->rowCount();
            ?>
            <h3><?php echo $number_of_likes ?></h3>
            <p>total avis</p>
            <a href="view_posts.php" class="btn">voir les articles</a>
        </div>

    </div>
</section>
<!-- DASHBOARD SECTION END -->





<script src="../js/admin_script.js"></script>
</body>
</html>
