<?php
@include '../components/connect.php';

session_start();

if(isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);
    $pass = htmlentities(sha1($_POST['pass']));

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);

    if($select_admin->rowCount() > 0) {
        // Récupère le champ id en BDD et on le met dans un tableau associatif
        $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
        // On remplit la session avec
        $_SESSION['admin_id'] = $fetch_admin_id['id'];
        // $messages[] = 'working';
        header('location:dashboard.php');
    } else {
        $messages[] = 'Pseudo ou mot de passe incorrect !';
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
    <title>connexion</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body style="padding-left: 0">
<!-- MESSAGE D'INFO -->
<?php
    if(isset($messages)) {
        foreach ($messages as $message) {
            echo '
            <div class="message">
                <span>'.$message.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove()"></i>
            </div>
            ';
        }
    }
?>

<!-- ADMIN LOGIN SECTION START -->
<section class="form-container">
    <form action="" method="post">
        <h3>se connecter</h3>
        <p>pseudo par défaut = <span>admin</span> & mdp = <span>111</span></p>
        <input type="text" class="box" name="name" placeholder="entrer votre pseudo" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')" required>
        <input type="password" class="box" name="pass" placeholder="entrer votre mot de passe" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')" required>
        <input type="submit" class="btn" name="submit" value="connexion">
    </form>
</section>
<!-- ADMIN LOGIN SECTION END -->





<script src="../js/admin_script.js"></script>
</body>
</html>
