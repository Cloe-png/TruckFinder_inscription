<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodTruck App - {TITLE}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --rouge: #e63946;
            --orange: #f18f01;
            --turquoise: #1d3557;
            --bleu-fonce: #2a3547;
            --blanc-casse: #f1f1f1;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: var(--bleu-fonce) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary-custom {
            background-color: var(--turquoise);
            border-color: var(--turquoise);
        }
        .btn-primary-custom:hover {
            background-color: #1a2c4a;
            border-color: #1a2c4a;
            transform: translateY(-2px);
        }
        .btn-secondary-custom {
            background-color: var(--orange);
            border-color: var(--orange);
        }
        .btn-danger-custom {
            background-color: var(--rouge);
            border-color: var(--rouge);
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }
        .foodtruck-bg {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7)),
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
        }
        .form-control-custom {
            border-radius: 30px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }
        .form-control-custom:focus {
            border-color: var(--turquoise);
            box-shadow: 0 0 0 0.25rem rgba(29, 53, 87, 0.25);
        }
        .alert-custom {
            border-radius: 30px;
            padding: 15px 20px;
        }
        .foodtruck-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .foodtruck-card:hover {
            transform: translateY(-5px);
        }
        .foodtruck-img {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-truck me-2"></i>
                <span class="fw-bold">Truck<span class="text-warning">Finder</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!--{NAVIGATION}-->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!--{ALERT}-->
        <!--{CONTENT}-->
    </div>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">TRUCKFINDER - 2025 - BTS2SIO</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-custom');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</body>
</html>
