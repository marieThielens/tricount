<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>"/>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Settings</title>
</head>
<body>

<!-- p = padding, my = margin-->
    <header class="container-fluid d-flex p-3 mb-4  text-white bleuClair">
    <form method="get" action="Tricount" class="container d-flex justify-content-between">
        <button  type="submit" class="btn btn-outline-danger" >Back</button>
        <p class="mt-2"> Settings</p>
    </form>

    </header>

    <?php var_dump($currentUser); ?>

    <div class="main container  my-auto">

       <p>Hey <?= $currentUser->full_name ?> !</p>

        <p>I know your email adress is  <span class="text-danger"><?= $currentUser->mail ?></span> .</p>

        <p>What can I do for you  <?= $currentUser->full_name ?> ?</p>


        <div class=" d-flex flex-column">
            
            <a href="User/edit_profil" role="button" class="btn btn-outline-primary mt-2">Session 1</a>
            <a href="User/edit_profil" role="button" class="btn btn-outline-primary mt-2">Edit profile</a>
            <a href="User/change_password" role="button" class="btn btn-outline-primary mt-2">Change password</a>
            <a href="User/logout" class="btn btn-danger btn-info mt-2" role="button">Logout</a>
        </div>
    </div>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>