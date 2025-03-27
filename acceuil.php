<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="https://social.gouv.bj/public/images/amoirie.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Lien vers votre fichier CSS externe -->
    <link rel="stylesheet" href="stylee.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://inclusionsociale.social.gouv.bj/admin/css/home.css?v=6">
    <title>MASM - Plateforme de suivie des activités</title>
</head>
<body>
    <div class="banner">
        <div class="carousel-container">
            <div class="carousel">
                <img src="images/po.jpg" class="active" alt="Image de présentation 1">
                <img src="images/DES.jpg" alt="Image de présentation 2">
                <img src="images/re.jpeg" alt="Image de présentation 3">
            </div>
       
        <div id="banner1" >
            <div class="container pt-4">
                <nav class="navbar navbar-expand-md">
                    <div class="container-fluid">
                        
                            <img src="images/logo_masm.png" class="logo" alt="">
                        
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto px-md-3 mb-2 mb-md-0 col justify-content-between">
                                <li class="nav-item">
                                    <a class="nav-link active fsi-6" aria-current="page" href="login.php">Accueil</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fsi-6" aria-current="page" target="_blank" href="login.php">TACHES</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fsi-6" aria-current="page" href="login.php">RAPPORT</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fsi-6" aria-current="page" href="login.php">
                                        <i class="fas fa-sign-in-alt"></i> CONNEXION
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    </div>
                </nav>
                <div class="carousel-text" style="margin-bottom: -100px;">
                    <p>Bienvenue dans votre espace de gestion des tâches ! Cette page vous offre une vision d'ensemble complète et intuitive sur vos missions et leur progression. Elle est conçue pour vous aider à suivre facilement vos objectifs, prioriser vos actions, et rester informé des tâches.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="about">
        <h2 style="text-align: center;">À propos de nous</h2>
        <div class="about-grid">
            <div class="about-card">
                <h3>Mission</h3>
                <p>Renforcer la cohésion sociale et promouvoir l'accès aux ressources économiques pour les citoyens les plus vulnérables.</p>
            </div>
            <div class="about-card">
                <h3>Vision</h3>
                <p>Un pays où chaque citoyen bénéficie d'une égalité des chances et d'un soutien pour améliorer sa qualité de vie.</p>
            </div>
            <div class="about-card">
                <h3>Valeurs</h3>
                <p>Solidarité, équité, inclusion et innovation pour répondre aux besoins des plus démunis.</p>
            </div>
            <div class="about-card">
                <h3>Histoire</h3>
                <p>Créé pour améliorer les conditions sociales et économiques des populations les plus vulnérables à travers des initiatives stratégiques.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Ministère des Affaires Sociales et de la Microfinance. Tous droits réservés.</p>
    </footer>

    <script>
        const images = document.querySelectorAll('.carousel img');
        let currentIndex = 0;

        function showNextImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
        }

        setInterval(showNextImage, 5000); // Change image every 5 seconds
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    #banner1 {
        height: 75vh;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .fsi-6 {
        font-size: 17px;
    }

    .text-green {
        color: #11855A !important;
    }

    #banner1 nav {
        border-radius: 60px;
        background-color: rgba(255, 255, 255, 0.932);
    }

    #banner1 nav .logo {
        width: 17rem;
        /* Désactiver le survol */
        pointer-events: none; /* Empêche les interactions de survol */
    }

    @media (max-width: 768px) {
        #banner1 nav .logo {
            width: 15rem;
        }
        #banner1 nav {
            border-radius: 20px;
        }
    }

    #banner1 nav li a {
        text-transform: uppercase;
    }

    #banner1 nav li a.active {
        color: #069bc8;
        font-weight: bold;
    }

    #banner1 .title {
        height: 60%;
    }

    @media (min-width: 768px) {
        #banner1 .title {
            font-size: 45px !important;
        }
    }

    /* Content Styles */
    #content {
        background-color: white;
        border-radius: 10px;
        position: relative;
        top: -10vh;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #content .links {
        position: relative;
        top: -5vh;
        margin: 2rem;
    }

    #content .links a {
        text-decoration: none;
    }

    @media (max-width: 993px) {
        #content,
        #content .links {
            top: 0 !important;
        }

        #content .links {
            margin-top: 2rem;
        }

        #banner1 .title {
            height: 70%;
        }
    }
    
    /* Custom Navigation Bar */
    @media (min-width: 1200px) {
        .container, .container-lg, .container-md, .container-sm, .container-xl {
            max-width: 1200px !important;
        }
    }

    .nav-link.fsi-6 {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .required-symbol {
        font-weight: bold;
        color: red;
    }

    .navigation-bar {
        z-index: 999 !important;
    }

    @media (max-width: 768px) {
        #banner1 nav {
            background-color: rgba(255, 255, 255, 0.98);
            border: 1px solid #eee;
        }
    }
</style>
</html>