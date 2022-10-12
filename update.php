<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
}

if (isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);

    if(!empty($conn)) {
        if(!empty($name)) {
            // Modifier le nom de l'user en bdd
            $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
            $messages[] = 'Votre nom a été modifié !';
        }

        if(!empty($email)) {
            // Afficher l'email de l'user
            $select_email = $conn->prepare("SELECT email FROM `users` WHERE email = ?");
            $select_email->execute([$email]);
            if($select_email->rowCount() > 0) {
                $messages[] = 'Cet email est déja pris !';
            } else {
                // Modifier l'email de l'user en bdd
                $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
                $update_email->execute([$email, $user_id]);
                $messages[] = 'Votre email a été modifié !';
            }
        }

        // Correspond à un mdp crypté égal à rien du tout donc vide
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
        // Afficher mdp actuel de l'user
        $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
        $select_prev_pass->execute([$user_id]);
        $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
        // Récupérer mdp actuel
        $prev_pass = $fetch_prev_pass['password'];
        // Récupérer l'ancien mdp, le new mdp et le confirm mdp saisis par le user dans le form et les chiffrer
        $old_pass = htmlentities(sha1($_POST['old_pass']));
        $new_pass = htmlentities(sha1($_POST['new_pass']));
        $c_pass = htmlentities(sha1($_POST['c_pass']));

        if($old_pass != $empty_pass) {
            // si ancien mdp saisi par le user est différent du mdp actuel à modifier
            if($old_pass != $prev_pass) {
                $messages[] = 'Votre ancien mot de passe n\'est pas bon !';
            } elseif ($new_pass != $c_pass) {
                $messages[] = 'Le mot de passe confirmé ne correspond pas au nouveau mot de passe !';
            } else {
                // Si new mdp est différent de vide
                if($new_pass != $empty_pass) {
                    // Modifier le mdp de l'user en bdd
                    $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                    $update_pass->execute([$c_pass, $user_id]);
                    $messages[] = 'Votre mot de passe a été modifié !';
                } else {
                    $messages[] = 'Merci d\'entrer un nouveau mot de passe !';
                }
            }
        }

    }
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
    <title>modifier profil</title>
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
        <h3>Modifier son profil</h3>
        <input type="text" name="name" maxlength="50" placeholder="<?= $fetch_profile['name'] ?>" class="box">
        <input type="email" name="email" maxlength="50" placeholder="<?= $fetch_profile['email'] ?>" class="box" >
        <input type="password" name="old_pass" maxlength="50" placeholder="Entrer votre ancien mot de passe" class="box">
        <input type="password" name="new_pass" maxlength="50" placeholder="Entrer votre nouveau mot de passe" class="box">
        <input type="password" name="c_pass" maxlength="50" placeholder="Confirmer votre nouveau mot de passe" class="box">
        <input type="submit" name="submit" value="Modifier" class="btn">
    </form>
</section>


<!-- FOOTER SECTION START -->
<?php include 'components/footer.php'; ?>
<!-- FOOTER SECTION END -->


<script src="js/script.js"></script>
</body>
</html>