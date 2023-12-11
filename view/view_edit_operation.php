<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Operation</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>new_expense</title>
    <base href="<?= $web_root ?>"/>
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="lib/jQuery/jquery-3.6.4.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js"></script>

    <script>
        <?php if (Configuration::get('modale') === 'on'): ?>
        function confirm_cancel(){
            document.getElementById('cancelLink').addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Unsaved changes !',
                    text:'Are you sure you want to leave this form ? Changes you made will not be saved.',
                    icon: 'warning',
                    confirmButtonText: 'Leave Page',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        history.back(); // Revenir à la page précédente
                    }
                });
            });
        }

        function confirm_delete(){
            document.getElementById('deleteLink').addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Delete operation !',
                    text:'Are you sure you want to delete this tricount ?',
                    icon: 'warning',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (delete_operation()){
                            window.location.href = "Tricount/view_tricount/<?= $operation->tricount->id ?>";
                        }
                        async function delete_operation (){
                            await $.post("operation/delete_service/", {operation:<?= $operation->id?>}, null, 'json' ).then((result) => {
                                if (result.isConfirmed) {
                                    return true;

                                }
                            }).catch((error) => {
                                console.log( error);
                                return false;
                            })}
                    }
                });
            });
        }
        <?php endif;  ?>

        $(document).ready(function () {
            <?php if (Configuration::get('modale') === 'on'): ?>
            confirm_cancel();
            confirm_delete();

            $("#cancelLink").attr("href","javascript:confirm_back()");
            $("#deleteLink").attr("href","javascript:confirm_delete()");
            <?php endif;  ?>

            <?php if (Configuration::get('just_validate') === 'on'): ?>

            /* ---- Désactivation des erreurs php ----*/
            let phpErrors = document.querySelectorAll('.errors');
            phpErrors.forEach((error) => {
                error.style.display = 'none';
            });
            <?php endif;  ?>

            let coutTotal = $("#amountOperation"); // l'input ou je rentre le montant
            let containerAmount = $(".containerAmount");

            // DatePicker ----
            $("#dateOperation").datepicker({
                dateFormat: "dd/mm/yy"
            });
            let todayDate = new Date();
            const formattedDate = todayDate.toLocaleDateString('fr-FR'); // Format en mois/jour/année (e.g. 5/19/2023)


            let ajout = $(".showAmount");
            ajout.css("display", "block");


            // Mettre à jour le montant que chaque personne doit payer à chaque fois qu'un changement est effectué
            function updateMontantsAPayer() {
                // poidsParticipants correspond à l'écoute de l'input des participants avec la méthode updateMontantsAPayer
                // donc s'actualise
                const totalPoids = poidsParticipants
                    // convertir une collection d'objet en un tableau (la collections Participants )
                    .toArray()
                    // réduire le tableau à une seule valeur pour avoir la somme des poids de tous les participants
                    .reduce((total, poids) => {
                        const parsed = parseFloat($(poids).val());
                        if (isNaN(parsed) || parsed < 0) return total;
                        return total + parsed;
                    }, 0);

                // la div container de mes 4 "inputs"
                containerAmount.each(function () {
                    // récupérer la valeur de l'input ou je met le poids
                    let poids = parseInt($(this).find(".weight").val());

                    if (isNaN(coutTotal.val()) || isNaN(poids) || poids <= 0) { // si le poids n'est pas un nombre ou est inférieur à 0
                        // le montant vaut 0
                        $(this).find(".montant").val("0.00");
                        // Décocher la checkbox quand l'input est vide ou à 0.....
                        $(this).find(".checkBox").prop("checked", false);

                        //Pour les checkbox qui sont selectionnée
                    } else {
                        const checkBox = $(this).find(".checkBox");
                        if (!checkBox.prop("checked")) {
                            checkBox.prop("checked", true);
                        }
                        // je fais mon calcul
                        const coutParPersonne = parseFloat((coutTotal.val() / totalPoids) * poids);
                        // je mets 2 décimales
                        $(this).find(".montant").val(coutParPersonne.toFixed(2));
                    }
                });
            };

            // Handle changes to the total cost
            coutTotal.on("input", updateMontantsAPayer);

            //Gérer les modifications du cout total
            const poidsParticipants = $(".weight").on("input", updateMontantsAPayer);

            // Les cases à cocher
            $(".checkBox").on("input", function () {
                let isChecked = $(this).is(":checked");
                // const qui permet de savoir l'input conserné pour plus tard récupérer le contenu
                const montantInput = $(this).closest(".containerAmount").find(".montant");
                const poidsInput = $(this).closest(".containerAmount").find(".weight");
                if (!isChecked) {
                    montantInput.val("0.00");
                    poidsInput.val("0");
                    console.log(poidsInput);
                }
                else {
                    // Quand la case est coché il remet le poids à 1 par défaut
                    poidsInput.val("1");
                    // la valeur de l'input = la valeur ou on rentre le montant
                    montantInput.val(coutTotal.val());
                    updateMontantsAPayer();
                }
            });
            $(".checkBox").prop("checked", true);
            updateMontantsAPayer();


            <?php if (Configuration::get('just_validate') === 'on'): ?>
            const validator = new JustValidate('#formEdit');
            validator
                .addField('#titleOperation', [
                    {
                        rule: 'required',
                    },
                    {
                        rule: 'minLength',
                        value: 3,
                    },
                    {
                        rule: 'maxLength',
                        value: 256,
                    },
                ] , { successMessage : 'Looks good !'})
                .addField('#amountOperation', [ {
                    rule: 'required',
                },
                {
                    rule: 'minNumber',
                    value: 0.1,
                    errorMessage: 'The amount must be greater than or equal to 0'
                }
                ] , { successMessage : 'Looks good !'})
                .addField('#dateOperation', [
               /*     {
                        rule: 'required',
                    },*/
                    {
                        plugin: JustValidatePluginDate(() => ({
                            format: 'dd/MM/yyyy',
                        })),
                        errorMessage: 'Date should be in dd/MM/yyyy format (e.g. 20/12/2021)',
                    },
                    {
                        plugin: JustValidatePluginDate((fields) => {
                            return {
                                format: 'dd/MM/yyyy',
                                isBeforeOrEqual:formattedDate,

                            };
                        }),
                        errorMessage: 'Date can\'t be in the future',
                    },
                ], { successMessage : 'Looks good !'})
                .addRequiredGroup('#participates', 'Select at lease one person!', {
                    successMessage: 'Everything looks good',
                });
            const fieldSelectors = document.querySelectorAll('.weight');
            fieldSelectors.forEach(function(fieldSelector) {
                let checkbox = fieldSelector.closest('.containerAmount').querySelector('.checkBox');

                checkbox.addEventListener('change', function() {
                    if (!checkbox.checked) {
                        validator.removeField(fieldSelector);
                    } else {
                        validator.addField(fieldSelector, [
                            {
                                rule: 'integer',
                            },
                            {
                                rule: 'minNumber',
                                value: 0.1,
                                errorMessage: 'The weight must be greater than or equal to 0',
                            },
                            {
                                rule: 'required',
                                condition: function() {
                                    return checkbox.checked && fieldSelector.value.trim() === '';
                                },
                                errorMessage: 'This field is required',
                            }
                        ], { successMessage : 'Looks good !'});
                    }
                });
            });
                validator.onSuccess(function(event) {
                    event.target.submit(); //par défaut le form n'est pas soumis
                });

            <?php endif;  ?>

        });
    </script>
    <style>
        .ui-datepicker {
            background-color: #B4CFD5 !important;
            border: 1px solid #000000 !important;
            padding: 1em !important;
        }
        .ui-icon {
            padding: 1em;
        }
        .errors {
            color: red !important;
        }
        .success {
            color: green !important;
        }
    </style>
</head>
<body>
<header class="container-fluid d-flex p-3 mb-4 justify-content-between text-secondary bleuClair">
    <a href="operation/show_operation/<?= $operation->id ?>" id="cancelLink" class="btn btn-outline-danger">Cancel</a>
    <h1 class="small"><?= $operation->tricount->title ?> ▷ Edit Expense</h1>
    <button type="submit" class="btn btn-primary" form="formEdit" >Save</button>
</header>

<div class="container">

    <form method="post" action="operation/show_edit_operation/<?= $operation->id ?>" id="formEdit">
        <input type="text" name="titleOperation" id="titleOperation" placeholder="Title" class="input-group mb-3 form-control" value="<?= isset($form_input['titleOperation']) ? htmlspecialchars($form_input['titleOperation']) : $operation->title; ?>">
        <!-- Afficher l'erreur -->
        <?= (isset($errors['title_length'])) ? "<p class='errors'>".$errors['title_length']."</p>" : ""?>
        <?= (isset($errors['title_empty'])) ? "<p class='errors'>".$errors['title_empty']."</p>" : ""?>

        <div class="input-group mb-3">
            <input type="text" class="form-control" name="amountOperation" id="amountOperation"  placeholder="Amount" aria-label="Amount" aria-describedby="basic-addon2" value="<?=  isset($form_input["amountOperation"]) ? htmlspecialchars($form_input["amountOperation"]) : $operation->amount; ?>">
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">EUR</span>
            </div>
        </div>
        <!-- Afficher l'erreur -->
        <?= (isset($errors['amount_size'])) ? "<p class='errors'>".$errors['amount_size']."</p>" : ""?>
        <?= (isset($errors['amount_format'])) ? "<p class='errors'>".$errors['amount_format']."</p>" : ""?>
        <?= (isset($errors['amount_empty'])) ? "<p class='errors'>".$errors['amount_empty']."</p>" : ""?>

        <h2><small>Date</small></h2>
        <div class="input-group date mb-4" id="datepicker">
            <!-- Pour avoir un bon affichage de la date -->
            <?php
            $dateValue = isset($form_input["dateOperation"]) ? htmlspecialchars($form_input["dateOperation"]) : $operation->operation_date;
            // Convertir la date au format "MM/dd/yyyy"
            $dateFormatted = date("d/m/Y", strtotime($dateValue));
            ?>

            <input type="text" class="form-control" name="dateOperation" id="dateOperation" value="<?= isset($form_input["dateOperation"]) ? htmlspecialchars($form_input["dateOperation"]) : $dateFormatted ; ?>" placeholder="<?= $operation->operation_date ?>" />
            <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg></span>
            </div>
        </div>
        <?= (isset($errors['date_format'])) ? "<p class='errors'>".$errors['date_format']."</p>" : ""?>

            <h2>Paid by</h2>
            <div class="input-group mb-4">
                <select class="form-select" name="Paid_by" aria-label="Paid by">
                    <!-- Mettre par défaut celui qui a payé en premier dans la liste déroulante-->
                    <?php foreach ($tricount_subscribers as $tricount_subscriber):?>
                        <option value=" <?= $tricount_subscriber->id ?>"
                            <?= ((!empty($form_input["Paid_by"]) && $form_input["Paid_by"] == $tricount_subscriber->id ) ? "selected" : "") ?>
                            <?= (empty($form_input["Paid_by"]) && $tricount_subscriber->id !== $operation->initiator->id) ? "selected" : "" ?>
                        >
                            <?php echo $tricount_subscriber->full_name?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>
        <div id="participates">
            <h2>For whom ? (select at least one)</h2>
            <?= (isset($errors['fromWhom'])) ? "<p class='errors'>".$errors['fromWhom']."</p>" : ""?>
            <?= (isset($errors['repartition'])) ? "<p class='errors'>".$errors['repartition']."</p>" : ""?>
            <?php foreach ($tricount_subscribers as $subscriber): ?>
                <?php
                ; // ID cible à rechercher
                $is_participates = false;
                // Utiliser la fonction array_filter pour filtrer les objets ayant le même ID que $targetId
                if(!empty($list_repartition)){
                    $filteredArray = array_filter($list_repartition, function ($repartition) use ($subscriber) {
                        return $repartition->member->id === $subscriber->id;
                    });
                }
                // Vérifier si un objet correspondant a été trouvé
                if (!empty($filteredArray)) {
                    // Accéder au premier objet correspondant (la fonction array_filter renvoie un tableau)
                    $firstMatch = reset($filteredArray);
                    ($firstMatch->member->id == $subscriber->id ) ? $is_participates= true : $is_participates = false;
                }
                $member_weight = 0;
                if(empty($list_repartition) && $operation->is_participate($subscriber) !== false ) :
                    $member_weight = $operation->repartition_weight($subscriber);
                endif;
                if(!empty($list_repartition) && $is_participates !== false) :
                    $member_weight = $firstMatch->weight;
                endif;
                ?>
                <div class="input-group mb-4 containerAmount">
                    <div class="input-group-prepend d-flex">
                        <div class="input-group-text">
                            <input class="input-group-text checkBox" type="checkbox"  name="participates[]" value="<?= $subscriber->id;?>" aria-label="Checkbox for following text input"
                                <?= (empty($list_repartition) && $operation->is_participate($subscriber)) ? "checked" : ""?>
                                <?= (!empty($list_repartition) && $is_participates) ? "checked" : ""?>
                            >
                        </div>

                    </div>
                    <span class="input-group-text flex-grow-1" ><?= $subscriber->full_name ?></span>
                    <!------------------------------------------------------------->
                    <div class="input-group-text d-flex flex-column showAmount" style="display: none !important;">
                        <label for="chose">Amount</label>
                        <input id="amount_<?= $subscriber->id;?>" class="montant" type="text" name="amountUser[]" value="0" disabled>
                    </div>
                    <div class="input-group-text d-flex flex-column">
                        <label for="chose">Weight</label>
                        <input type="text" class="weight" name="weightUser_<?= $subscriber->id ?>" value="<?= $member_weight ?>">
                    </div>
                </div>
        <?php endforeach; ?>
        </div>
        <input type="hidden" name="operation_id" value="<?= $operation->id ?>">
    </form>
</div>
<div class="container mt-5 d-flex flex-column mb-5">
    <a href="Operation/delete/<?= $operation->id?>" id="deleteLink" class="btn btn-danger btn-lg btn-block mb-5">Delete this operation</a>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>