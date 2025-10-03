<!DOCTYPE html>
<html>
<head>
    <title>FoodTruck App - {TITLE}</title>
    <style>
        :root {
            --rouge: #e63946;
            --orange: #f18f01;
            --turquoise: #1d3557;
            --bleu-fonce: #2a3547;
            --blanc-casse: #f1f1f1;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--blanc-casse);
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: var(--bleu-fonce);
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        footer {
            background: var(--bleu-fonce);
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary {
            background: var(--turquoise);
        }
        .btn-primary:hover {
            background: #1a2c4a;
        }
        .btn-secondary {
            background: var(--orange);
        }
        .btn-secondary:hover {
            background: #d67b02;
        }
        .btn-success {
            background: var(--rouge);
        }
        .btn-success:hover {
            background: #c42d37;
        }
        .btn-danger {
            background: #8a2532;
        }
        .btn-danger:hover {
            background: #6a1b26;
        }
        .card {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: var(--bleu-fonce);
            color: white;
        }
        .alert {
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4f4dd;
            color: var(--turquoise);
        }
        .alert-error {
            background: #fdecef;
            color: var(--rouge);
        }
        a {
            color: var(--turquoise);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>TRUCKFINDER</h1>
            <h6>Trouverez vous un foodtruck à votre goût ?</h6>
            <!--{NAVIGATION}-->
        </div>
    </header>

    <div class="container">
        <!--{ALERT}-->
        <!--{CONTENT}-->
    </div>

    <footer>
        <div class="container">
            <p>TruckFinder - 2025 - BTS2SIO</p>
        </div>
    </footer>
</body>
</html>
