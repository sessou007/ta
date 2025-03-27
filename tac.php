<?php
session_start(); // Démarrer la session pour accéder aux variables de session



// Inclure la connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_tache';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit();
}
if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, redirection vers la page de connexion
    header("Location: login.php");
    exit; // Terminez le script après la redirection
}

// Vérifiez si l'utilisateur est connecté et récupérez son ID
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

include 'config.php';
$userId = $_SESSION['user_id']; // Récupérer l'ID utilisateur depuis la session

// Récupérer les événements non supprimés de la table 'evenement'
$query = "SELECT id, titre, description, debut, fin, status, button_disabled 
          FROM evenement 
          WHERE DELETED = 0 AND user_id = :user_id"; // Filtrage des événements non supprimés et appartenant à l'utilisateur
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" crossorigin="anonymous"></script>
    
</head>
<header>
<?php include('respons.php'); ?>
</header>

<body>

    <!-- Contenu de la page -->
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
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td data-label="Titre"><?php echo htmlspecialchars($row['titre'] ?? ''); ?></td>
                            <td data-label="Description"><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                            <td data-label="Date de Début"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['debut']))); ?></td>
                            <td data-label="Date de Fin"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['fin']))); ?></td>
                            <td data-label="Statut">
                                <?php if (isset($row['status'])): ?>
                                    <?php if ($row['status'] === 'effectué'): ?>
                                        <span class="text-success" style="display: block; background-color: #d4edda; padding: 10px; border-radius: 5px;">Effectué</span>
                                    <?php elseif ($row['status'] === 'non_effectué'): ?>
                                        <span class="text-danger" style="display: block; background-color: #f8d7da; padding: 10px; border-radius: 5px;">Non Effectué</span>
                                    <?php else: ?>
                                        <button class="btn btn-success btn-sm" id="effectueButton_<?php echo $row['id']; ?>" onclick="updateStatus(<?php echo $row['id']; ?>, 'effectue')" <?php echo $row['button_disabled'] ? 'disabled' : ''; ?>>Effectué</button>
                                        <button class="btn btn-danger btn-sm" id="nonEffectueButton_<?php echo $row['id']; ?>" onclick="requestReason(<?php echo $row['id']; ?>, 'non_effectue')" <?php echo $row['button_disabled'] ? 'disabled' : ''; ?>>Non Effectué</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateStatus(id, status, reason = null) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous allez marquer cette tâche comme " + (status === 'effectue' ? "effectuée" : "non effectuée") + ".",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, continuer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'update_status.php',
                        type: 'POST',
                        data: { id: id, status: status, reason: reason },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                Swal.fire(
                                    'Mis à jour !',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    updateButtonDisplay(id, status);
                                });
                            } else {
                                Swal.fire('Erreur', "Erreur lors de la mise à jour du statut: " + data.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Erreur', "Erreur lors de la mise à jour du statut. Veuillez réessayer.", 'error');
                        }
                    });
                }
            });
        }

        function updateButtonDisplay(id, status) {
            $('#effectueButton_' + id).hide();
            $('#nonEffectueButton_' + id).hide();
            if (status === 'effectue') {
                $('#effectueButton_' + id).after('<span class="text-success" style="display: block; background-color: #d4edda; padding: 10px; border-radius: 5px;">Effectué</span>');
            } else {
                $('#nonEffectueButton_' + id).after('<span class="text-danger" style="display: block; background-color: #f8d7da; padding: 10px; border-radius: 5px;">Non Effectué</span>');
            }
        }

        function requestReason(id) {
            Swal.fire({
                title: 'Raison de non-effectuation',
                input: 'textarea',
                inputPlaceholder: "Veuillez entrer la raison...",
                showCancelButton: true,
                confirmButtonText: 'Envoyer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const reason = result.value.trim();
                    if (reason !== "") {
                        updateStatus(id, 'non effectué', reason);
                    } else {
                        Swal.fire('Erreur', "La raison ne peut pas être vide.", 'error');
                    }
                }
            });
        }
    </script>
</body>
<style>
        /* Style général */
        body {
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Contenu principal */
        .container {
            margin-top: 5rem;
            padding: 20px;
        }

        h1.text-center {
            font-size: 2rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Tableau */
        .table thead th.date-col, .table tbody td.date-col,
        .table thead th.status-col, .table tbody td.status-col {
            width: auto;
            text-align: center;
        }

        /* Boutons */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-success:hover, .btn-danger:hover {
            opacity: 0.8;
        }

        /* Styles Responsive */
        @media (max-width: 768px) {
            .container {
                margin-top: 3rem; /* Réduire la marge pour les petits écrans */
                padding: 10px;
            }

            h1.text-center {
                font-size: 1.5rem; /* Taille de police plus petite pour les titres */
            }

            .table-responsive {
                overflow-x: auto; /* Permettre le défilement horizontal si nécessaire */
            }

            .table thead {
                display: none; /* Masquer l'en-tête du tableau sur les petits écrans */
            }

            .table tbody tr {
                display: block;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .table tbody td {
                display: block;
                text-align: left; /* Aligner le contenu à gauche */
                padding: 10px;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label); /* Afficher les libellés des colonnes */
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
                color: #333;
            }

            .table tbody td.date-col,
            .table tbody td.status-col {
                text-align: left; /* Aligner les dates et statuts à gauche */
            }

            .btn-success, .btn-danger {
                width: 100%; /* Boutons pleine largeur */
                margin-bottom: 5px; /* Espace entre les boutons */
            }
        }

        @media (max-width: 480px) {
            .container {
                margin-top: 2rem;
                padding: 5px;
            }

            h1.text-center {
                font-size: 1.2rem; /* Taille de police encore plus petite pour les très petits écrans */
            }

            .table tbody td {
                padding: 8px; /* Réduire le padding pour les très petits écrans */
            }
        }
    </style>
</html>