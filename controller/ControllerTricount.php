<?php
require_once 'model/Member.php';
require_once 'model/Tricount.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'controller/MyController.php';


class ControllerTricount extends MyController {

    //page d'accueil.
    public function index() : void{
        $member = $this->get_user_or_redirect(); // vient de myConrtoller
        $author = $this->get_author($member); // celui qui a crée le tricounts
        $tricounts = $author->get_tricounts(); // récupérer les tricounts
        $participants =[];

        foreach($tricounts as $t) {
            $participants = $t->get_number_participants();
        }
        (new View("tricounts"))->show(["tricounts" => $tricounts, // var pour l'html => var du contoller
                                             "member" => $member,
                                             "participants" => $participants]);
    }

    private function get_author(Member $user) :Member|false {
        if (!isset($_GET["param1"]) || $_GET["param1"] == "") {
            return $user;
        } else {
            return Member::get_member_by_mail($_GET["param1"]);
        }
    }
    public function add_tricount() : void {
        // Récupération de la valeur de la configuration
        $justValidate = Configuration::get('just_validate');
        $member = $this->get_user_or_redirect(); // récupérer le membre
        (new View("add_tricount"))->show(['justValidate' => $justValidate]);
    }

    public function view_tricount() :void{
        $member = $this->get_user_or_redirect();
        $author = $this->get_author($member); // celui qui a crée le tricounts
        $total=0;
        $Expenses=0;
        $tricounts_id=-1;
        $tricount=null;
        $listOperations=[];
        $form_input = [];
        if (isset($_GET["param1"])){
            $tricounts_id = $_GET["param1"];

            if(!is_numeric($tricounts_id ) ){
                $errors[]="pas un chiffre";
                self::error_view($errors);
            }
            //Verifier que le tricount exist
            //verifier que l'utilisateur connecté à le droit de le consulter
            $tricount =Tricount::get_by_id($tricounts_id);
            if($tricount === false){
                Tools::abort("Tricount doesn't exist.");
            }
            if(!$member->has_access_to_tricount($tricount)){
                $errors[]="Vous n'avez pas accès au tricount";
                self::error_view($errors);
            }
                //Utiliser nbParticipants et nbOperation avec Objects dans la vue pour alléger le controller
                $participants=$tricount->nb_participants();
                $operations=$tricount->nb_operations();
                if($operations!=0 && $participants!=0)
                    $listOperations=$tricount->get_operations();
                    $Expenses=$tricount->total_expenses();
                    $total = $member->my_total($tricount);
            // }
        }
        else{
            Tools::abort("Pas de id tricount");
        }//Page erreurs

         // celui qui a crée le tricounts
        (new View("tricount"))->show([//'participants'=>$participants,
                                            //'operations'=>$operations,
                                            'total'=>$total,
                                            'Expenses'=>$Expenses,
                                            'Tricount'=>$tricount,
                                            'listOperations'=>$listOperations,
                                            'id' =>$tricounts_id,
        ]);
    }

    public function add_tricount_bd() :void{
        $justValidate = Configuration::get('just_validate');
        $form_input = [];
        $member = $this->get_user_or_redirect();
        $errors=[];
        $newTricount = new Tricount("","", new Member(""));
        if(isset($_POST) && $_POST != ""){
            if(isset($_POST["titleTricount"])  &&
                isset($_POST["descriptionTricount"]) ){

                $newTricount=new Tricount($_POST["titleTricount"], date('d-m-y h:i:s'),$member,$_POST["descriptionTricount"]);
                //Attention: il faut passer le member pour verifier
                // qu'un membre ne possède pas deux fois le même nom de tricount
                //TODO : refactor de la méthode validate
                $errors = $newTricount->validate($_POST["titleTricount"]);

                if(empty($errors)){
                    $newTricount = $newTricount->persist();
                    // ajouter un abonné à un tricount existant
                    $array [0] = $member->id;
                    $newTricount->add_subscribers($array);
                    self::redirect("Tricount","view_tricount",$newTricount->id);
                }
                else{
                    $form_input['title'] = $_POST['titleTricount'];
                    $form_input['description'] = $_POST['descriptionTricount'];
                }


            }
        }
        (new View("add_tricount"))->show(['tricount' => $newTricount ,
                                                'form_input' => $form_input,
                                                'errors'=> $errors,
                                                'justValidate' => $justValidate]);
    }

    public function edit_tricount(): void {
        $member = $this->get_user_or_redirect();
        /*Récupérer le tricount séléctionner
        *mettre a jour la description
        */
        $tricount=null;
        $form_input = [];
        $errors = [];
        $id_tricount =-1;
        //$author = $this->get_author($member); // celui qui a crée le tricounts

        if(!is_numeric($_GET["param1"]) ){
            Tools::abort("The url is invalid, parameter must be string");
        }
        $tricount=Tricount::get_by_id($_GET["param1"]);
        if ($tricount === false){
            Tools::abort("Tricount doesn't exist");
        }
        if(!$member->has_access_to_tricount($tricount)){
            $errors[]="Vous dont'have the right to acces to this tricount";
            self::error_view($errors);
        }
        $all_users=Member::get_all();



        if(isset($_POST) && $_POST != "" ){
            if(isset($_POST["id_tricount"]) && $_POST["id_tricount"] != "" &&
                isset($_POST['titleTricount']) && $_POST['titleTricount'] != "" &&
                isset($_POST['descriptionTricount']) ){

                $update_tricount = new Tricount($_POST['titleTricount'],
                                                $tricount->createdAt,
                                                $tricount->creator,
                                                $_POST['descriptionTricount'],
                                                $tricount->id);
                $errors = $update_tricount->validate();

                if(empty($errors)){
                    $update_tricount->persist();
                    if(isset($_POST['user_sub']) && !empty($_POST['user_sub']) && $_POST['user_sub'][1]){
                        // ajouter un abonné à un tricount existant
                        $array=array_map('intval',$_POST['user_sub']);
                        $update_tricount->add_subscribers($array);
                    }
                    self::redirect('tricount', 'view_tricount', $update_tricount->id );
                }
                else{
                    $form_input['title'] = $_POST['titleTricount'];
                    $form_input['description'] = $_POST['descriptionTricount'];
                }
            }

        }
        (new View("edit_tricount"))->show(["Tricount" => $tricount,
                                                 "form_input" => $form_input,
                                                 "errors" => $errors,
                                                 "all_users" => $all_users]);
    }
    public function delete(): void
    {
        $member=$this->get_user_or_redirect();

        if (isset($_GET["param1"])) {
            $id_tricount = $_GET["param1"];
            $tricount = Tricount::get_by_id($id_tricount);
            if ($tricount === false){
                Tools::abort("Tricount doesn't exist");
            }
            if(!$member->has_access_to_tricount($tricount)){
                Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
            }
            if (isset($_POST["delete"])){
                $this->deleted($tricount);
                self::redirect("tricount");
            }
            (new View("delete_tricount"))->show(["tricount"=>$tricount]);

        }
        else
            Tools::abort("Pas d'ID");

    }



    /**
     *
     * @param {}
     * @return {bool}
     */
    private function deleted(Tricount $tricount): bool {
        $member = $this->get_user_or_redirect();
        if(!$member->has_access_to_tricount($tricount)){
            Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
        }
        if($tricount) {
            $tricount->delete();
            return true;
        }

        return false;
    }
    public function delete_service(){
        $member = $this->get_user_or_false();

            if(isset($_POST["tricount"]) && $_POST["tricount"] !== ""){
            $tricount = Tricount::get_by_id($_POST["tricount"]);
            if(!$member->has_access_to_tricount($tricount)){
                Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
                }
            if($tricount !== false){
                 if($tricount->delete()){
                    echo "true";
                    return;
                }
            }
            }
             echo "false";
    }

    public function balance() : void {
        $member = self::get_user_or_redirect();
        $participants = [];

        //On doit récupérer un tableau de membre et le tricount
        $balance_per_participant = [];
        $tricount = null;
        if(isset($_GET['param1']) && $_GET['param1'] != ""){
            $idTricount = $_GET['param1'];
            $tricount = Tricount::get_by_id($idTricount);
            if(!$member->has_access_to_tricount($tricount)){
                Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
            }
            $participants = $tricount->get_Participants();
        }
        (new View("balance"))->show(["tricount" => $tricount,
                                            "participants"=>$participants,
                                            "balance_per_participant"=>$balance_per_participant]);
    }
    public function remove() :void{
        $member = $this->get_user_or_false();
        $this->remove_participant();
    }

    public function add_participant() :void{
        $member = $this->get_user_or_false();
        $errors = [];

        // Si la valeur est vide
/*        if(isset($_POST['user_sub']) && !empty($_POST['user_sub'])) {
            // La valeur est vide, donc on redirige l'utilisateur vers la page edit_tricount
            $errors[] = "Not a person";
        }*/

  if(isset($_POST['user_sub']) && isset($_POST['id_tricount'])){

            $id_tricount = $_POST['id_tricount'];
            $tricount = Tricount::get_by_id($id_tricount);
            $users_sub = $_POST['user_sub'];
            if(!$member->has_access_to_tricount($tricount)){
                Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
            }

            foreach ( $users_sub as $user_sub){
                $memb = Member::get_member_by_id($user_sub);

                if($memb != false && !empty($_POST['user_sub'])){
                    $tricount->add_perticipant($memb);
                }
            }
/*      if(!empty($errors)) {
          $error_message = implode(", ", $errors);
          $_SESSION['flash'] = $error_message;
      }*/
            self::redirect("tricount","edit_tricount",$tricount->id);
        }
    }

    public function remove_participant() :void{
        $member = self::get_user_or_false();
        //if(!$member->has_access_to_tricount())
        if(isset($_POST['id_tricount']) && $_POST['id_tricount'] != ""){
            if (isset($_POST['id_participant']) && $_POST['id_participant'] != ""
            ) {
                $id_tricount = $_POST['id_tricount'];
                $tricount = Tricount::get_by_id($id_tricount);
                if(!$member->has_access_to_tricount($tricount)){
                    Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
                }
                $id_participant = $_POST['id_participant'];
                $member = Member::get_member_by_id($id_participant);
                if($tricount->subscribes($member)){
                    $tricount->remove_perticipant($member);
                }
                self::redirect("tricount","edit_tricount",$tricount->id);
            }
        }
        self::redirect("tricount","edit_tricount",$tricount->id);
    }

    public function subscription_service() : bool {
        $member = self::get_user_or_false();

        if(isset($_POST["monTricount"]) && $_POST["monUser"] !== "") {
            $user = Member::get_By_Id($_POST["monUser"]);
            $tricount = Tricount::get_by_id($_POST['monTricount']);
            if($user === false){
                echo json_encode(["success" => false]);
                exit;
            }
            if($tricount === false){
                echo json_encode(["success" => false]);
                exit;
            }
            if(!$member->has_access_to_tricount($tricount)){
                echo json_encode(["success" => false]);
                exit;
            }
            else {
                //$res = "true";
                $tricount->add_perticipant($user);
                echo json_encode(["success" => true]);
                exit;

            }
        }
        echo json_encode(["success" => false]);
        exit;
    }
    public function delete_subscription_service() : bool {
        $member = self::get_user_or_false();

        if(isset($_POST["monTricount"]) && $_POST["monUser"] !== "") {
            $tricount_id = $_POST["monTricount"];
            $user_id = Member::get_By_Id($_POST["monUser"]);
            $tricount = Tricount::get_by_id($tricount_id);
            if($user_id === false){
                echo json_encode(["success" => false]);
                exit;
            }
            if($tricount === false){
                echo json_encode(["success" => false]);
                exit;
            }
            if(!$member->has_access_to_tricount($tricount)){
                echo json_encode(["success" => false]);
                exit;
            }
            else {
                //$res = "true";

                $tricount->remove_perticipant($user_id);
                echo json_encode(["success" => true]);
                exit;

            }
        }
        echo json_encode(["success" => false]);
        exit;
    }

    public function tricount_exists_service() :void {
        $member = self::get_user_or_false();
        $res = "false";
        if(isset($_POST["titre"]) && $_POST["titre"] !== "" && $member !== false){
            if(isset($_POST["id"]) && $_POST["id"] !== ""){
                if(Tricount::already_exiting_title_service(($_POST["titre"]),$member,$_POST["id"])){
                    $res =  "true";
                }
            }
            else{
                if(Tricount::already_exiting_title_service(($_POST["titre"]),$member)){
                    $res =  "true";
                }
            }



        }
        echo $res;

     }


    private function error_view(array $errors):void{
        (new View("error"))->show(["errors" => $errors]);
    }

}