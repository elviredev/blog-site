<?php
@include '../components/connect.php';

session_start();
if (isset($_SESSION)) {
    $admin_id = $_SESSION['admin_id'];
}
if(!isset($admin_id)) {
    header('location:admin_login.php');
}

/** @var TYPE_NAME $fetch_profile */

if (isset($_POST['submit'])) {
    $name = htmlentities($_POST['name']);

    if(!empty($conn)) {
        // Si un pseudo est envoyé
        if (!empty($name)) {
            // Vérifier s'il existe en BDD, si oui message
            $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
            $select_name->execute([$name]);
            if ($select_name->rowCount() > 0) {
                $messages[] = 'Pseudo déjà pris !';
            } else { // Si le pseudo n'existe pas en BDD on peut le modifier
                $update_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
                $update_name->execute([$name, $admin_id]);
                $messages[] = 'Pseudo modifié !';
            }
        }

        // Correspond à un mdp crypté égal à rien du tout donc vide
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
        // Afficher le mdp présent en BDD
        $select_prev_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
        $select_prev_pass->execute([$admin_id]);
        // Récupérer ce mdp et le mettre dans un tableau associatif
        $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
        // Variabiliser ce mdp dans $prev_pass
        $prev_pass = $fetch_prev_pass['password'];
        // Crypter le old, new et c_pass saisie par l'utilisateur
        $old_pass = htmlentities(sha1($_POST['old_pass']));
        $new_pass = htmlentities(sha1($_POST['new_pass']));
        $c_pass = htmlentities(sha1($_POST['c_pass']));

        // Si old mdp saisi par utilisateur est != du mdp vide
        if ($old_pass != $empty_pass) {
            // Si old mdp saisi par utilisateur est != du mdp présent en BDD
            if ($old_pass != $prev_pass) {
                $messages[] = 'L\'ancien mot de passe ne correspond pas';
                // Sinon si le new mdp est != du mdp confirmé
            } elseif ($new_pass != $c_pass) {
                $messages[] = 'Le mot de passe confirmé ne correspond pas au nouveau mot de passe';
            } else {
                // Si le new mdp est != du mdp vide
                if ($new_pass != $empty_pass) {
                    // modifier le mdp en BDD
                    $update_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                    $update_pass->execute([$c_pass, $admin_id]);
                    $messages[] = 'Mot de passe modifié !';
                } else {
                    $messages[] = 'Merci d\'entrer un nouveau mot de passe !';
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
    <title>modifier profil</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<!-- UPDATE PROFILE SECTION START -->
<section class="form-container">
    <form action="" method="post">
        <h3>modifier le profil</h3>
        <input type="text" class="box" name="name" placeholder="<?= $fetch_profile ?>" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" class="box" name="old_pass" placeholder="entrer votre ancien mot de passe" maxlength="20"  oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" class="box" name="new_pass" placeholder="entrer votre nouveau mot de passe" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" class="box" name="c_pass" placeholder="confirmer votre nouveau mot de passe" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" class="btn" name="submit" value="modifier">
    </form>
</section>
<!-- UPDATE PROFILE SECTION END -->





<script src="../js/admin_script.js"></script>
</body>
</html>
