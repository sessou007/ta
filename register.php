<?php
session_start();
require 'config.php'; // Assurez-vous que ce fichier contient la connexion PDO à la base de données

// Récupération des départements
$departments = [];
$stmt = $pdo->query("SELECT department_id, department_name FROM departments");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row;
}

$posts = [];
$stmt1 = $pdo->query("SELECT id, poste_name FROM postes where poste_name !='supra_admin'");
while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $posts[] = $row;
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']); // Enlever les espaces
    $last_name = trim($_POST['last_name']); // Enlever les espaces
    $email = trim($_POST['email']); // Enlever les espaces
    $position = $_POST['position']; // Récupérer le poste
    $department_id = $_POST['department_id']; // Récupérer l'ID du département
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation des champs
    if (empty($first_name) || empty($last_name)) {
         // Message de succès
        echo '<script>alert("Le prénom et le nom ne peuvent pas être vides.")</script>';
    } elseif (preg_match('/^\s*$/', $first_name) || preg_match('/^\s*$/', $last_name)) {
         // Message de succès
         echo '<script>alert("Le prénom et le nom ne peuvent pas contenir uniquement des espaces.")</script>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
          // Message de succès
          echo '<script>alert("Veuillez entrer un email valide au format Gmail (ex: utilisateur@gmail.com).")</script>';
    } elseif ($password !== $confirm_password) {
        // Message de succès
        echo '<script>alert("Les mots de passe ne correspondent pas.")</script>';
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        // Message de succès
        echo '<script>alert("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.")</script>';
         
    } else {
        // Vérification de l'unicité de l'email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $email_count = $stmt->fetchColumn();

        if ($email_count > 0) {
             // Message de succès
        echo '<script>alert("Ce compte existe déjà avec cet email.")</script>';
        } else {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, poste_id, active, department_id) VALUES (?, ?, ?, ?, ?, 0, ?)");
            $stmt->execute([$first_name, $last_name, $email, $hashed_password, $position, $department_id]);
            // Message de succès
            echo '<script>
            alert("Compte créé avec succès. Veuillez attendre l\'activation par le supra admin.");
            window.location.href = "acceuil.php";
          </script>';
    exit; // Arrête l'exécution du script après l'affichage du message
    
 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Créer un compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('images/font.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 50%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .form-container {
            width: 65%;
            padding: 20px;
        }
        .image-container {
            width: 35%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 32px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .row {
            display: flex;
            justify-content: space-between;
        }
        .row .col-md-6 {
            width: 48%;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }
        button {
            width: 50%;
            padding: 10px;
            background-color: blue;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: blue;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            gap: 30px; /* Ajoute de l'espacement entre les boutons */
        }
        .action-buttons .btn {
            width: 48%;
            background-color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Formulaire -->
    <div class="form-container">
        <h1 style="font-family:italic">Créer votre compte</h1>
        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="last_name" placeholder="Nom" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="first_name" placeholder="Prénom" required>
                </div>
            </div>
            <input type="email" name="email" placeholder="Email" required>
            <div class="row">
                <div class="col-md-6">
                    <select name="department_id" required>
                        <option value="" disabled selected>Choisir une direction</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo htmlspecialchars($department['department_id']); ?>">
                                <?php echo htmlspecialchars($department['department_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <select name="position" required>
                        <option value="" disabled selected>Choisir un poste</option>
                        <?php foreach ($posts as $post): ?>
                            <option value="<?php echo htmlspecialchars($post['id']); ?>">
                                <?php echo htmlspecialchars($post['poste_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 password-container">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>
                <div class="col-md-6 password-container">
                    <input type="password" name="confirm_password" placeholder="Confirmer" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit">Créer un compte</button>
                <a href="acceuil.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>

    <!-- Image avec lien -->
    <div class="image-container">
        
            <img src="images/connexion.png" alt="Illustration d'inscription">
        </a>
    </div>
</div>

<script>
    function togglePassword(inputName) {
        const input = document.querySelector(`input[name="${inputName}"]`);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>

</body>
</html>
