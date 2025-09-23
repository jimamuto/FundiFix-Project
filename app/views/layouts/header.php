<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF--8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fundi-Fix Booking Service</title>
    
    <!--  BOOTSTRAP CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- A navigation bar from Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?action=home">Fundi-Fix</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    
                    <!-- DYNAMIC NAVIGATION LINKS -->
                    <!-- This PHP code checks if a user is logged in by looking at the session. -->
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- If the user IS logged in, show these links -->
                        <li class="nav-item">
                            <a class="nav-link" href="?action=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?action=logout">Logout</a>
                        </li>
                    <?php else: ?>
                        <!-- If the user IS NOT logged in, show these links -->
                        <li class="nav-item">
                            <a class="nav-link" href="?action=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?action=register">Register</a>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- The main content of each page will start here -->

