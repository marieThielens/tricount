<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance</title>
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .progress-bar {
            background-color: #f2f2f2;
            border-radius: 5px;
            padding: 3px;
        }

        .progress {
            height: 20px;
            border-radius: 5px;
            color: white;
            text-align: center;
            font-weight: bold;
            line-height: 20px;
            transition: width 0.5s ease-in-out;
        }

        .progress.positif {
            background-color: green;
        }

        .progress.negatif {
            background-color: red;
        }

    </style>
</head>
<body>
<header class="container-fluid d-flex p-3 mb-4 justify-content-around text-secondary bleuClair">
    <a href="Tricount/view_tricount/<?=$tricount->id?>" role="button" class="btn btn-outline-danger mt-2">Cancel</a>
    <h1><?=$tricount->title?> ▷ Balance</h1>

</header>


<div class="container">
    <?php foreach ($participants as $participant):
        if($tricount->implicates($participant)):?>

    <div class="d-flex flex-row <?php if($tricount->balance_status($participant) < 0) echo 'flex-row-reverse'; ?>">
        <div class="progress sizeProgressBar w-50  mb-1 d-flex ">
            <div class="progress-bar <?= ($tricount->balance_status($participant) >= 0) ? "bg-success" : "bg-danger"  ?>" role="progressbar" style="width:<?= $tricount->balance_percentage($participant)?>%;" aria-valuemin="0" aria-valuemax="100">
               <?= $tricount->balance_status($participant) ?>
            </div>
        </div>
        <div class=" mb-1"><p><?= $participant->full_name ?> </p></div>
    </div>

        <?php endif;
    endforeach; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>