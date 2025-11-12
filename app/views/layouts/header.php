<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FundiFix - Home Repairs Made Easy</title>

    <!-- Custom CSS - ABSOLUTE PATH -->
    <link rel="stylesheet" href="http://localhost/FundiFix-Project/public/css/style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- ABSOLUTE PATH for all links -->
            <?php if (isset($_SESSION['user']) && !isset($_SESSION['2fa_user']) && !isset($_SESSION['verify_email'])): ?>
                <!-- For logged-in users: FundiFix goes to dashboard -->
                <a class="navbar-brand fw-bold" href="http://localhost/FundiFix-Project/public/index.php?action=dashboard">FundiFix</a>
            <?php else: ?>
                <!-- For logged-out users: FundiFix goes to home page -->
                <a class="navbar-brand fw-bold" href="http://localhost/FundiFix-Project/public/index.php?action=home">FundiFix</a>
            <?php endif; ?>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user']) && !isset($_SESSION['2fa_user']) && !isset($_SESSION['verify_email'])): ?>
                        <!-- For logged-in users: Home goes to dashboard -->
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=dashboard">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=logout">Logout</a></li>
                    <?php else: ?>
                        <!-- For logged-out users: Home goes to home page -->
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=login">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
