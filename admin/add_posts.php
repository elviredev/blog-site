<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

/** @var TYPE_NAME $fetch_profile */

// Publier un article
if(isset($_POST['publish'])) {
    $name = htmlentities($_POST['name']);
    $title = htmlentities($_POST['title']);
    $content = htmlentities($_POST['content']);
    $category = htmlentities($_POST['category']);
    $status = 'active';

    $image = htmlentities($_FILES['image']['name']);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if(!empty($conn)) {
        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
        $select_image->execute([$image, $admin_id]);

        if(isset($image)) {
            if ($select_image->rowCount() > 0 AND $image != '') {
                $messages[] = 'Nom de l\'image déja existant';
            } elseif ($image_size > 2000000) {
                $messages[] = 'Taille de l\'image trop grande';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        } else {
            $image = '';
        }

        if ($select_image->rowCount() > 0 AND $image != '') {
            $messages[] = 'Merci de renommer votre image';
        } else {
            $insert_post = $conn->prepare("INSERT INTO `posts` (admin_id, name, title, content, category, image, status) VALUES (?,?,?,?,?,?,?)");
            $insert_post->execute([$admin_id, $name, $title, $content, $category, $image, $status]);
            $messages[] = 'Article publié !';
        }
    }
}

// Enregistrer un brouillon
if(isset($_POST['draft'])) {
    $name = htmlentities($_POST['name']);
    $title = htmlentities($_POST['title']);
    $content = htmlentities($_POST['content']);
    $category = htmlentities($_POST['category']);
    $status = 'desactive';

    $image = htmlentities($_FILES['image']['name']);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if(!empty($conn)) {
        $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
        $select_image->execute([$image, $admin_id]);

        if(isset($image)) {
            if ($select_image->rowCount() > 0 AND $image != '') {
                $messages[] = 'Nom de l\'image déja existant';
            } elseif ($image_size > 2000000) {
                $messages[] = 'Taille de l\'image trop grande';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
            }
        } else {
            $image = '';
        }

        if ($select_image->rowCount() > 0 AND $image != '') {
            $messages[] = 'Merci de renommer votre image';
        } else {
            $insert_post = $conn->prepare("INSERT INTO `posts` (admin_id, name, title, content, category, image, status) VALUES (?,?,?,?,?,?,?)");
            $insert_post->execute([$admin_id, $name, $title, $content, $category, $image, $status]);
            $messages[] = 'Brouillon enregistré !';
        }
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
    <title>ajout article</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="post-editor">
    <h1 class="heading">ajouter un article</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="name" value="<?= $fetch_profile ?>">
        <p>Titre de l'article <span>*</span></p>
        <input type="text" name="title" placeholder="ajouter un titre" maxlength="100" class="box" required>
        <p>Contenu de l'article <span>*</span></p>
        <textarea name="content" maxlength="10000" cols="30" rows="10" class="box" placeholder="entrer votre contenu..." required></textarea>
        <p>Catégorie de l'article <span>*</span></p>
        <select name="category" class="box" required>
            <option value="" disabled selected>-- choisir la catégorie</option>
            <option value="nature">nature</option>
            <option value="education">education</option>
            <option value="animaux">animaux</option>
            <option value="mode">mode</option>
            <option value="divertissement">divertissement</option>
            <option value="films">films</option>
            <option value="gaming">gaming</option>
            <option value="musique">musique</option>
            <option value="sports">sports</option>
            <option value="news">news</option>
            <option value="voyage">voyage</option>
            <option value="humour">humour</option>
            <option value="informatique">informatique</option>
            <option value="nourriture">nourriture</option>
            <option value="style de vie">style de vie</option>
            <option value="personnel">personnel</option>
            <option value="santé">santé</option>
            <option value="business">business</option>
            <option value="shopping">shopping</option>
            <option value="animations">animations</option>
        </select>
        <p>Image de l'article</p>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
        <div class="flex-btn">
            <input type="submit" name="publish" value="publier article" class="btn">
            <input type="submit" name="draft" value="enregistrer brouillon" class="option-btn">
        </div>
    </form>

</section>






<script src="../js/admin_script.js"></script>
</body>
</html>
