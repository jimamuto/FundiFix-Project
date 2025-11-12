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
            <!-- Brand Logo -->
            <a class="navbar-brand fw-bold" href="http://localhost/FundiFix-Project/public/index.php">
                FundiFix
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Simple logged-in menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=dashboard">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=profile">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=logout">
                                Logout (<?php echo htmlspecialchars($_SESSION['user']['name']); ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Guest menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=home">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=login">
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/FundiFix-Project/public/index.php?action=register">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <?php echo $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <?php echo $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</body>
</html>