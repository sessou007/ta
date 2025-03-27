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
        #notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    color: #333; /* Couleur du texte */
    font-family: 'Arial', sans-serif; /* Police de caractères */
}

.notification-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    color: #333; /* Couleur du texte */
    transition: background-color 0.3s ease; /* Animation au survol */
}

.notification-item.unread {
    background: #f9f9f9;
    font-weight: bold; /* Texte en gras pour les notifications non lues */
}

.notification-item:hover {
    background-color: #f1f1f1; /* Couleur de fond au survol */
    cursor: pointer; /* Curseur en forme de main au survol */
}

.notification-item:last-child {
    border-bottom: none; /* Supprime la bordure du dernier élément */
}

/* Style pour le compteur de notifications */
#notification-count {
    background: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    position: absolute;
    top: -5px;
    right: -10px;
}

/* Style pour l'icône de notification */
#notification-bell {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    position: relative;
    color: white; /* Couleur de l'icône */
}

#notification-bell:hover {
    color: #28a745; /* Couleur de l'icône au survol */
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
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-item {
            animation: fadeIn 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar intégrée -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Logo et Nom -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/logo-masm.png" alt="Armoirie du Bénin" class="armoirie" />
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
                        <a class="nav-link active" href="dashboard.php">Tableau de Bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tac.php">Taches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rapport.php">Rapport</a>
                    </li>
                    <!-- Notification Bell -->
                    <li class="nav-item">
                        <div id="notification-container">
                            <button id="notification-bell">
                                🔔 <span id="notification-count">0</span>
                            </button>
                            <div id="notification-dropdown">
                                <div id="notification-list"></div>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Icône de connexion avec menu déroulant -->
                <div class="dropdown">
                    <a href="#" class="btn btn-dark dropdown-toggle" id="userMenu" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </a>
                    <ul style="background-color: #333; color: white" class="dropdown-menu dropdown-menu-left" aria-labelledby="userMenu">
                        <li>
                            <a class="dropdown-item" href="profiles.php">
                                <i class="fas fa-user"></i> Profile <!-- Icône utilisateur -->
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion <!-- Icône déconnexion -->
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Élément audio pour le bip sonore -->
    <audio id="notification-sound" src="/t/sounds/mixkit-bell-notification-933.mp3"></audio>

    <script>
        $(document).ready(function() {
            // Fonction pour afficher ou masquer la liste des notifications
            function toggleNotifications() {
                const dropdown = $('#notification-dropdown');
                dropdown.toggle();
                if (dropdown.is(':visible')) {
                    fetchNotifications(); // Recharge les notifications lorsque la liste est affichée
                }
            }

            // Attacher l'événement au bouton
            $('#notification-bell').click(toggleNotifications);

            // Fonction pour récupérer les notifications
            function fetchNotifications() {
                $.ajax({
                    url: 'fetch_notifications.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            console.error("Erreur du serveur: ", data.error);
                            return;
                        }

                        // Vérifier s'il y a de nouvelles notifications non lues
                        const previousCount = parseInt($('#notification-count').text(), 10);
                        const newCount = data.count;

                        if (newCount > previousCount) {
                            // Jouer le son de notification
                            const notificationSound = document.getElementById('notification-sound');
                            notificationSound.play();
                        }

                        // Mettre à jour le compteur et la liste des notifications
                        updateNotificationCount(newCount);
                        updateNotificationList(data.notifications);
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors de la récupération des notifications: ", error);
                        console.log("Réponse du serveur: ", xhr.responseText);
                    }
                });
            }

            // Fonction pour mettre à jour le compteur de notifications
            function updateNotificationCount(count) {
                $('#notification-count').text(count);
            }

            // Fonction pour mettre à jour la liste des notifications
            function updateNotificationList(notifications) {
                const notificationList = $('#notification-list');
                notificationList.empty();

                notifications.forEach(notification => {
                    const iconClass = notification.read ? 'fas fa-bell-slash' : 'fas fa-bell'; // Icône différente pour les notifications lues/non lues
                    const notificationItem = $('<div>').addClass('notification-item')
                        .html(`<i class="${iconClass}"></i> ${notification.message}`) // Ajoute l'icône et le message
                        .click(function() {
                            markNotificationAsRead(notification.id);
                        });

                    if (!notification.read) {
                        notificationItem.addClass('unread');
                    }

                    notificationList.append(notificationItem);
                });
            }

            // Fonction pour marquer une notification comme lue
            function markNotificationAsRead(notificationId) {
                $.ajax({
                    url: 'mark_notification_as_read.php',
                    method: 'POST',
                    data: { id: notificationId },
                    success: function() {
                        fetchNotifications(); // Recharge les notifications après marquage
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors du marquage de la notification comme lue: ", error);
                        console.log("Réponse du serveur: ", xhr.responseText);
                    }
                });
            }

            // Recharge les notifications toutes les 30 secondes
            setInterval(fetchNotifications, 30000);

            // Charge les notifications au chargement de la page
            fetchNotifications();
        });
    </script>
</body>

</html>