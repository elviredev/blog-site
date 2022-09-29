<?php
if(isset($messages)){
    foreach($messages as $message){
        echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}
?>

<header class="header">
    <a href="../admin/dashboard.php" class="logo">Admin<span>Panel</span></a>

    <div class="profile">
        <?php
        if (!empty($conn)) {
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");

            if (isset($admin_id)) {
                $select_profile->execute([$admin_id]);
            }
            $fetch_profileRes = $select_profile->fetch(PDO::FETCH_ASSOC);
            // fetch retourne un booleen dans un tableau donc vérifier si le champs est définit sinon retourner une chaine vide
            $fetch_profile = isset($fetch_profileRes['name']) ? $fetch_profileRes['name'] : '';
        }
        ?>
        <p><?= $fetch_profile; ?></p>
        <a href="../admin/update_profile.php" class="btn">modifier profil</a>
    </div>
    <nav class="navbar">
        <a href="../admin/dashboard.php"><i class="fas fa-home"></i><span>accueil</span></a>
        <a href="../admin/add_posts.php"><i class="fas fa-pen"></i><span>ajout post</span></a>
        <a href="../admin/view_posts.php"><i class="fas fa-eye"></i><span>vue posts</span></a>
        <a href="../admin/admin_accounts.php"><i class="fas fa-user"></i><span>comptes</span></a>
        <a href="../components/admin_logout.php" onclick="return confirm('se déconnecter su site ?')"><i class="fas fa-right-from-bracket"></i><span style="color:var(--red);">déconnexion</span></a>
    </nav>

    <div class="flex-btn">
        <a href="../admin/admin_login.php" class="option-btn">connexion</a>
        <a href="../admin/admin_register.php" class="option-btn">s'enregistrer</a>
    </div>

</header>

<div id="menu-btn" class="fas fa-bars"></div>