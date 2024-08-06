<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sophisticated Responsive Website</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>Your Website</h3>
            </div>
            <ul class="list-unstyled components">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Portfolio</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content" class="content">
            <header class="header">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="bi bi-list"></i> <!-- Bootstrap hamburger menu icon -->
                </button>
                <h1>Welcome to Our Website</h1>
                <p>A showcase of modern design and functionality</p>
            </header>
            <div class="container">
                <h2>Main Content Area</h2>
                <p>This is the main content area of your website.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle (JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>

<?php
    //Prepare mail
    $mailTo = "aklinke111@gmail.com";
    $title = "Mail von import.kromiag.de";
    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
    $mailFrom .= "Content-Type: text/html\r\n";
//    $msg = "<b>Webhook from Sortly</b>:\r\n\r\n".print_r( $json_data, true );
    $msg = "<b>Webhook from Sortly</b>:\r\n\r\n".print_r($_GET);

    //Send mail
    mail($mailTo, $title, $msg, $mailFrom);
