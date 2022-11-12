<?php

$username = $_POST['username'];
$password = $_POST['password'];

session_start();
if(isset($username) && isset($password))
{
    // connexion à la base de données
    include_once('.inc.php');
    mysqli_query($conn,"set names utf8") or die (mysqli_connect_error()); //gestion de l'affichage des caractères spéciaux
    
    //On vérifie la connexion
    if(!$conn){
      die('Erreur : ' .mysqli_connect_error());
  }
    
    // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
    // pour éliminer toute attaque de type injection SQL et XSS
    //$username = mysql_real_escape_string($conn,htmlspecialchars($username)); 
    //$password = mysql_real_escape_string($conn,htmlspecialchars($password));

    if($username !== "" && $password !== "")
    {echo "mdp vide";
        // Récupère la clé de hachage
        $requete_hash = "SELECT password FROM authent_tutor where user_name = '$username'";
        $exec_requete_hash = mysqli_query($conn,$requete_hash);
        $reponse_hash      = mysqli_fetch_array($exec_requete_hash);
        if(!$exec_requete_hash){
            die('Erreur : ' .mysqli_error($conn));
        }

        //vérifie les infos de longin
        $requete = "SELECT count(*) FROM authent_tutor where 
              user_name = '$username'";
        $exec_requete = mysqli_query($conn,$requete);
        $reponse      = mysqli_fetch_array($exec_requete);
        $count = $reponse['count(*)'];
        $username = $username;

        if($count !=0 && password_verify($password,$reponse_hash[0])==true) // nom d'utilisateur et mot de passe corrects
        {echo "dans if";
            //récupère l'id-tutor
            $requete_id_tutor = "SELECT id_tutor FROM authent_tutor where 
            user_name = '$username' and password = '$reponse_hash[0]'";
            $exec_requete_id_tutor = mysqli_query($conn,$requete_id_tutor);
            $reponse_requete_id_tutor     = mysqli_fetch_array($exec_requete_id_tutor);
            $id_tutor = $reponse_requete_id_tutor['id_tutor'];

            $_SESSION['username'] = $username;
            $_SESSION['id_tutor'] = $id_tutor;
            header('Location: home_conn.php');        
        }
        else
        {
           header('Location: authent.php?erreur=1'); // utilisateur ou mot de passe incorrect
        }
    }
    else
    {
       header('Location: authent.php?erreur=2'); // utilisateur ou mot de passe vide
    }
}
else
{
   header('Location: authent.php');
}
mysqli_close($conn); // fermer la connexion
?>