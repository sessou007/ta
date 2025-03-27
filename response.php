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
    <!-- Styles pour la navbar -->
    <style>
        body {
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .navbar-brand span {
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            color: white !important;
            padding: 10px 15px; /* Espacement des liens */
            transition: background-color 0.3s, color 0.3s; /* Animation au survol */
        }
        .navbar-nav .nav-link.active {
            color: #28a745 !important;
            background-color: rgba(255, 255, 255, 0.1); /* Fond légèrement visible pour l'élément actif */
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Fond au survol */
            color: #28a745 !important; /* Couleur au survol */
        }
        .dropdown-toggle {
            color: white;
        }
        .dropdown-menu {
            background-color: #343a40;
            max-width: 200px; /* Limite la largeur du menu déroulant */
            overflow: hidden; /* Empêche le débordement */
            white-space: nowrap; /* Empêche le texte de passer à la ligne */
        }
        .dropdown-item {
            color: white !important;
            display: flex; /* Aligne l'icône et le texte */
            align-items: center; /* Centre verticalement */
        }
        .dropdown-item i {
            margin-right: 10px; /* Espace entre l'icône et le texte */
        }
        .dropdown-item:hover {
            background-color: #28a745;
        }

        /* Ajustements pour les petits écrans */
        @media (max-width: 768px) {
            .navbar-nav {
                flex-direction: column; /* Affiche les liens en colonne sur les petits écrans */
                align-items: flex-start; /* Aligne les liens à gauche */
            }
            .navbar-nav .nav-link {
                padding: 10px 20px; /* Espacement des liens sur les petits écrans */
                width: 100%; /* Occupe toute la largeur */
            }
            .dropdown-menu {
                position: static; /* Affiche le menu déroulant en mode statique sur les petits écrans */
                width: 100%; /* Occupe toute la largeur */
                max-width: 100%; /* Limite la largeur à 100% */
            }
        }
    </style>
</head>
<body>
    <!-- Navbar intégrée -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Logo et Nom -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/armo.png" alt="Armoirie du Bénin" class="armoirie" />
                <span class="fw-bold">Gestion des Tâches</span>
            </a>

            <!-- Bouton menu burger (Responsive) -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu de la navbar -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Liens principaux -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Tableau de Bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listdepart.php">Direction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listposte.php">Postes</a>
                    </li>
                </ul>

                <!-- Icône de connexion avec menu déroulant -->
                <div class="dropdown">
                    <a href="#" class="btn btn-dark dropdown-toggle" id="userMenu" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </a>
                    <ul style="background-color: #333; color: white  "    class="dropdown-menu dropdown-menu-left" aria-labelledby="userMenu">
                        <li>
                            <a   class="dropdown-item" href="profiles1.php">
                                <i    class="fas fa-user"></i> Profile <!-- Icône utilisateur -->
                            </a>
                        </li>
                        <li>
                            <a  class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion <!-- Icône déconnexion -->
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>