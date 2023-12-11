<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password</title>
    <base href="<?= $web_root ?>"/>
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="lib/jQuery/jquery-3.6.4.js" type="text/javascript"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <style>
        .errors {
            color: red !important;
        }
        .success {
            color: green !important;
        }
    </style>
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
                        window.location.href = event.target.href;
                    }
                });
            });
        }
        <?php endif;  ?>

        $(document).ready(function () {
            <?php if (Configuration::get('modale') === 'on'): ?>
            confirm_cancel();
        <?php endif;  ?>
            <?php if (Configuration::get('just_validate') === 'on'): ?>
            let valid_pass;
            let phpErrors = document.querySelectorAll('.errors');
            phpErrors.forEach((error) => {
                error.style.display = 'none';
            });

            const validator = new window.JustValidate('#change_password',{
                validateBeforeSubmitting: true,
                lockForm: true,
                focusInvalidField: false,
                successLabelCssClass: ['success'],
                errorLabelCssClass: ['errors'],
                errorLabelStyle: undefined
            });
            validator
                .addField('#currentPassword',[
                    {
                        rule: 'required'
                    },
                    ],{ successMessage : 'Looks good !'})
                .addField('#newPassword', [
                    {
                        rule: 'required',
                    },
                    {
                        rule: 'minLength',
                        value: 8,
                    },
                    {
                        rule: 'customRegexp',
                        value : /^(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,}$/,
                        errorMessage: 'Must be at least 8 characters long, must contain at least one number, one upper case letter and one non-alphanumeric character.'
                    },{
                        validator: (value) => {
                            let original_password = $("#currentPassword").val();
                            return original_password !== value;
                        },
                        errorMessage: 'Passwords shouldn\'t be the same than the old one',
                    },
                ],{ successMessage : 'Looks good !'})
                .addField('#confirmNewPassword', [
                {
                    rule: 'required',
                },
                {
                    validator: (value, fields) => {
                        let original_password = $("#currentPassword").val();
                        if (
                            fields['#newPassword'] &&
                            fields['#newPassword'].elem
                        ) {
                            const repeatPasswordValue =
                                fields['#newPassword'].elem.value;

                            return value === repeatPasswordValue && original_password !== value;
                        }

                        return true;
                    },
                    errorMessage: 'Passwords should be the same',
                },
            ],{ successMessage : 'Looks good !'})
            .onSuccess(function(event) {
                event.target.submit(); //par défaut le form n'est pas soumis
            });
            <?php endif;  ?>
        });
    </script>
    <style>
        .errors {
            color: red !important;
        }
        .success {
            color: green !important;
        }
    </style>
</head>
<body>
<header class="container d-flex p-3 my-3 bleuClair text-white ">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gem" viewBox="0 0 16 16">
        <path d="M3.1.7a.5.5 0 0 1 .4-.2h9a.5.5 0 0 1 .4.2l2.976 3.974c.149.185.156.45.01.644L8.4 15.3a.5.5 0 0 1-.8 0L.1 5.3a.5.5 0 0 1 0-.6l3-4zm11.386 3.785-1.806-2.41-.776 2.413 2.582-.003zm-3.633.004.961-2.989H4.186l.963 2.995 5.704-.006zM5.47 5.495 8 13.366l2.532-7.876-5.062.005zm-1.371-.999-.78-2.422-1.818 2.425 2.598-.003zM1.499 5.5l5.113 6.817-2.192-6.82L1.5 5.5zm7.889 6.817 5.123-6.83-2.928.002-2.195 6.828z"/>
    </svg>
    <h1>Tricount</h1>
</header>

<div class="container d-flex justify-content-center">
    <div class="col-12 col-sm-6 mb-3">
        <form action="main/change_password" method="post" id="change_password" name="change_password">
            <h2 class="title text-center mt-4">Change Password</h2>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input name="currentPassword" id="currentPassword"  class="form-control" type="password" value="<?= isset($form_input['original']) ? htmlspecialchars($form_input['original']) : ""; ?>" placeholder="••••••">
                    </div>
                    <?= (isset($errors['password_incorrect'])) ? "<p class='errors' >".$errors['password_incorrect']."</p>" : ""?>
                    <div id="errpass"></div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>New Password</label>
                        <input name="newPassword" id="newPassword" class="form-control" type="password" value="<?= isset($form_input['new']) ? htmlspecialchars($form_input['new']) : ""; ?>" placeholder="••••••">
                    </div>
                    <?= (isset($errors['password_different'])) ? "<p class='errors' >".$errors['password_different']."</p>" : ""?>
                    <?= (isset($errors['password_length'])) ? "<p class='errors' >".$errors['password_length']."</p>" : ""?>
                    <?= (isset($errors['password_format'])) ? "<p class='errors' >".$errors['password_format']."</p>" : ""?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Confirm <span class="d-none d-xl-inline">Password</span></label>
                        <input name="confirmNewPassword" id="confirmNewPassword" class="form-control" type="password" value="<?= isset($form_input['confirm']) ? htmlspecialchars($form_input['confirm']) : ""; ?>" placeholder="••••••">
                    </div>
                    <?= (isset($errors['password_same'])) ? "<p class='errors' >".$errors['password_same']."</p>" : ""?>
                </div>
            </div>
            <button type="submit" value="Sign Up" class="btn btn-primary col-12 mt-2">Confirm</button>
            <a href="User" role="button" id="cancelLink"  class="btn btn-outline-danger col-12 mt-2">Cancel</a>
        </form>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
