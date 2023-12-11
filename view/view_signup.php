<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <script src="lib/jQuery/jquery-3.6.4.js" type="text/javascript"></script>
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
            $(function() {
                // enlever les erreurs php pour ne pas avoir de doublons
                let phpErrors = document.querySelectorAll('.errors');
                phpErrors.forEach((error) => {
                    error.style.display = 'none';
                });

                const validation = new JustValidate('#signupForm', {
                    validateBeforeSubmitting: true, // déclencher les validations à chaque frappe
                    lockForm: true, // verrouille le formulaire pour asynchrone
                    focusInvalidField: false, // pour pas garder le focus le le 1 champ d'erreur
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errors'],
                    errorLabelStyle: undefined
                });
                validation
                    .addField('#userMail', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value : 3,
                            errorMessage: 'Minimum 3 characters'
                        },
                        {
                            rule: 'maxLength',
                            value : 36,
                            errorMessage: 'Maximum 16 characters'
                        },
                    ], { successMessage : 'Looks good !'})
                    .addField('#password', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value : 8,
                            errorMessage: 'Minimum 8 characters'
                        },
                        {
                            rule: 'maxLength',
                            value : 16,
                            errorMessage: 'Maximum 16 characters'
                        },
                    ], { successMessage : 'Looks good !'})
                    .addField('#password_confirm', [
                        {
                            rule: 'required',
                            errorMessage: 'Field is required'
                        },
                        {
                            rule: 'minLength',
                            value : 8,
                            errorMessage: 'Minimum 8 characters'
                        },
                        {
                            rule: 'maxLength',
                            value : 16,
                            errorMessage: 'Maximum 16 characters'
                        },
                    ], { successMessage : 'Looks good !'})

                    .onValidate(async function(event) {
                        const email = $("#userMail").val();
                        const mailAvailable = await $.post("main/mail_available_service/" , {email: email} , null, 'json' );
                        console.log(mailAvailable);
                        if (!mailAvailable)
                            this.showErrors({ '#userMail': 'Mail is already taken' });
                    }, { successMessage : 'Looks good !'})
                    .onSuccess(function(event) {
                        event.target.submit(); //par défaut le form n'est pas soumis
                    });

                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <!-- p = padding, my = margin-->
        <header class="container d-flex p-3 my-3 bleuClair text-white ">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gem" viewBox="0 0 16 16">
                <path d="M3.1.7a.5.5 0 0 1 .4-.2h9a.5.5 0 0 1 .4.2l2.976 3.974c.149.185.156.45.01.644L8.4 15.3a.5.5 0 0 1-.8 0L.1 5.3a.5.5 0 0 1 0-.6l3-4zm11.386 3.785-1.806-2.41-.776 2.413 2.582-.003zm-3.633.004.961-2.989H4.186l.963 2.995 5.704-.006zM5.47 5.495 8 13.366l2.532-7.876-5.062.005zm-1.371-.999-.78-2.422-1.818 2.425 2.598-.003zM1.499 5.5l5.113 6.817-2.192-6.82L1.5 5.5zm7.889 6.817 5.123-6.83-2.928.002-2.195 6.828z"/>
              </svg>
            <h1>Tricount</h1>
        </header>

        <!-- container permet que ça ne colle pas au bord. d'avoir des margins, border= entourer de gris-->
        <div class="main container border my-auto">
            <h2 class="title text-center mt-4">Sign Up</h2>
            <hr>

            <!-- controller main, méthode login-->
            <form action="main/signup" method="post" id="signupForm">
                <div class="input-group mb-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">@</div>
                      </div>
                      <input type="text" class="form-control" id="userMail" placeholder="Email" value="<?= $userEmail ?>" name="userEmail">
                    </div>
                    <?= (isset($errors['email_format'])) ? "<p style='color: red'>".$errors['email_format']."</p>" : ""?>
                    <?= (isset($errors['email_empty'])) ? "<p style='color: red'>".$errors['email_empty']."</p>" : ""?>
                    <?= (isset($errors['email_unique'])) ? "<p style='color: red'>".$errors['email_unique']."</p>" : ""?>
                  </div>
                <!-- col-auto pour que les deux soient collés -->
                <div class="col-auto">
                    <!--mb-2 car c'est 2 colonnes-->
                    <div class="input-group mb-2">
                      <div class="input-group-prepend">
                        <!-- icone avatar-->
                        <div class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                            </svg>
                        </div>
                      </div>
                      <input type="text" class="form-control" id="userName" placeholder="Full name" value="<?= $userFullName ?>" name="userFullName">
                    </div>
                    <?= (isset($errors['name_empty'])) ? "<p style='color: red'>".$errors['name_empty']."</p>" : ""?>
                    <?= (isset($errors['name_size'])) ? "<p style='color: red'>".$errors['name_size']."</p>" : ""?>
                    <div class="col-auto">
                        <!--mb-2 car c'est 2 colonnes-->
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <!-- icone avatar-->
                            <div class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-credit-card-2-back-fill" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5H0V4zm11.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-2zM0 11v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1H0z"/>
                                </svg>
                            </div>
                          </div>
                          <input type="text" class="form-control" id="iban" placeholder="IBAN" value="<?= $userIban ?>" name="userIban">
                        </div>
                        <?= (isset($errors['iban_format'])) ? "<p style='color: red'>".$errors['iban_format']."</p>" : ""?>

                    <div class="col-auto">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <!-- icone bag-->
                            <div class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5z"/>
                                  </svg>
                            </div>
                          </div>
                          <input type="password" class="form-control" id="password" placeholder="Password" value="<?= $userPassword ?>" name="userPassword">
                        </div>
                        <?= (isset($errors_pass['password_length'])) ? "<p style='color: red'>".$errors_pass['password_length']."</p>" : ""?>
                        <?= (isset($errors_pass['password_format'])) ? "<p style='color: red'>".$errors_pass['password_format']."</p>" : ""?>
                    </div>
                    <div class="col-auto">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <!-- icone bag-->
                            <div class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5z"/>
                                  </svg>
                            </div>
                          </div>
                          <input type="password" class="form-control" id="password_confirm" placeholder="Confirm your password" value="<?= $confirmUserPassword ?>" name="confirmUserPassword">
                        </div>
                        <?= (isset($errors_pass['password_same'])) ? "<p style='color: red'>".$errors_pass['password_same']."</p>" : ""?>
                    </div>
                </div>
                </div>
                <!--btn-primary: couleur bleue, col12 pour prendre toute la place du parent -->
                <button type="submit" value="Sign Up" class="btn btn-primary col-12 mt-2">Sign Up</button>
                <a href="Main/index" role="button" class="btn btn-outline-danger col-12 mt-2">Cancel</a>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>