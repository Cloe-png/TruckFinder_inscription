<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckFinder - Découvrez les meilleurs foodtrucks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f6c23e;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --success-color: #1cc88a;
            --turquoise-color: #17a2b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .hero-section {
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            opacity: 0.1;
            z-index: -1;
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 2rem;
        }

        .foodtruck-bg {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
        }

        .display-5 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .text-custom-turquoise {
            color: var(--turquoise-color);
        }

        .text-warning {
            color: var(--secondary-color);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #3a5bc7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .btn-secondary-custom {
            background-color: white;
            border-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .truck-carousel {
            background-color: white;
            padding: 20px 0;
            margin-top: 50px;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
        }

        .truck-scroll {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 15px 0;
            gap: 20px;
            -webkit-overflow-scrolling: touch;
        }

        .truck-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .truck-scroll::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 10px;
        }

        .truck-card {
            flex: 0 0 250px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .truck-card:hover {
            transform: translateY(-5px);
        }

        .truck-image {
            height: 150px;
            width: 100%;
            object-fit: cover;
        }

        .truck-info {
            padding: 15px;
        }

        .truck-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .truck-type {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .rating {
            color: #ffc107;
            margin-bottom: 10px;
        }

        .scrolling-text {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .scrolling-content {
            display: inline-block;
            padding-left: 100%;
            animation: scroll 20s linear infinite;
            font-weight: 500;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }

            .display-5 {
                font-size: 2rem;
            }

            .truck-card {
                flex: 0 0 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Section héroïque -->
    <div class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-custom p-4 p-md-5 text-center foodtruck-bg">
                        <div class="mb-4">
                            <i class="bi bi-truck display-4 text-warning mb-3"></i>
                            <h1 class="display-5 fw-bold mb-3">Bienvenue sur <span class="text-custom-turquoise">Truck</span><span class="text-warning">Finder</span></h1>
                            <p class="lead mb-4">Découvrez les meilleurs food trucks près de chez vous ou gérez le vôtre en quelques clics !</p>
                        </div>
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <a href="index.php?action=login" class="btn btn-primary-custom btn-lg me-md-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Connexion
                            </a>
                            <a href="index.php?action=register" class="btn btn-secondary-custom btn-lg">
                                <i class="bi bi-person-plus me-2"></i> S'inscrire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bande défilante de texte -->
    <div class="scrolling-text">
        <div class="scrolling-content">
            ★ Découvrez nos foodtrucks stars du moment ★ Le Camion Qui Fume ★ Burger Nomade ★ Tacos El Paso ★ Wok & Roll ★ Pizza Mobile ★ Glaces Artisanales ★ ★ Profitez de nos offres exclusives ★
        </div>
    </div>

    <!-- Section des foodtrucks populaires -->
    <div class="truck-carousel">
        <div class="container">
            <h3 class="text-center mb-4"><i class="bi bi-star-fill text-warning me-2"></i>Nos foodtrucks populaires</h3>
            <div class="truck-scroll">

            <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Le Camion Qui Fume">
                    <div class="truck-info">
                        <h5 class="truck-name">Le Camion Qui Fume</h5>
                        <p class="truck-type">BBQ & Burgers</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="ms-2">4.7 (128)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Paris 11ème</p>
                    </div>
                </div>

                <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Burger Nomade">
                    <div class="truck-info">
                        <h5 class="truck-name">Burger Nomade</h5>
                        <p class="truck-type">Burgers gourmets</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <span class="ms-2">4.5 (96)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Lyon 2ème</p>
                    </div>
                </div>

                <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1550547660-96bd40d7357b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Tacos El Paso">
                    <div class="truck-info">
                        <h5 class="truck-name">Tacos El Paso</h5>
                        <p class="truck-type">Cuisine mexicaine</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="ms-2">5.0 (214)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Marseille</p>
                    </div>
                </div>

                <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Wok & Roll">
                    <div class="truck-info">
                        <h5 class="truck-name">Wok & Roll</h5>
                        <p class="truck-type">Cuisine asiatique</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="ms-2">4.8 (187)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Bordeaux</p>
                    </div>
                </div>

                <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Pizza Mobile">
                    <div class="truck-info">
                        <h5 class="truck-name">Pizza Mobile</h5>
                        <p class="truck-type">Pizzas artisanales</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <span class="ms-2">4.3 (145)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Toulouse</p>
                    </div>
                </div>

                <div class="truck-card">
                    <img src="https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="truck-image" alt="Glaces Artisanales">
                    <div class="truck-info">
                        <h5 class="truck-name">Glaces Artisanales</h5>
                        <p class="truck-type">Glaces & desserts</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="ms-2">5.0 (321)</span>
                        </div>
                        <p class="text-success small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>Nice</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
