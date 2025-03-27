<?php
session_start();
require 'config.php'; // Configuration de la base de données

if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, redirection vers la page de connexion
    header("Location: login.php");
    exit; // Terminez le script après la redirection
}

$notification = '';

// Vérification de l'état du compte à chaque chargement de page
$stmt = $pdo->prepare("SELECT active FROM Users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des départements et postes depuis la base de données
$departments = $pdo->query("SELECT department_id, department_name FROM departments")->fetchAll(PDO::FETCH_ASSOC);
$postes = $pdo->query("SELECT id, poste_name FROM postes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Logique de gestion des utilisateurs
    if (isset($_POST['activate_user_id'])) {
        $activate_user_id = $_POST['activate_user_id'];
        $stmt = $pdo->prepare("UPDATE Users SET active = 1 WHERE user_id = ?");
        $stmt->execute([$activate_user_id]);
        $notification = "Compte activé avec succès.";
    }

    if (isset($_POST['deactivate_user_id'])) {
        $deactivate_user_id = $_POST['deactivate_user_id'];
        $stmt = $pdo->prepare("UPDATE Users SET active = 0 WHERE user_id = ?");
        $stmt->execute([$deactivate_user_id]);
        $notification = "Compte désactivé avec succès.";
    }

    if (isset($_POST['delete_user_id'])) {
        $delete_user_id = $_POST['delete_user_id'];
        $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->execute([$delete_user_id]);
        $notification = "Compte supprimé avec succès.";
    }
}

// Récupération des utilisateurs avec leurs départements
$users = $pdo->query("
    SELECT u.*, d.department_name 
    FROM Users u 
    LEFT JOIN departments d ON u.department_id = d.department_id
")->fetchAll(PDO::FETCH_ASSOC);

if (!$user || $user['active'] == 0) { 
    // Si le compte est désactivé ou introuvable
    session_destroy(); // Détruire la session
    header("Location: login.php?error=compte_desactive");
    exit;
}
// Gestion des ajouts de département et de poste
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['department_name']) && !empty($_POST['department_name'])) {
        $department_name = trim($_POST['department_name']);
        $stmt = $pdo->prepare("INSERT INTO departments (department_name) VALUES (?)");
        $stmt->execute([$department_name]);
        $notification = "Nouveau département ajouté avec succès.";
    }
    
    if (isset($_POST['poste_name']) && !empty($_POST['poste_name'])) {
        $poste_name = trim($_POST['poste_name']);
        $stmt = $pdo->prepare("INSERT INTO postes (poste_name) VALUES (?)");
        $stmt->execute([$poste_name]);
        $notification = "Nouveau poste ajouté avec succès.";
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['modify_poste_id']) && isset($_POST['new_poste_id'])) {
        $user_id = intval($_POST['modify_poste_id']);
        $new_poste_id = intval($_POST['new_poste_id']);

        $query = "UPDATE users SET poste_id = :poste_id WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':poste_id', $new_poste_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Poste mis à jour avec succès !'); window.location.href = 'home.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour du poste.');</script>";
        }
    }

    if (isset($_POST['modify_department_id']) && isset($_POST['new_department_id'])) {
        $user_id = intval($_POST['modify_department_id']);
        $new_department_id = intval($_POST['new_department_id']);

        $query = "UPDATE users SET department_id = :department_id WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':department_id', $new_department_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Direction mise à jour avec succès !'); window.location.href = 'home.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de la direction.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestion des utilisateurs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'response.php'; ?> <!-- Inclure la barre de navigation -->
</head>

<body>

    <div class="container mt-5">
        <h1 class="text-center">Gestion des utilisateurs</h1>
        <?php if ($notification): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($notification); ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <!-- Formulaire d'ajout de département -->
            <div class="col-md-6">
                <div class="form-container">
                    <h3>Ajouter une direction</h3>
                    <form method="post">
                        <div class="input-group">
                            <input type="text" name="department_name" class="form-control" placeholder="Nom de la direction" required>
                            <button type="submit" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulaire d'ajout de poste -->
            <div class="col-md-6">
                <div class="form-container">
                    <h3>Ajouter un poste</h3>
                    <form method="post">
                        <div class="input-group">
                            <input type="text" name="poste_name" class="form-control" placeholder="Nom du poste" required>
                            <button type="submit" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

        <!-- Table des utilisateurs -->
        <table class="table table-striped table-bordered mt-3">
            <thead class="thead-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Direction</th>
                    <th>Actif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="modify_poste_id" value="<?php echo $user['user_id']; ?>">
                            <select name="new_poste_id" class="form-control mr-2" required>
                                <?php foreach ($postes as $poste): ?>
                                    <option value="<?php echo $poste['id']; ?>" <?php echo ($user['poste_id'] == $poste['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($poste['poste_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Modifier le poste</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="modify_department_id" value="<?php echo $user['user_id']; ?>">
                            <select name="new_department_id" class="form-control mr-2" required>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo $department['department_id']; ?>" <?php echo ($user['department_id'] == $department['department_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($department['department_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Modifier Direction</button>
                        </form>
                    </td>
                    <td><?php echo ($user['active']) ? 'Oui' : 'Non'; ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <?php if ($user['active']): ?>
                                    <li>
                                        <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver ce compte ?');">
                                            <input type="hidden" name="deactivate_user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="dropdown-item bg-warning">Désactiver</button>
                                        </form>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir activer ce compte ?');">
                                            <input type="hidden" name="activate_user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="dropdown-item bg-success">Activer</button>
                                        </form>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');">
                                        <input type="hidden" name="delete_user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="dropdown-item bg-danger">Supprimer</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
</body>
<?php include 'footer.php'; ?> <!-- Inclure le pied de la page -->
</html>