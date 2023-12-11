<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <title>List Tricounts</title>
</head>
<body>

<!-- p = padding, my = margin-->
    <header class="container-fluid d-flex p-3 mb-4 justify-content-between text-white bleuClair">
        <h1>Yours tricounts</h1>
        <form action="tricount/add_tricount" class="mt-2">
            <Button type="submit" class="btn btn-primary">Add</Button>
        </form>
    </header>
<!--    --><?php //print_r($member->id) ?><!-- id du membre -->

   <?php foreach ($tricounts as $key => $tricount): ?>
        <div class="container border container rounded">
           <a href="Tricount/view_tricount/<?= $tricount->id ?>" class="text-reset text-decoration-none">
            <div class="d-flex justify-content-between">
                <h2><?= $tricount->title ?></h2>
                <p>
                    <?php echo $tricount->get_nb_participants() != 0 ? $tricount->get_nb_participants() . " friends" : "You are alone "; ?>
                </p>
            </div>
            <div>
                <p><?=$tricount->description == null || $tricount->description == "NULL" ? "No description" : $tricount->description ?></p>
            </div>
            </a>
        </div>
    <?php endforeach; ?>

    <footer class="d-flex justify-content-end mt-3 container align-items-end">
        <form method="get" action="User">
            <button type="submit" class="btn btn-link">
                 <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-gear-fill text-warning" viewBox="0 0 16 16">
                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                </svg>
            </button>
        </form>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>


