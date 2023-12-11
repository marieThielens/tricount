<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <style>
            .errors {
                color: red !important;
            }
        </style>
    </head>
    <body>
        <!-- p = padding, my = margin-->
        <header class="container-fluid d-flex p-3 my-3 text-white bleuClair">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gem" viewBox="0 0 16 16">
                <path d="M3.1.7a.5.5 0 0 1 .4-.2h9a.5.5 0 0 1 .4.2l2.976 3.974c.149.185.156.45.01.644L8.4 15.3a.5.5 0 0 1-.8 0L.1 5.3a.5.5 0 0 1 0-.6l3-4zm11.386 3.785-1.806-2.41-.776 2.413 2.582-.003zm-3.633.004.961-2.989H4.186l.963 2.995 5.704-.006zM5.47 5.495 8 13.366l2.532-7.876-5.062.005zm-1.371-.999-.78-2.422-1.818 2.425 2.598-.003zM1.499 5.5l5.113 6.817-2.192-6.82L1.5 5.5zm7.889 6.817 5.123-6.83-2.928.002-2.195 6.828z"/>
              </svg>
            <h1>Tricount</h1>
        </header>

        <!-- container permet que ça ne colle pas au bord. d'avoir des margins, border= entourer de gris-->
        <div class="main container border my-auto">
            <h2 class="title text-center mt-4">Sign In</h2>
            <hr>

            <!-- controller main, méthode login-->
            <form action="main/login" method="post">
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
                      <input type="mail" name="mail" value="<?= (isset($form_input['mail'])) ? $form_input['mail'] : " " ?>" class="form-control" placeholder="mail@gmail.com">
                    </div>
                    <?= (isset($errors['member_missing'])) ? "<p class='errors' >".$errors['member_missing']."</p>" : "" ?>
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
                          <input type="password" value="<?= (isset($form_input['password'])) ? $form_input['password'] : ""?>" name="password" class="form-control" placeholder="*********">
                        </div>
                        <?= (isset($errors['password_wrong'])) ? "<p class='errors' >".$errors['password_wrong']."</p>" : ""?>
                    </div>
                </div>
                <!--btn-primary: couleur bleue, col12 pour prendre toute la place du parent -->
                <button type="submit" class="btn btn-primary col-12">Login</button>
            </form>

            <!-- d-flex justify-content-center pour centrer la div, mt= margin top -->
            <div class="d-flex justify-content-center mt-4 mb-4">
                <a href="main/signup" class="styleLink link-primary text-center">Now here ? Click here to join the party
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-sunglasses" viewBox="0 0 16 16">
                        <path d="M4.968 9.75a.5.5 0 1 0-.866.5A4.498 4.498 0 0 0 8 12.5a4.5 4.5 0 0 0 3.898-2.25.5.5 0 1 0-.866-.5A3.498 3.498 0 0 1 8 11.5a3.498 3.498 0 0 1-3.032-1.75zM7 5.116V5a1 1 0 0 0-1-1H3.28a1 1 0 0 0-.97 1.243l.311 1.242A2 2 0 0 0 4.561 8H5a2 2 0 0 0 1.994-1.839A2.99 2.99 0 0 1 8 6c.393 0 .74.064 1.006.161A2 2 0 0 0 11 8h.438a2 2 0 0 0 1.94-1.515l.311-1.242A1 1 0 0 0 12.72 4H10a1 1 0 0 0-1 1v.116A4.22 4.22 0 0 0 8 5c-.35 0-.69.04-1 .116z"/>
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-1 0A7 7 0 1 0 1 8a7 7 0 0 0 14 0z"/>
                      </svg>
                </a>
            </div>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>