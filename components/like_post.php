<?php

if(isset($_POST['like_post'])) {

    /** @var TYPE_NAME $user_id */
    if($user_id != '') {
        // Récupèrerer ces données envoyées via le form en input hidden
        $post_id = htmlentities($_POST['post_id']);
        $admin_id = htmlentities($_POST['admin_id']);
        if(!empty($conn)) {
            // Afficher les likes de cet user pour ce post
            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
            $select_likes->execute([$post_id, $user_id]);
            // Si un like existe déja pour ce post, on le supprime
            if($select_likes->rowCount() > 0) {
                $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?");
                $remove_likes->execute([$post_id, $user_id]);
                $messages[] = 'Supprimé des likes !';
            } else {
                // Sinon, on ajoute le like en bdd
                $add_likes = $conn->prepare("INSERT INTO `likes` (user_id, admin_id, post_id) VALUES (?,?,?)");
                $add_likes->execute([$user_id, $admin_id, $post_id]);
                $messages[] = 'Ajouté aux likes !';
            }
        }
    } else {
        $messages[] = 'Merci de vous connecter.';
    }

}

