<?php
session_start(); // Démarrer la session pour accéder aux variables de session
require 'config.php';

// Vérifiez si l'utilisateur est connecté et récupérez son ID
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

if (isset($_GET['user_id'])) {
    $userid = $_GET['user_id'];
}

$userId = $_SESSION['user_id']; // Récupérer l'ID utilisateur depuis la session

// Récupérer les événements non supprimés de la table 'evenement'
$sql = "SELECT * FROM evenement WHERE user_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $userid, PDO::PARAM_INT);
$stmt->execute();
$userevents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT id FROM postes WHERE poste_name = 'directeur'");
$stmt->execute();
$postv = $stmt->fetch(PDO::FETCH_ASSOC);

// Préparation de la requête pour récupérer les colonnes "statut" et "role"
$sql = "SELECT poste_id FROM users WHERE user_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $postid = $row['poste_id'];
    if ($postid == $postv['id']) {
        // Récupérer la direction et le département du directeur
        $sql = "SELECT department_id FROM users WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $postid, PDO::PARAM_INT);
        $stmt->execute();
        $director = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($director) {
            $sql = "SELECT * FROM users WHERE department_id = :department_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':department_id', $director['department_id'], PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

// Vérifier l'état de l'utilisateur
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT active FROM Users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['active'] == 0) {
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

    <style>
        .effectue {
            color: green;
        }
        .non-effectue {
            color: red;
        }
        .en-cours {
            color: orange;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<!-<?php include('respons.php'); ?><!-- Barre de navigation verticale -->
<nav class="sidebar">
    <!-- Contenu de la barre de navigation -->
</nav>

<div class="container mt-5">
    <h1 class="text-center">Liste des Tâches</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th class="date-col">Date de Début</th>
                    <th class="date-col">Date de Fin</th>
                    <th class="status-col">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userevents as $userevent): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($userevent['titre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($userevent['description'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($userevent['debut']))); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($userevent['fin']))); ?></td>
                        <td class="<?php
                            if ($userevent['status'] === 'effectué') {
                                echo 'effectue';
                            } elseif ($userevent['status'] === 'non effectué') {
                                echo 'non-effectue';
                            } elseif ($userevent['status'] === 'en cours') {
                                echo 'en-cours';
                            }
                        ?>">
                            <?php echo htmlspecialchars($userevent['status'] ?? ''); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateStatus(taskId, status) {
    // Demande de confirmation avant de changer le statut
    Swal.fire({
        title: 'Confirmer',
        text: 'Êtes-vous sûr de vouloir marquer cette tâche comme ' + (status === 'effectué' ? 'effectuée' : 'non effectuée') + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non'
    }).then((result) => {
        if (result.isConfirmed) {
            // Effectuer une requête AJAX pour mettre à jour le statut
            $.ajax({
                url: 'update_status.php', // Le fichier PHP qui traitera la mise à jour
                method: 'POST',
                data: { id: taskId, status: status },
                success: function(response) {
                    // Traitement de la réponse
                    Swal.fire('Succès!', 'Le statut de la tâche a été mis à jour.', 'success').then(() => {
                        location.reload(); // Recharger la page pour voir les changements
                    });
                },
                error: function() {
                    Swal.fire('Erreur!', 'Une erreur s\'est produite lors de la mise à jour du statut.', 'error');
                }
            });
        }
    });
}

function requestReason(taskId) {
    // Ouvrir une boîte de dialogue pour demander une raison
    Swal.fire({
        title: 'Raison non effectuée',
        input: 'textarea',
        inputPlaceholder: 'Entrez la raison...',
        showCancelButton: true,
        confirmButtonText: 'Soumettre',
        preConfirm: (reason) => {
            if (!reason) {
                Swal.showValidationMessage('Veuillez entrer une raison');
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Effectuer une requête AJAX pour soumettre la raison
            $.ajax({
                url: 'submit_reason.php', // Le fichier PHP qui traitera la raison
                method: 'POST',
                data: { id: taskId, reason: result.value },
                success: function(response) {
                    Swal.fire('Succès!', 'Votre raison a été soumise.', 'success').then(() => {
                        location.reload(); // Recharger la page pour voir les changements
                    });
                },
                error: function() {
                    Swal.fire('Erreur!', 'Une erreur s\'est produite lors de la soumission de la raison.', 'error');
                }
            });
        }
    });
}
</script>

</body>
</html>