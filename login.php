<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['submit'])) {
    $email = htmlentities($_POST['email']);
    $pass = htmlentities(sha1($_POST['pass']));

    if(!empty($conn)) {
        // Afficher le user correspondant à l'email + mdp saisis dans le form
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
        $select_user->execute([$email, $pass]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);
        // Si user existe, le renvoyer vers la homepage
        if($select_user->rowCount() > 0) {
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php');
        } else {
            $messages[] = 'Email ou mot de passe incorrect !';
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
    <title>se connecter</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER SECTION START -->
<?php include 'components/user_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="form-container">
    <form action="" method="post">
        <h3>Se connecter</h3>
        <input type="email" name="email" maxlength="50" placeholder="Entrer votre email" class="box" required>
        <input type="password" name="pass" maxlength="50" placeholder="Entrer votre mot de passe" class="box" required>
        <input type="submit" name="submit" value="Se connecter" class="btn">
    </form>
</section>


<!-- FOOTER SECTION START -->
<?php include 'components/footer.php'; ?>
<!-- FOOTER SECTION END -->


<script src="js/script.js"></script>
</body>
</html>