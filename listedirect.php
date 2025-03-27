<?php
session_start();
require 'config.php'; // Configuration de la base de données

if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, redirection vers la page de connexion
    header("Location: login.php");
    exit; // Terminez le script après la redirection
}

// Vérification de la connexion
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT active FROM Users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) { 
    // Si le compte est désactivé ou introuvable
    session_destroy(); // Détruire la session
    header("Location: login.php?error=compte_desactive");
    exit;
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit();
}

// Vérifiez si l'utilisateur est connecté et récupérez son ID
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

$stmt = $pdo->prepare("SELECT id FROM postes WHERE poste_name = 'directeur'");
$stmt->execute();
$postv = $stmt->fetch(PDO::FETCH_ASSOC);

$userId = $_SESSION['user_id']; // Récupérer l'ID utilisateur depuis la session

$isDirector = $_SESSION['poste_id'] === $postv['id']; // Vérifiez si l'utilisateur est directeur

if ($isDirector) {
    // Récupérer la direction et le département du directeur
    $stmt = $conn->prepare("SELECT department_id FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($director) {
        $departmentId = $director['department_id'];
        $stmt = $pdo->prepare("SELECT id FROM postes WHERE poste_name = 'directeur'");
        $stmt->execute();
        $postdr = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT id FROM postes WHERE poste_name = 'supra_admin'");
        $stmt->execute();
        $postsup = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupérer uniquement les chefs de service et sous-chefs de service dans la même direction et département
        $requete = "SELECT * FROM users WHERE department_id = ? AND poste_id NOT IN (?, ?)";
        $stmt = $conn->prepare($requete);
        $stmt->execute([$departmentId, $postdr['id'], $postsup['id']]);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $utilisateurs = []; // Aucun utilisateur trouvé
    }
} else {
    // Si ce n'est pas un directeur, ne rien faire ou afficher un message d'erreur
    $utilisateurs = []; // Aucun utilisateur à afficher
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers votre fichier CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery pour l'autocomplétion -->
</head>
<body>
<?php include 'respons.php'; ?> <!-- Inclure la barre de navigation -->

<div class="main-content">
    <h2>Liste des Chefs de Service et Sous-Chefs de Service</h2>

    <div class="button-group">
        <!-- Boutons ici -->
    </div>

    <table>
        <thead>
            <tr>
                <th>Identifiant</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): 
                $psts = $utilisateur['poste_id'];
                $requete = "SELECT poste_name FROM postes WHERE id = ?";
                $stmt = $conn->prepare($requete);
                $stmt->execute([$psts]);
                $pst = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
                <tr>
                    <td data-label="Identifiant"><?php echo htmlspecialchars($utilisateur['user_id'] ?? ''); ?></td>
                    <td data-label="Nom"><?php echo htmlspecialchars($utilisateur['last_name'] ?? ''); ?></td>
                    <td data-label="Prénom"><?php echo htmlspecialchars($utilisateur['first_name'] ?? ''); ?></td>
                    <td data-label="Email"><?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></td>
                    <td data-label="Rôle"><?php echo ($pst['poste_name'] ?? ''); ?></td>
                    <td data-label="Actions">
                        <a href="voir.php?user_id=<?php echo urlencode($utilisateur['user_id']); ?>" class="btn btn-dashboard" style=" background-color: #007bff;"></style>Voir le Tableau de Bord</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    /* Styles de base */
    
    .button-group {
        margin-bottom: 20px; /* Espace au-dessous des boutons */
    }

    .btn {
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        background-color: #007bff; /* Couleur bleue */
        margin-right: 10px; /* Espace entre les boutons */
    }

    .btn-dashboard {
        background-color: #17a2b8; /* Couleur différente pour le tableau de bord */
    }

    table {
        width: 1190px;
        border-collapse: collapse;
        margin-top: 30px;
      
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        
    }

    th {
        background-color: #f2f2f2;
    }

    a {
        text-decoration: none;
        color: #fff;
    }

    /* Styles Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0; /* Supprimer la marge pour les petits écrans */
            padding: 10px;
        }

        .btn {
            display: block;
            width: 100%;
            margin-bottom: 10px; /* Espace entre les boutons */
        }

        table, thead, tbody, th, td, tr {
            display: block;
        }

        th {
            display: none; /* Masquer les en-têtes de tableau sur mobile */
        }

        tr {
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }

        td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: left; /* Aligner le contenu à gauche */
        }

        td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }

        .btn-dashboard {
            width: auto;
            display: inline-block;
        }
    }

    @media (max-width: 480px) {
        #search {
            width: 100%;
            max-width: none;
        }

        .btn-dashboard {
            width: 100%;
            display: block;
            margin-top: 10px;
        }

        td {
            padding-left: 10px; /* Réduire l'espace pour les très petits écrans */
        }

        td::before {
            position: static;
            display: block;
            width: 100%;
            padding-right: 0;
            margin-bottom: 5px;
            font-weight: bold;
        }
    }
</style>

<?php include 'footer.php'; ?>
</body>
</html>