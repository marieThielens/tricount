<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add tricount</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="lib/jquery-3.6.3.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <!-- justValidate-->
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>

    <style>
        .errors {
            color: red !important;
        }
        .success {
            color: green !important;
        }
    </style>

    <script>

        let titre, description, reponse_title,reponse_description,lenght_title, btn_add, valid_title, valid_description, form_add;
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
        <?php endif ?>

        $(function (){
            <?php if (Configuration::get('modale') === 'on'): ?>
            confirm_cancel();
            $("#cancelLink").attr("href","javascript:confirm_back()");
            <?php endif ?>

            /* ---- Désactivation des erreurs php ----*/
            let phpErrors = document.querySelectorAll('.errors');
            phpErrors.forEach((error) => {
                error.style.display = 'none';
            });

            <?php if (Configuration::get('just_validate') === 'off'): ?>

            titre = $("#titleTricount");
            description = $("#descriptionTricount");
            form_add = $("#formAdd");
            reponse_title = $("#validation_title");
            reponse_description = $("#validation_description");
            valid_title =false;
            valid_description = true;
            btn_add = $("#btnAdd");

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
                    uniqueTitle($(this).val(),reponse_title);
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
                const response = await $.post("tricount/tricount_exists_service/",{titre : string},null,'json');
                field.addClass("text-danger"); // que le texte soit en rouge
                if(response){
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


            <?php else: ?>

            /*---------------JustValidate-----------------*/

            let formAdd = document.getElementById('formAdd');
            reponse_title = $("#validation_title");
            let titreAvailable;

            const validator = new JustValidate("#formAdd", {
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
                .onValidate(async function(event) {
                     titreAvailable = await $.post("tricount/tricount_exists_service/", {titre: $("#titleTricount").val()}, null, 'json');
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

                ] , { successMessage : 'Looks good !'});
            // Pour envoyer le formulaire formulaire ----------

            formAdd.addEventListener('submit', async (event) => {

                event.preventDefault(); // empecher l'envoi du formulaire
                validator.validate(); // vérifier les inputs

                // const isValidTitle = await validateTitle();
                if(validator.isValid ) { // && isValidTitle
                    formAdd.submit();
                }
            });
            async function validateTitle() {
                if (!valid_title) {
                    reponse_title.html("Please enter a valid title.");
                    return false;
                }
                return true;
            }
            <?php endif;  ?>
        });


    </script>

</head>
<body>

<!-- p = padding, my = margin-->
<header class="container-fluid d-flex p-3 mb-4 justify-content-between text-secondary bleuClair">
    <form action="tricount/index" class="mt-2">
        <button type="submit" id="cancelLink" class="btn btn-outline-danger">Cancel</button>
    </form>
    <h1>Tricounts ▷ Add</h1>
        <button type="submit" id="btnAdd" class="btn btn-primary" form="formAdd" >Save</button>
</header>

<div class="container" >

    <form action="tricount/add_tricount_bd" method="post" id="formAdd">
        <div class="form-group">
            <label for="titleTricount">Title :</label>
            <input type="text" class="form-control input" id="titleTricount" name="titleTricount" placeholder="Week-end à Paris" value="<?= isset($form_input['title']) ? htmlspecialchars($form_input['title']) : ""; ?>">
            <p id="validation_title"></p>
        </div>
        <!-- Afficher l'erreur -->
        <?= (isset($errors['title_empty'])) ? "<p class='errors' >".$errors['title_empty']."</p>" : ""?>
        <?= (isset($errors['title_size'])) ? "<p class='errors' >".$errors['title_size']."</p>" : ""?>
        <?= (isset($errors['title_unique'])) ? "<p class='errors' >".$errors['title_unique']."</p>" : ""?>
        <div class="form-group mt-2">
            <label for="descriptionTricount">Description(optinal) :</label>
            <textarea class="form-control" id="descriptionTricount" name="descriptionTricount" row="5" ><?= isset($form_input['description']) ? htmlspecialchars($form_input['description']) : ""; ?></textarea>
            <p id="validation_description"></p>
        </div>
        <!-- Afficher l'erreur -->
        <?= (isset($errors['description_size'])) ? "<p class='errors' >".$errors['description_size']."</p>" : ""?>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>