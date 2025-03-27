<?php
// Appel au fichier connect
include('config.php');

// Préparer la requête
$requete = "SELECT * FROM departments ORDER BY department_id";

// Exécuter la requête
$reponse = $pdo->query($requete);

// Récupérer les données de la requête
$donnees = $reponse->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styletable.css">
    <title>LISTE-DEPARTEMENT</title>
</head>
<?php include('response.php'); ?>
<body>
    <h1>Gestion des directions</h1><br>
    
    <div class="cadre">
        <button><a href="home.php">Nouveau</a></button><br>
        <table>
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($donnees as $ligne) { ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['department_name']) ?></td>
                    <td>
                        <p class="bloc">
                            <a href="modifedepart.php?id=<?= $ligne['department_id'] ?>">
                                <img src="images/modif.jpg" alt="modifier">
                            </a>
                            <a href="suppredepart.php?id=<?= $ligne['department_id'] ?>"
                               onclick="return confirm('Voulez-vous supprimer ?')">
                                <img src="images/sup.jpg" alt="supprimer">
                            </a>
                        </p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>