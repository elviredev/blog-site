<?php
@include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}


?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>catégories</title>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- HEADER SECTION START -->
<?php include 'components/user_header.php' ?>
<!-- HEADER SECTION END -->

<section class="categories">
    <h1 class="heading">toutes les catégories</h1>

    <div class="box-container">

        <div class="box">
            <span>01</span>
            <a href="category.php?category=nature">nature</a>
        </div>
        <div class="box">
            <span>02</span>
            <a href="category.php?category=animaux">animaux</a>
        </div>
        <div class="box">
            <span>03</span>
            <a href="category.php?category=informatique">informatique</a>
        </div>
        <div class="box">
            <span>04</span>
            <a href="category.php?category=mode">mode</a>
        </div>
        <div class="box">
            <span>05</span>
            <a href="category.php?category=divertissement">divertissement</a>
        </div>
        <div class="box">
            <span>06</span>
            <a href="category.php?category=films">films</a>
        </div>
        <div class="box">
            <span>07</span>
            <a href="category.php?category=gaming">gaming</a>
        </div>
        <div class="box">
            <span>08</span>
            <a href="category.php?category=musique">musique</a>
        </div>
        <div class="box">
            <span>09</span>
            <a href="category.php?category=sports">sports</a>
        </div>
        <div class="box">
            <span>10</span>
            <a href="category.php?category=humour">humour</a>
        </div>
        <div class="box">
            <span>11</span>
            <a href="category.php?category=nourriture">nourriture</a>
        </div>
        <div class="box">
            <span>12</span>
            <a href="category.php?category=news">news</a>
        </div>
        <div class="box">
            <span>13</span>
            <a href="category.php?category=shopping">shopping</a>
        </div>
        <div class="box">
            <span>14</span>
            <a href="category.php?category=voyage">voyage</a>
        </div>
        <div class="box">
            <span>15</span>
            <a href="category.php?category=business">business</a>
        </div>
        <div class="box">
            <span>16</span>
            <a href="category.php?category=sante">santé</a>
        </div>
        <div class="box">
            <span>17</span>
            <a href="category.php?category=animations">animations</a>
        </div>

    </div>

</section>



<script src="./js/script.js"></script>
</body>
</html>
