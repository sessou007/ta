<?php
session_start();

// Inclure la connexion à la base de données
include 'config.php'; // Vérifiez que le chemin est correct
include 'respons.php';

// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, redirection vers la page de connexion
    header("Location: login.php");
    exit; // Terminez le script après la redirection
}//Fonction pour obtenir les rapports basés sur la période choisie
function getRapportPeriode($periodeType)
{
    global $pdo; // Utilisation de la variable globale $pdo

    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        return []; // Retourne un tableau vide si l'utilisateur n'est pas connecté
    }

    $userId = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté

    // Construire la requête en fonction du type de période
    $query = "";
    switch ($periodeType) {
        case 'journalier':
            $query = "SELECT DISTINCT e.titre, e.description, e.debut, e.fin, e.status, e.raison
                      FROM evenement e
                      WHERE e.DELETED = 0 AND e.user_id = :user_id AND DATE(e.debut) = CURDATE()";
            break;
        case 'mensuel':
            $query = "SELECT DISTINCT e.titre, e.description, e.debut, e.fin, e.status, e.raison
                      FROM evenement e
                      WHERE e.DELETED = 0 AND e.user_id = :user_id 
                      AND YEAR(e.debut) = YEAR(CURDATE()) AND MONTH(e.debut) = MONTH(CURDATE())";
            break;
        case 'annuel':
            $query = "SELECT DISTINCT e.titre, e.description, e.debut, e.fin, e.status, e.raison
                      FROM evenement e
                      WHERE e.DELETED = 0 AND e.user_id = :user_id 
                      AND YEAR(e.debut) = YEAR(CURDATE())";
            break;
    }

    $stmt = $pdo->prepare($query); // Utilisez $pdo ici
    $stmt->bindParam(':user_id', $userId); // Lier l'ID de l'utilisateur
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Affichage du rapport sous forme de tableau
function afficherTableauRapport($periodeType)
{
    $rapportData = getRapportPeriode($periodeType);
    echo "<div id='rapport'>";
    echo "<h2 class='rapport-titre'>" . ucfirst($periodeType) . " Rapport</h2>";
    echo "<table class='tableau-rapport'> 
            <thead>
                <tr>
                    <th>Titre</th> 
                    <th>Description</th> 
                    <th>Date de Début</th> 
                    <th>Date de Fin</th> 
                    <th>Statut</th> 
                    <th>Raison</th>
                </tr>
            </thead>
            <tbody>";

    if (empty($rapportData)) {
        echo "<tr><td colspan='6' class='no-data'>Aucune tâche trouvée pour cette période.</td></tr>";
    } else {
        foreach ($rapportData as $row) {
            echo "<tr> 
                    <td>{$row['titre']}</td> 
                    <td>{$row['description']}</td> 
                    <td>" . date('d/m/Y H:i', strtotime($row['debut'])) . "</td> 
                    <td>" . ($row['fin'] ? date('d/m/Y H:i', strtotime($row['fin'])) : 'Non défini') . "</td> 
                    <td>{$row['status']}</td> 
                    <td>" . ($row['status'] === 'non effectué' ? $row['raison'] : '-') . "</td> 
                  </tr>";
        }
    }

    echo "</tbody></table>";
    echo "</div>"; // Fermeture du conteneur
}

// Traitement de la demande de rapport
$periodeType = $_POST['periode_type'] ?? 'mensuel';

// Affichage du formulaire de sélection
echo "<h1 class='form-title'>Générer le Rapport</h1>";
echo '<form method="POST" action="" class="form-rapport">
        <label for="periode_type">Sélectionner le type de période :</label>
        <select name="periode_type" id="periode_type" class="select-rapport">
            <option value="journalier"' . ($periodeType == 'journalier' ? ' selected' : '') . '>Journalier</option>
            <option value="mensuel"' . ($periodeType == 'mensuel' ? ' selected' : '') . '>Mensuel</option>
            <option value="annuel"' . ($periodeType == 'annuel' ? ' selected' : '') . '>Annuel</option>
        </select>
        <button type="submit" class="btn-submit">Générer le Rapport</button>
    </form>';

// Affichage du rapport
echo "<div id='rapport-content'>";
afficherTableauRapport($periodeType);
echo "</div>";

// Bouton d'impression
echo "<button class='btn-imprimer' onclick='imprimerSection(\"rapport-content\")'>Imprimer le Rapport</button>";
echo "</div>"; 


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT active FROM Users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

 //if (!$user || $user['active'] == 0) { 
    // Si le compte est désactivé ou introuvable
     //session_destroy(); // Détruire la session
     //header("Location: login.php?error=compte_desactive");
    // exit;
 //}

if (!$user) { 
    // Si le compte est désactivé ou introuvable
    session_destroy(); // Détruire la session
    header("Location: login.php?error=compte_desactive");
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    <!-- Inclure Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
   
  

<script>
    
    function imprimerSection() {
        var contenu = document.getElementById('rapport-content').innerHTML;
        var styles = `
            <style>
                @media print {
                    body {
                        font-family: 'Arial', sans-serif;
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    .tableau-rapport {
                        margin-left: auto;
                        margin-right: auto;
                        width: auto;
                        margin-top: 20px;
                        border-collapse: collapse;
                    }
                    .tableau-rapport th, .tableau-rapport td {
                        padding: 12px;
                        border: 1px solid #ddd;
                    }
                    .tableau-rapport th {
                        background-color: #0056b3;
                        color: #fff;
                    }
                    .no-data {
                        text-align: center;
                        color: red;
                    }
                }
            </style>
        `;
        
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = styles + contenu;

        window.print();

        document.body.innerHTML = originalContent;
    }
</script>

<!-- Styles CSS -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .form-title, .rapport-titre {
        text-align: center;
        margin-top: 20px;
        color: #0056b3;
    }

    .form-rapport {
        text-align: center;
        width: 50%;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
        padding: 20px;
        overflow-x: auto;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .select-rapport {
        width: 100%;
        padding: 8px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .btn-submit {
        background-color: #0056b3;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    .btn-submit:hover {
        background-color: #003f7f;
    }

    .tableau-rapport {
        margin-left: auto;
        margin-right: auto;
        width: auto;
        margin-top: 20px;
        border-collapse: collapse;
        text-align: center;
    }

    .tableau-rapport th, .tableau-rapport td {
        padding: 12px;
        border: 1px solid #ddd;
    }

    .tableau-rapport th {
        background-color: #0056b3;
        color: #fff;
    }

    .no-data {
        text-align: center;
        color: red;
    }

    .btn-imprimer {
        background-color: #28a745;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
        display: block;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-imprimer:hover {
        background-color: #218838;
    }

    #rapport-content {
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        overflow-x: auto;
    }

    
   

    
    
</style>
<?php include 'footer.php'; ?>
</body>
</html>