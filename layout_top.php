<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Socadel - <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app">
    <div class="topbar">
        <div class="logo">SOCA<span>DEL</span></div>
        <div class="user">
            <?php echo htmlspecialchars($_SESSION["name"]); ?>
            <a href="account.php">My account</a>
            <a href="logout.php">Logout</a>
        </div></main>
    </div>
    <main class="container">