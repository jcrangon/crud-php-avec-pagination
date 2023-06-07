<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titrePrincipal ?></title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- css custom -->
    <link rel="stylesheet" href="./assets/css/app.css">
</head>
<body id="<?= $bodyId ?>">
<nav class="navbar bg-success">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="<?= HTTP_SITE_URL ?>">CRUD - PHP procedural</a>
  </div>
</nav>
<main>