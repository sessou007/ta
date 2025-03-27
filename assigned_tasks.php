<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_tache';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les tâches assignées par l'utilisateur connecté
$sql = "
    SELECT e.id, e.titre, e.description, e.debut, e.fin, e.status, u.first_name, u.last_name 
    FROM task_assignments ta
    JOIN evenement e ON ta.task_id = e.id
    JOIN users u ON ta.user_id = u.user_id
    WHERE e.assigned_by = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<header>
<?php include('respons.php'); ?>
</header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches Assignées</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .effectue {
            color: green;
            font-weight: bold;
        }
        .non-effectue {
            color: red;
            font-weight: bold;
        }
        .en-cours {
            color: orange;
            font-weight: bold;
        }
        table {
            margin-top: 20px;
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Tâches Assignées</h1>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Statut</th>
                    <th>Assigné à</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['titre']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td><?php echo htmlspecialchars($task['debut']); ?></td>
                        <td><?php echo htmlspecialchars($task['fin']); ?></td>
                        <td class="<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                            <?php echo htmlspecialchars($task['status'] ?? ''); ?>
                        </td>
                        <td><?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>