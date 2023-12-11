<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Tricount</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1vu.0">
    <link href="css/styles.css" rel="stylesheet"  type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="lib/jQuery/jquery-3.6.4.js"></script>

   <script>
       $(document).ready(function() {
           let select = $("#selectOption");
           select.show(); // Afficher l'input select
           let container = $("#containerSelect");
           // Récupérer mes items. get permet d'avoir le résultat
           let items = container.find('.operation').get();
           trierParDate("desc");

           select.change(function() {
               // récupérer le choix que l'utilisateur a fait avec l'input
                let selectValue = select.val();
                // pour récupérer ma div parent
                let operations = $(".operation");
               if(selectValue === "montantAsc") {
                    let ordre = "asc";
                    trierParMontant(ordre);
               };
                if(selectValue === "montantDes") {
                    let ordre = "des";
                    trierParMontant(ordre);
               };
               if (selectValue === 'titreAsc') {
                   let ordre = "asc";
                   trierParTitre(ordre);
               }
               if(selectValue === "titreDes") {
                   let ordre = "des";
                   trierParTitre(ordre);
               }
               if(selectValue === "createurAsc") {
                   let ordre = "asc";
                   trierParCreateur(ordre);
               }
               if(selectValue === "createurDes") {
                   let ordre = "des";
                   trierParCreateur(ordre);
               }
               if(selectValue === "dateAsc") {
                   let ordre = "asc";
                   trierParDate(ordre);
               }
               if(selectValue === "dateDes") {
                   let ordre = "des";
                   trierParDate(ordre);
               }
           });

           function boucle(items) {
               $.each(items, function(i, item) {
                   container.append(item);
               });
           }
           function trierParMontant(ordre) {
               items.sort(function(a, b) {
                   let montantA = parseInt($(a).find(".montant").text());
                   let montantB = parseInt($(b).find(".montant").text());
                   if(ordre === "asc") {
                       return montantA - montantB;
                   } else {
                       return montantB - montantA;
                   }
            });
           boucle(items);
           }

           function trierParTitre(ordre) {
               items.sort(function(a, b) {
                   let titleA = $(a).find('.titreTricount').text();
                   let titleB = $(b).find('.titreTricount').text();
                   if(ordre === "asc") {
                       return titleA.localeCompare(titleB); // permet de comparer deux string
                   } else {
                       return titleB.localeCompare(titleA);
                   }
               });
               boucle(items);
           }
           function trierParCreateur(ordre) {
               items.sort(function(a, b) {
                   let createurA = $(a).find(".createur").text();
                   let createurB = $(b).find(".createur").text();
                   if(ordre === "asc") {
                       return createurA.localeCompare(createurB);
                   } else {
                       return createurB.localeCompare(createurA);
                   }
               });
               boucle(items);
           }
           function trierParDate(ordre) {
               items.sort(function(a, b) {
                   // parse pour avoir le temps en milliseconde
                    let dateA = Date.parse($(a).find(".date").text());
                    let dateB = Date.parse($(b).find(".date").text());
                    if(ordre === "asc") {
                        return dateA - dateB;
                    } else {
                        return dateB - dateA;
                    }
               });
               boucle(items);
           }
       });

   </script>

</head>
<body>

<header class="container-fluid d-flex align-items-center p-3 mb-4 justify-content-between text-secondary bleuClair">
    <a href="Tricount/index" role="button" class="btn btn-outline-danger">Cancel</a>
    <h1 class="h6"> <?=$Tricount->title?> ▷ Expenses</h1>
    <a href="Tricount/edit_tricount/<?=$Tricount->id?>" class="btn btn-primary">Edit</a>
</header>

<div class="container">
     <?php if ($Tricount->nb_participants()<=1 && $Tricount->nb_operations()==0): ?>
    <div class="bd-highlight text-center border border-dark rounded pb-2">
        <h2 class="bg-secondary text-white">You are alone!</h2>
        <p>Click below to add your friends!</p>
        <a href="Tricount/edit_tricount/<?php echo $Tricount->id;?>" class="btn btn-primary" role="button">Add friends</a>
    </div>
     <?php endif; ?>

    <!--              Partie qui montre les -------------------->
    <?php if ($Tricount->nb_participants()>1 && $Tricount->nb_operations()==0): ?>
    <div class="bd-highlight text-center border border-dark rounded pb-2">
        <h2 class="bg-secondary text-white">Your tricount is empty!</h2>
        <p>Click below to add your first expense!</p>
        <a href="operation/show_add_operation/<?= $Tricount->id;?>" class="btn btn-primary" role="button">Add an expense</a>
    </div>
     <?php endif; ?>
</div>

<!-- -------------     Partie qui montre les tricounts ------------------ -->
<?php if($Tricount->nb_participants()!=0 && $Tricount->nb_operations()!=0): ?>
<div class="container" id="containerSelect">
    <div>
        <a href="tricount/balance/<?= $Tricount->id ?>/" type="submit"  class="btn btn-success btn-block ">View Balance</a>
    </div>
    <select class="form-select mt-1" aria-label="Default select example" id="selectOption" style="display: none">
        <option selected value="titreAsc">Titre ⇧</option>
        <option value="titreDes">Titre ⇩</option>
        <option value="montantAsc">Montant ⇧</option>
        <option value="montantDes">Montant ⇩</option>
        <option value="dateDes">Date ⇧</option>
        <option value="dateAsc">Date ⇩</option>
        <option value="createurAsc">Createur ⇧</option>
        <option value="createurDes">Createur ⇩</option>

    </select>
    <?php foreach ($listOperations as $operation): ?>
    <div class="containerOperation">
    <div class="container d-flex justify-content-between border rounded mt-3 operation" >
        <a href="Operation/show_operation/<?= $operation->id?>" class="text-reset text-decoration-none">
            <div class="mt-2 " >
                <h2 id="<?= $operation->id; ?>" class="titreTricount"><?php echo $operation->title ?></h2>
                <p id="<?= $operation->amount; ?>" class="d-flex align-items-start"><span class="montant" style="padding-right: 2px"><?= $operation->amount . " €" ?></span> euros</p>
            </div>
            <div class="mt-2 ">
                <p class="d-flex justify-content-start">Paid by<span class="createur" style="padding-left: 2px"><?=$operation->get_initiator()->full_name ?></span></p>
                <p class="d-flex align-items-start date"><?=date("d/m/Y", strtotime($operation->operation_date));?></p>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<footer class="container-fluid fixed-bottom d-flex justify-content-between bleuClair">
    <div>
        <p>MY TOTAL</p>
        <p><?= ($total === "") ? "<p> 0 € </p>" : $total . " €";?> </p>
    </div>
    <div>
        <a href="operation/show_add_operation/<?php echo $Tricount->id;?>" class="btn btn-primary rounded-circle position-absolute bottom-3 positionBtn">+</a>
    </div>
    <div>
        <p>TOTAL EXPENSES</p>
        <p><?= ($Expenses == "") ? "<p> 0 € </p>" : $Expenses . " €";?> </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>