<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<!-- p = padding, my = margin-->
<header class="container-fluid d-flex p-3 mb-4 justify-content-between text-secondary bleuClair">
    <a href="Tricount/index" role="button" class="btn btn-outline-danger">Leave</a>
</header>
    <h1>Something went wrong</h1>
    <div class="main">
        <!-- Afficher les erreurs si pseudo ou mdp sont pas bons -->
        <?= $error ?>
        <?php if (!empty($errors)): ?>
            <div class='errors'>
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>