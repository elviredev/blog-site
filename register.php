<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $pass = htmlentities(sha1($_POST['pass']));
    $cpass = htmlentities(sha1($_POST['cpass']));

    if(!empty($conn)) {
        // Afficher le user correspondant à l'email saisi dans le form
        $select_users = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_users->execute([$email]);

        if($select_users->rowCount() > 0) {
            $messages[] = 'Cet email existe déja !';
        } else {
            if($pass != $cpass) {
                $messages[] = 'Le mot de passe ne correspond pas !';
            } else {
                $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password) VALUES (?,?,?)");
                $insert_user->execute([$name, $email, $cpass]);

                // Récupère le user enregistré avec son email et mdp
                $fetch_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
                $fetch_user->execute([$email, $cpass]);
                $row = $fetch_user->fetch(PDO::FETCH_ASSOC);
                // Si le user existe bien en base, ouvre une session pour lui et le renvoi sur la home page directement
                if($fetch_user->rowCount() > 0) {
                    $_SESSION['user_id'] = $row['id'];
                    header('location:home.php');
                }
            }
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
    <title>s'inscrire</title>
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
        <h3>Créer un compte</h3>
        <input type="text" name="name" maxlength="50" placeholder="Entrer votre pseudo" class="box" required>
        <input type="email" name="email" maxlength="50" placeholder="Entrer votre email" class="box" required>
        <input type="password" name="pass" maxlength="50" placeholder="Entrer votre mot de passe" class="box" required>
        <input type="password" name="cpass" maxlength="50" placeholder="Confirmer votre mot de passe" class="box" required>
        <input type="submit" name="submit" value="Créer un compte" class="btn">
    </form>
</section>


<!-- FOOTER SECTION START -->
<?php include 'components/footer.php'; ?>
<!-- FOOTER SECTION END -->


<script src="js/script.js"></script>
</body>
</html>