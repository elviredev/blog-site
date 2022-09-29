<?php
@include '../components/connect.php';

session_start();

if(isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $pass = htmlentities(sha1($_POST['pass']));
    $cpass = htmlentities(sha1($_POST['cpass']));

    if (!empty($conn)) {
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? ");

        $select_admin->execute([$name]);

        if($select_admin->rowCount() > 0) {
            $messages[] = 'Ce pseudo existe déja !';
        } else {
            if ($pass != $cpass) {
                $messages[] = 'Le mot de passe ne correspond pas !';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin` (name, password) VALUES (?,?) ");
                $insert_admin->execute([$name, $cpass]);
                $messages[] = 'Nouvel administrateur enregistré !';
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
    <title>creer un compte admin</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<!-- ADMIN REGISTER SECTION START -->
<section class="form-container">
    <form action="" method="post">
        <h3>créer un compte</h3>
        <input type="text" class="box" name="name" placeholder="entrer votre pseudo" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" class="box" name="pass" placeholder="entrer votre mot de passe" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" class="box" name="cpass" placeholder="confirmer votre mot de passe" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" class="btn" name="submit" value="créer">
    </form>
</section>
<!-- ADMIN REGISTER SECTION END -->





<script src="../js/admin_script.js"></script>
</body>
</html>
