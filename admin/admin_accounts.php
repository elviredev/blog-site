<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

// Supprimer le compte admin
if(!empty($conn)) {
    if(isset($_POST['delete'])) {
        // Supprimer les images de l'admin
        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
        $select_image->execute([$admin_id]);
        // fetch retourne un booleen dans un tableau donc vérifier si le champs est définit sinon retourner une chaine vide
        $fetch_imageRes = $select_image->fetch(PDO::FETCH_ASSOC);
        $fetch_image = isset($fetch_imageRes['image']) ? $fetch_imageRes['image'] : '';
        if($fetch_image != '') {
            unlink('../uploaded_img/'.$fetch_image);
        }
        // Supprimer les posts de l'admin
        $delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
        $delete_posts->execute([$admin_id]);
        // Supprimer les commentaires de l'admin
        $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
        $delete_comments->execute([$admin_id]);
        // Supprimer les likes de l'admin
        $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
        $delete_likes->execute([$admin_id]);
        // Supprimer le compte de l'admin
        $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
        $delete_admin->execute([$admin_id]);
        header('location:../components/admin_logout.php');
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
    <title>comptes admin</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="accounts">
    <h1 class="heading">comptes administrateurs</h1>
    <div class="box-container">
        <div class="box" style="order: -2">
            <p>Enregistrer un nouvel admin</p>
            <a href="admin_register.php" class="option-btn">Créer compte</a>
        </div>
        <?php
            $select_account = $conn->prepare("SELECT * FROM `admin`");
            $select_account->execute();
            if($select_account->rowCount() > 0) {
                while ($fetch_account = $select_account->fetch(PDO::FETCH_ASSOC)) {
                    // Afficher les posts de l'admin
                    $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
                    // récupérer les posts de l'admin et voir son total de posts
                    $count_admin_posts->execute([$fetch_account['id']]);
                    $total_admin_posts = $count_admin_posts->rowCount();
        ?>
        <div class="box" style="<?php if($fetch_account['id'] == $admin_id){echo 'order:-1';} ?>">
            <p>id : <span><?= $fetch_account['id'] ?></span></p>
            <p>pseudo : <span><?= $fetch_account['name'] ?></span></p>
            <p>total articles : <span><?= $total_admin_posts ?></span></p>
            <?php
                // Si le compte récupéré est égal au compte de session, affichage boutons
                if($fetch_account['id'] == $admin_id) {
            ?>
                    <div class="flex-btn">
                        <a href="update_profile.php" class="option-btn">modifier</a>
                        <form action="" method="post">
                            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Voulez-vous supprimer ce compte ?')">supprimer</button>
                        </form>
                    </div>
            <?php
                }
            ?>
        </div>
        <?php
                }
            } else {
                echo ' <p class="empty">Aucun compte n\a été trouvé !</p>';
            }
        ?>
    </div>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
