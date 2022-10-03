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

// Modifier l'article
if(isset($_POST['save'])) {
    if(!empty($conn)) {
        $title = htmlentities($_POST['title']);
        $content = htmlentities($_POST['content']);
        $category = htmlentities($_POST['category']);
        // $status = 'desactive';
        $status = htmlentities($_POST['status']);

        $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
        $update_post->execute([$title, $content, $category, $status, $get_id]);
        $messages[] = 'Article modifié !';

        // Modifier image
        $old_image = htmlentities($_POST['old_image']);
        $image = htmlentities($_FILES['image']['name']);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_img/' . $image;

        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
        $select_image->execute([$image, $admin_id]);

        if(!empty($image)) {
            if ($select_image->rowCount() > 0 AND $image != '') {
                $messages[] = 'Merci de renommer votre image !';
            } elseif ($image_size > 2000000) {
                $messages[] = 'Taille de l\'image trop grande !';
            } else {
                $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
                $update_image->execute([$image, $get_id]);
                move_uploaded_file($image_tmp_name, $image_folder);
                $messages[] = 'Image modifiée !';
                if($old_image != $image && $old_image != '') {
                    unlink('../uploaded_img/'.$old_image);
                }
            }
        } else {
            $image = '';
        }
    }
}

// Supprimer l'article
if(isset($_POST['delete'])) {
    $delete_id = htmlentities($_POST['post_id']);

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

    header('location:view_posts.php');
}

// Supprimer l'image de l'article à modifier
if(isset($_POST['delete_image'])) {
    $empty_image = '';
    // Afficher image du post à modifier
    $select_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
    $select_image->execute([$get_id]);
    // Récupérer le champ de l'image en BDD
    $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
    // Si le champ 'image' en BDD est différent de vide
    if($fetch_image['image'] != '') {
        // Supprimer l'image du dossier des images téléchargées
        unlink('../uploaded_img/'.$fetch_image['image']);
    }
    // Modifier l'image en BDD en envoyant l'empty_image donc égale à image vide
    $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
    $unset_image->execute([$empty_image, $get_id]);
    $messages[] = 'Image supprimée !';
}

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>modifier article</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="post-editor">
    <h1 class="heading">modifier un article</h1>

    <?php
    // Afficher le post de l'admin à modifier
    $select_post = $conn->prepare("SELECT * FROM `posts` WHERE id = ? AND admin_id = ?");
    $select_post->execute([$get_id, $admin_id]);
    // s'il y a au moins un enregistrement en BDD
    if($select_post->rowCount() > 0) {
        // Tant qu'il y a des enregistrements, on récupère les champs du post
        while ($fetch_post = $select_post->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?= $fetch_post['id'] ?>">
        <input type="hidden" name="old_image" value="<?= $fetch_post['image'] ?>">
        <input type="hidden" name="name" value="<?= $fetch_profile ?>">
        <p>Statut de l'article <span>*</span></p>
        <select name="status" class="box" required>
            <option value="<?= $fetch_post['status'] ?>" selected><?= $fetch_post['status'] ?></option>
            <option value="active">activé</option>
            <option value="desactive">desactivé</option>
        </select>
        <p>Titre de l'article <span>*</span></p>
        <input type="text" name="title" placeholder="ajouter un titre" maxlength="100" class="box" value="<?= $fetch_post['title'] ?>" required>
        <p>Contenu de l'article <span>*</span></p>
        <textarea name="content" maxlength="10000" cols="30" rows="10" class="box" placeholder="entrer votre contenu..." required><?= $fetch_post['content'] ?></textarea>
        <p>Catégorie de l'article <span>*</span></p>
        <select name="category" class="box" required>
            <option value="<?= $fetch_post['category'] ?>" selected><?= $fetch_post['category'] ?></option>
            <option value="nature">nature</option>
            <option value="education">education</option>
            <option value="pets and animals">animaux</option>
            <option value="technology">technologie</option>
            <option value="fashion">mode</option>
            <option value="entertainment">divertissement</option>
            <option value="movies and animations">films</option>
            <option value="gaming">jeu vidéo</option>
            <option value="music">musique</option>
            <option value="sports">sports</option>
            <option value="news">actualités</option>
            <option value="travel">voyage</option>
            <option value="comedy">humour</option>
            <option value="design and development">design et development</option>
            <option value="food and drinks">nourriture et boissons</option>
            <option value="lifestyle">style de vie</option>
            <option value="personal">personal</option>
            <option value="health and fitness">santé et fitness</option>
            <option value="business">affaires</option>
            <option value="shopping">shopping</option>
            <option value="animations">animations</option>
        </select>
        <p>Image de l'article</p>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
        <!-- Modifier l'image -->
        <?php
            if($fetch_post['image'] != '') {
        ?>
                <img src="../uploaded_img/<?= $fetch_post['image'] ?>" alt="<?= $fetch_post['image'] ?>" class="image">
                <input type="submit" name="delete_image" value="supprimer image" class="inline-delete-btn">
        <?php
            }
        ?>
        <div class="flex-btn">
            <input type="submit" name="save" value="enregistrer article" class="btn">
            <a href="view_posts.php" class="option-btn">Retour articles</a>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Voulez-vous supprimer cet article ?')">supprimer article</button>
        </div>
    </form>
    <?php
        }
    } else {
        echo '<p class="empty">Pas d\'articles ajoutés pour l\'instant !</p>';
    }
    ?>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
