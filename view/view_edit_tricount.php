<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <title>edit_tricount</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="lib/jQuery/jquery-3.6.4.js"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script src="lib/just-validate-4.2.0.production.min.js"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js"></script>
    <script>

        let btnAdd, contenu, select, options, btnDelete, formDelete, formAdd;
        const idUser = "<?= $Tricount->id ?>";

        <?php if (Configuration::get('modale') === 'on'): ?>
        function confirm_back(){
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
                        window.location.href = "Tricount/view_tricount/<?= $Tricount->id ?>";
                    }
                });
            });
        }
        function confirm_delete() {

            document.getElementById('action_delete').addEventListener('click', function(event) {

                event.preventDefault();

                Swal.fire({
                    title: 'Delete Tricount !',
                    text:'Are you sure you want to delete this Tricount ?',
                    icon: 'warning',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete_tricount();
                    }
                });
            });
        }

        function delete_tricount() {
            $.post("tricount/delete_service/", {
                tricount: <?= $Tricount->id ?>
            }, null, 'json').then((result) => {
                if (result) {
                    window.location.href = "Tricount/";
                }
            }).catch((error) => {
                console.log(error);
            })
        }
        <?php endif;  ?>

        $(document).ready(function() {
            let phpErrors = document.querySelectorAll('.errors');
            phpErrors.forEach((error) => {
                error.style.display = 'none';
            });
            <?php if (Configuration::get('modale') === 'on'): ?>

            confirm_back();
            confirm_delete();
            $("#cancelLink").attr("href","javascript:confirm_back()");
            $("#action_delete").attr("href","javascript:confirm_delete()");
            <?php endif;  ?>

            <?php if (Configuration::get('just_validate') === 'on'): ?>
            let titleError = $('#validation_title');
            titleError.css('display', 'none');
            let descriptionError = $('#validation_description');
            descriptionError.css('display', 'none');

            let titreAvailable;
            const validator = new JustValidate("#saveSub", {
                validateBeforeSubmitting: true,
                lockForm: true,
                focusInvalidField: false,
                successLabelCssClass: ['success'],
                errorLabelCssClass: ['errors'],
                errorLabelStyle: undefined,
            });
            validator
                .addField('#titleTricount', [
                    {
                        rule: 'required',
                        errorMessage: 'Title is required'

                    },
                    {
                        rule: 'maxLength',
                        value: 256
                    },
                    {
                        rule: 'minLength',
                        value: 3,
                        errorMessage: 'Minimum 3 characters'
                    },
                ], { successMessage : 'Looks good !'})
                .onValidate(async function() {
                    titreAvailable = await $.post("tricount/tricount_exists_service/", {titre: $("#titleTricount").val(),id:<?= $Tricount->id?>}, null, 'json');
                    if(titreAvailable) {
                        this.showErrors({ '#titleTricount' : "title already exists" });
                    }
                })
                .addField("#descriptionTricount", [
                    {
                        validator: (value) => {
                            const stringValue = String(value);
                            return stringValue.length === 0 || (stringValue.length > 2);
                        },
                        errorMessage: (value) => { // si ma description est vide
                            if (value.length === 0) {
                                return null; // pas d'erreur
                            }
                            if (value.length === 1 || value.length === 2) {
                                return "Description should be more than 2 letters.";
                            }
                            return null;
                        },
                    },

                ] , { successMessage : 'Looks good !'})
                .onSuccess(function(event) {
                    event.target.submit(); //par défaut le form n'est pas soumis
                });
            <?php else: ?>
            //Implementaion verification titre et description JAVASCRIPT PURE

            let titre = $("#titleTricount");
            let description = $("#descriptionTricount");
            let form_add = $("#saveSub");
            let reponse_title = $("#validation_title");
            let reponse_description = $("#validation_description");
            let valid_title =false;
            let valid_description = true;
            let btn_add = $("#btnAdd");

            // envoi = false
            btn_add.click(function (event) {
                // if envoi = false
                event.preventDefault();

                if(valid_title && valid_description ){
                    form_add.submit();
                }
            });
            titre.on("input", async function (){
                let inputVal = $(this).val().trim();

                //Titre doit faire min 3 caractères
                if(inputVal.length < 3) {
                    reponse_title.html("Title need to be more than 3 character.");
                    reponse_title.removeClass();
                    reponse_title.addClass("text-danger");
                    valid_title = false;
                }
                else {
                    reponse_title.html("");
                    reponse_title.removeClass();
                    valid_title = true;
                    await uniqueTitle($(this).val(), reponse_title);
                }
            });
            description.on("input", function (){
                let descriptionVal = $(this).val().trim();
                if(descriptionVal.length < 3 && descriptionVal.length > 0) {
                    reponse_description.html("Must contain at least 3 characters");
                    reponse_description.addClass("text-danger");
                    valid_description = false;
                } else {
                    reponse_description.html("");
                    valid_description = true;
                }
            });
            async function uniqueTitle (string,field){
                const response = await $.post("tricount/tricount_exists_service/",{titre : string,id:<?= $Tricount->id?>},null,'json');
                field.addClass("text-danger"); // que le texte soit en rouge
                if(response && string.length >=3){
                    field.html("Title already exists.");
                    field.removeClass();
                    field.addClass("text-danger");
                    valid_title = false;
                }
                else{
                    field.html("Title don't already exists.");
                    field.removeClass();
                    field.addClass("text-success");
                    valid_title = true;
                }
            }
            <?php endif;  ?>


            btnAdd = $("#ajaxAddParticipant");
            contenu = $(".contenu");
            // id unique car j'avais besoin en php qu'il soit unique pour pas qu'il delete le dernier de la liste


            select = $("#selectSub");
            options = select.find('option');
            btnDelete = $(".btnDelete");
            // formDelete = $("#delSub");
            let container = $('form.link');
            let items = container.find('.container-fluid');

            sortDeleteList();
            sortParticipantsList();


            /*  -----------------------------------------------*/
            $(document).on('click', '.btnDelete', async function  (event) {
                // btnDelete.click(async function  (event) {
                // empecher le POST via le formulaire
                event.preventDefault();

                // récupérer l'id du participant
                let idParticipant = $(this).siblings("input[name='id_participant']").val();

                const data = await $.post("tricount/delete_subscription_service/" , { monTricount : idUser , monUser : idParticipant }, null, 'json');

                if(data) {
                    /*     nouveauBoutton.click(async  function (e) {
                            e.preventDefault();*/
                    // trouver la div la plus proche
                    let divAsupprimer = $(this).closest('.container-fluid');
                    let nomUtilisateur = $(this).closest('.container-fluid').find('p').text();
                    let option = $("<option>").attr({"value" : idParticipant});
                    option.append(nomUtilisateur);

                    $('#selectSub').append(option);
                    divAsupprimer.remove();

                }
            });


            btnAdd.click(async function (event) {
                // empecher le POST via le formulaire
                event.preventDefault();

                // récupérer l'option séléctionnée (son id)
                let selectedOption = $('#selectSub option:selected').val();
                // Récupérer le texte correspondant à la valeur choisie
                let selectedText = $('#selectSub option:selected').text();

                const data = await $.post("tricount/subscription_service/" , { monTricount : idUser , monUser : selectedOption }, null, 'json');
                if(data) {
                    let div = $("<div>");
                    div.attr("class", "container-fluid border rounded d-flex justify-content-between mb-1");
                    let text = $('<p>').text(selectedText);
                    div.append(text);

                    // Crée un champ caché
                    let nouveauInput = $('<input>').attr({
                        'type' : 'text',
                        'name' : 'id_participant',
                        'value' : selectedOption,
                        'hidden' : true
                    });
                    div.append(nouveauInput);

                    let inputCache2 = $('<input>').attr({
                        'type' : 'text',
                        'name' : 'id_tricount',
                        'value' : <?= $Tricount->id ?>,
                        'hidden' : true
                    });
                    div.append(inputCache2);

                    // Crée un bouton supprimer qui envoie le formulaire avec la valeur à supprimer
                    let nouveauBoutton = $('<button>').addClass('btn btn-primary btnDelete');


                    nouveauBoutton.attr({
                        'type' : 'submit',
                        'form' : 'delSub',
                        'width': '36',
                        'height': '36',
                    });
                    let boutonPoubelle = $('<svg>').attr({
                        'xmlns': 'http://www.w3.org/2000/svg',
                        'width': '16',
                        'height': '16',
                        'fill': 'currentColor',
                        'class': 'bi bi-trash',
                        'viewBox': '0 0 16 16'
                    });

                    let boutonPath = $('<path>').attr('d', 'M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z');
                    let boutonPath2 = $('<path>').attr({
                        'fill-rule': 'evenodd',
                        'd': 'M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'
                    });
                    nouveauBoutton.append(boutonPoubelle);
                    boutonPoubelle.append(boutonPath);
                    boutonPoubelle.append(boutonPath2);
                    div.append(nouveauBoutton);

                    $("#delSub").append(div);
                    // Enlever le contenu
                    $("#selectSub option:selected").remove();
                }
            });

            // Fonction de tri de la liste des participants ---------------------
            function sortParticipantsList() {
                options.sort(function (a, b) {
                    let aText = a.text.toLowerCase();
                    let bText = b.text.toLowerCase();
                    if(aText > bText) return 1;
                    else if (aText < bText) return -1;
                    else return 0;
                });
                select.html(options);
            }

            // Fonction de tri pour les participants qu'on peut supprimer ----------------
            function sortDeleteList() {
                items.sort(function(a, b) {
                    var nameA = $(a).find('p').text().toUpperCase();
                    var nameB = $(b).find('p').text().toUpperCase();
                    return (nameA < nameB) ? -1 : (nameA > nameB) ? 1 : 0;
                });
                container.empty();
                $.each(items, function(i, item) {
                    container.append(item);
                });
            }

        });
    </script>
</head>
<body>
<header class="container-fluid d-flex p-3 mb-4 justify-content-between text-secondary bleuClair">
    <!-- Si on utilise pas la modale le lien php est désactivé-->

        <a class="btn btn-outline-danger" href="Tricount/view_tricount/<?= $Tricount->id ?>" id="cancelLink">Cancel</a>
    <h1><?php echo $Tricount->title;?> ▷ Edit</h1>
    <div></div>
    <button type="submit" class="btn btn-primary" form="saveSub">Save</button>
</header>

<form action="Tricount/edit_tricount/<?php echo $Tricount->id;?> " id="saveSub" method="post">
    <div class="container">
        <h2>Settings</h2>
            <input type="hidden" id="id_tricount" name="id_tricount" value="<?= $Tricount->id ?>">
            <div class="form-group">
                <label for="titleTricount">Title :</label>
                <input type="text" class="form-control" id="titleTricount" name="titleTricount" value="<?= isset($form_input['title']) ? htmlspecialchars($form_input['title']) : $Tricount->title; ?>" placeholder="Week-end à Paris">
            </div>
            <p id="validation_title"></p>
            <?= (isset($errors['title_empty'])) ? "<p class='errors' >".$errors['title_empty']."</p>" : ""?>
            <?= (isset($errors['title_size'])) ? "<p class='errors' >".$errors['title_size']."</p>" : ""?>
            <?= (isset($errors['title_unique'])) ? "<p class='errors' >".$errors['title_unique']."</p>" : ""?>
            <div class="form-group mt-2">
                <label for="descriptionTricount">Description (optional) :</label>
                <textarea class="form-control" id="descriptionTricount" name="descriptionTricount" rows="5"><?= isset($form_input['description']) ? htmlspecialchars($form_input['description']) : $Tricount->description; ?></textarea>
            </div>
            <p id="validation_description"></p>
            <?= (isset($errors['description_size'])) ? "<p class='errors' >".$errors['description_size']."</p>" : ""?>
    </div>
</form>


    <div class="container mt-2">
        <h2 class="mb-3">Subscriptions</h2>
        <div class="input-group-prepend contenu">
            <form class='link' action='tricount/remove_participant' method='post' id="delSub" >
                <?php foreach ($all_users as $user): ?>
                    <?php if($Tricount->subscribes($user)): ?>
                        <div class="container-fluid border rounded d-flex justify-content-between mb-1">
                            <p><?php echo $user->full_name ?></p>
                            <?php if (!$Tricount->implicates($user)):
                                if(!$Tricount->has_created_by($user)):
                                    ?>

                                    <input type='text' name='id_participant' value='<?= $user->id ?>' hidden>
                                    <input type='text' name='id_tricount' value='<?= $Tricount->id ?>' hidden>
                                    <button type="submit" class="btn btn-primary btnDelete" form="delSub">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>
                                <?php endif;
                            endif; ?>
                        </div>
                    <?php endif;?>
                <?php endforeach; ?>
            </form>
        </div>
        <div class="mt-4"><p>--Add a new Subscriber--</p></div>
        <form action="Tricount/add_participant/ " id="saveSub" method="post">
            <div class="input-group mb-3 prout">
                <input type="hidden" name="id_tricount" id="id_tricount" value="<?= $Tricount->id?>">
                <select class="form-select" aria-label="Default select example" name="user_sub[]" id="selectSub">
                    <?php foreach ($all_users as $user): ?>
                        <?php if(!$Tricount->subscribes($user)): ?>
                            <option value="<?= $user->id?>" ><?php echo $user->full_name?></option>
                        <?php endif;?>
                    <?php endforeach; ?>
                </select>
                <div class="input-group-prepend">
                    <input class="btn btn-primary" type="submit" id="ajaxAddParticipant" value="Add">
                </div>
            </div>
        </form>
    </div>


    <div class="container mt-5 d-flex flex-column mb-5">
        <!-- Si on utilise pas la modale le lien php est désactivé-->
            <a href="tricount/delete/<?php echo $Tricount->id;?>" id="action_delete" class="btn btn-danger btn-lg btn-block mb-5">Delete this tricount</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>

