<?php
$auth = new SecureUserAuthentication();
?>
<!doctype html>
<html lang="fr" class="mh-100">

<head>
    <meta charset="utf-8">
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>

    <!-- Custom -->
    <link rel="stylesheet" href="/layouts/default_style.css">
    <script src="/js/loading.js"></script>
    <script src="https://unpkg.com/infinite-scroll@4/dist/infinite-scroll.pkgd.min.js"></script>
</head>
<body class="d-flex flex-column mh-100">
<?= $navbar ?>
<div id="loadingScreen" class="d-flex justify-content-center align-items-center flex-column">
    <h1 class="fst-italic brand text-center"><span class="text-secondary">We</span><span class="text-main">Sports</span>
    </h1>
    <h2 class="mb-3"><span class="badge rounded-pill bg-secondary shadow d-flex align-items-center"
                           id="loadingText"><span class="me-2">Chargement</span>
                    <span class="spinner-border" role="status">
                    </span></h2>
</div>
<div class="container bg-feed shadow hidden-scroll mh-100">
    <div class="d-flex flex-column mh-100">
        <?= $content ?>
    </div>
</div>
</body>

</html>