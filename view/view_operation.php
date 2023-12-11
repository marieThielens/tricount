<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/styles.css" rel="stylesheet" type="text/css"/>
    <title>View Operation</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <header class="container-fluid d-flex p-3 mb-4 justify-content-between text-secondary bleuClair">
        <!-- Lien Cancel et Edit a corriger car il est en double dans l'url du naviguateur -->
        <a href="Tricount/view_tricount/<?=$operation->tricount->id?>" class="btn btn-outline-danger">Cancel</a>
        <h1 class="small"><?=$operation->tricount->title?>  ▷  <?= $operation->title ?></h1>
        <a href="Operation/show_edit_operation/<?=$operation->id?>" class="btn btn-primary">Edit</a>
    </header>
<div class="container">
    <!--
    Montant de la dépense
    Nom de la personne qui a payé et a quel date
    Le nombre de participants impliqué
    Liste des participants avec la répartition de la dépense
    -->
    <p class="text-center font-weight-bold" style="font-weight: bold"><?php echo round($operation->amount,2) . " €"?> </p>
    <div>
        <p>Paid by <span><?= $operation->initiator->full_name ?></span></p>
        <p><?=date("d/m/Y", strtotime($operation->operation_date));?></p>
    </div>
    <p>For <span><?php echo $operation->get_number_participants();?></span> participants<?php if($operation->is_participate($member)): ?>, including <span style="font-weight: bold">me</span></p><?php endif; ?>
</div>

<?php if(!empty($list_participants)) :?>
    <div class="container">
    <?php
    $creator = $operation->initiator->full_name;
    foreach ($list_participants as $participant): ?>

        <?php if ($operation->participates($participant)):?>
            <div class="d-flex justify-content-between border p-2 <?= ($participant->id == $member->id) ? 'fw-bold' : '' ?>">
                <p><?php echo ($participant->id == $member->id) ? "$participant->full_name (me)" : "$participant->full_name" ?></p>
                <p><?php echo $operation->get_amount($participant)?> €</p>
            </div>
        <?php endif;?>
    <?php endforeach; ?>
    </div>
<?php endif;?>
<?php if(empty($list_participants)) : ?>
<div class="container border">
    <p>No participants for this expense. Click on the edit button to add participants </p>
</div>

<?php endif;?>
</div>

<footer class="container-fluid position-absolute bottom-0 d-flex justify-content-between bleuClair">
    <form action="" class="mt-2">
        <?php if($operation->previous_one() !== false): ?>
            <a href="Operation/show_operation/<?=$operation->previous_one()?>" class="btn btn-primary">Previous</a>
        <?php endif; ?>
    </form>

    <form action="" class="mt-2">
        <?php if($operation->next_one() !== false): ?>
            <a href="Operation/show_operation/<?=$operation->next_one()?>" class="btn btn-primary">Next</a>
        <?php endif; ?>
    </form>

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>