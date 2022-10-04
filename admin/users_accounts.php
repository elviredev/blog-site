<?php
@include '../components/connect.php';

session_start();
$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
}

?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>comptes utilisateurs</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include '../components/admin_header.php'; ?>
<!-- HEADER SECTION END -->

<section class="accounts">
    <h1 class="heading">comptes utilisateurs</h1>
    <div class="box-container">
        <?php
        if (!empty($conn)) {
            $select_account = $conn->prepare("SELECT * FROM `users`");
        }
        $select_account->execute();
        if($select_account->rowCount() > 0) {
            while ($fetch_account = $select_account->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <p>id : <span><?= $fetch_account['id'] ?></span></p>
            <p>pseudo : <span><?= $fetch_account['name'] ?></span></p>
            <p>email : <span><?= $fetch_account['email'] ?></span></p>
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
