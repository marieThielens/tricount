<?php
require_once 'model/Member.php';
require_once 'framework/View.php';
require_once "framework/Controller.php";
require_once "model/Member.php";
require_once "controller/MyController.php";
require_once "controller/ControllerTricount.php";
require_once  "controller/ControllerUser.php";

class ControllerMain extends MyController {
    public function index() : void {
        // si l'utilisateur est connecté redirige vers ses tricounts
        if($this->user_logged()) { // fonction qui vient de framework/Controller
            $this->redirect( "tricount", "index"); // controller, méthode dans le controlleur
        } else { // sinon va vers la page d'acceuil
            $errors = [];
            (new View("login"))->show(['errors' => $errors]);
        }
    }

    public function mail_available_service() : void {
        $member = $this->get_user_or_false();
        $res = "true";
        if(isset($_POST["email"]) && $_POST["email"] !== ""){
            $member = Member::email_exist($_POST["email"]);
            if($member !== false){
                $res = "false";
            }

        }
        echo $res;
    }

    public function correct_password_for_service() : void{
        $member = $this->get_user_or_false();
        $res = "false";
        if(isset($_POST["pass"]) && $_POST["pass"] !== ""){
            $errors = Member::validate_login($member->mail,$_POST["pass"]);
            if(empty($errors)){
                $res = "true";
            }
        }
        echo $res;
    }

    // gestion de la connexion d'un utilisateur
    public function login() : void {
        $mail = "";
        $password = "";
        $role = "admin";
        $errors = [];
        $form_input = [];
        if(isset($_POST["mail"]) && isset($_POST["password"])) {
            $mail = $_POST["mail"];
            $password = $_POST['password'];
            $errors = Member::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user(Member::get_member_by_mail($mail));
            }
            else{
                $form_input['mail'] = $_POST["mail"];
                $form_input['password'] = $_POST["password"];
            }
        }
        (new View("login"))->show(['errors' => $errors,'form_input' => $form_input]);
    }

    // gestion du changement de mot de passe
    public function change_password() : void {
        $member = $this->get_user_or_redirect();
        $currentPassword= "";
        $newPassword= "";
        $confirmNewPassword = "";
        $errors=[];
/*        var_dump($member->hashed_password);*/
        if(isset($_POST['currentPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
            $currentPassword = $_POST['currentPassword'];
            $newPassword = $_POST['newPassword'];
            $confirmNewPassword = $_POST['confirmNewPassword'];

            // Vérifier que md1 et 2 est le même
            $errors = Member::validate_passwords($confirmNewPassword,$newPassword);
            // récupérer l'user connecté
            // reagrde le mot de passen entré comparé au mot de passe haché
            if (!Member::check_password($currentPassword, $member->hashed_password)) {
                //$errors = array_merge($errors, (array)"Wrong password. Please try again.");
                $errors['password_incorrect'] = "Wrong password. Please try again.";
            }
            // Hacher le nouveau mot de passe
            $newPassword = Tools::my_hash($newPassword);
            // mettre à jour le mot de passe dans la db

            // Vérifier que le nouveau mot de passe est différent de l'ancien mot de passe
            if($newPassword === $currentPassword || $currentPassword === $confirmNewPassword) {
                //$errors = array_merge($errors, (array) "The new password must be different from the old one.");
                $errors['password_different'] = "The new password must be different from the old one.";
            }
            if(empty($errors)) {
                //Azerty2010,

                //$errors = array_merge($errors, Member::updatePassword( $member->id, $newPassword));
                $new_member=new Member($member->mail,
                                        $newPassword,
                                        $member->full_name,
                                        $member->role,$member->iban,
                                        $member->id);

                $new_member->persist(); // sauvegarder l'user
                $this->log_user($new_member,"User"); //
            }
            else{
                $form_input['original'] = $_POST['currentPassword'];
                $form_input['new'] = $_POST['newPassword'];
                $form_input['confirm'] = $_POST['confirmNewPassword'];
            }
        }
        (new View("change_password"))->show(['errors'=>$errors,'form_input'=>$form_input]);
    }

    // gestion de l'inscription d'un utilisateur
    public function signup() : void {
        $userFullName="";
        $userPassword="";
        $confirmUserPassword="";
        $userEmail="";
        $userIban="";
        $errors=[];
        $errors_pass = "";

        if(isset($_POST['userFullName']) && isset($_POST['userPassword']) && isset($_POST['confirmUserPassword'])
            && isset($_POST['userEmail'])&& isset($_POST['userIban'])){
            //Functions pour nettoyer les inputs à developper
            $userFullName=($_POST['userFullName']);
            $userPassword=$_POST['userPassword'];
            $confirmUserPassword=$_POST['confirmUserPassword'];
            $userEmail=$_POST['userEmail'];
            $userIban=$_POST['userIban'];

            $userPasswordHashed=Tools::my_hash($userPassword);
            $new_member = new Member($userEmail,$userPasswordHashed,$userFullName,null,$userIban,null);
            $errors = $new_member->validate();
            $errors_pass=Member::validate_passwords($userPassword,$confirmUserPassword);

            if(empty($errors) && empty($errors_pass)){
                // Enlever les espaces vide
                $userIban = strtolower(str_replace(' ','', $userIban));
                $new_member->iban = $userIban;
                $new_member->persist();
                $this->log_user($new_member);
                self::redirect("Tricount","index");
               // (new View("tricounts"))->show();
            }
        }
        (new View("signup"))->show(['userFullName'=>$userFullName,
            'userIban'=>$userIban,'userPassword'=>$userPassword,'confirmUserPassword'=>$confirmUserPassword,
            'userEmail'=>$userEmail,'errors'=>$errors,"errors_pass"=>$errors_pass]);
    }
    // gestion de la modification du profile
    public function edit_profil() : void {
        //TODO: Implementer le persiste de member pour regrouper ADD et UPDATE MEMBER
        $member = $this->get_user_or_redirect();
        $errors = [];
        $success = "";
        $userEmail = "";
        $userFullName = "";
        $userIban = "";
        $form_input = [];
        if(isset($_POST['userEmail']) &&
            isset($_POST['userFullName'])  &&
            isset($_POST['userIban'])
        ) {

            $member = Member::get_member_by_id($member->id );
            if($member){
                $iban = strtolower(str_replace(' ','', $userIban));
                $member = new Member($_POST['userEmail'],
                                    $member->hashed_password ,
                                    $_POST['userFullName'],
                                    null,
                                    $_POST['userIban'],
                                    $member->id );
                $errors = $member->validate();
                if(empty($errors)) {
                    $member->persist();
                    $this->log_user($member,'User');
                    // il faut ajouter un persist

                }
                else{
                    $form_input['email'] = $_POST['userEmail'];
                    $form_input['name'] = $_POST['userFullName'];
                    $form_input['iban'] = $_POST['userIban'];
                }
            }

        }
        (new View("edit_profil"))->show(['errors'=>$errors,
                                                'form_input' => $form_input,
                                                "success" => $success,
                                                "currentUser"=>$member]);
    }
}