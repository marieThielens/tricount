<?php
require_once 'model/Member.php';
require_once 'model/Tricount.php';
require_once 'controller/ControllerTricount.php';
require_once 'model/Operation.php';
require_once 'model/Repartition.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'controller/MyController.php';
require_once 'model/Repartition.php';
class ControllerOperation extends Controller
{
    public function index(): void {}

    public function show_edit_operation(): void
    {
        /*verifier que l'utilisateur courant peux y accéder
        */
        $member = $this->get_user_or_redirect();
        $errors= [];
        $id_operation = -1;
        $operation = null;
        $list_tricount_subscribers = [];
        $participates = [];
        $valueOperation = [];
        $form_input = [];
        $result_repartition = [];
        $list_repartition = [];
        $formatted_date = "";

        $id_tricount = $_GET['param1'];
        if ( !is_numeric($id_operation)) {
            Tools::abort("The url is invalid, paramter must be string");
        }
        $operation = Operation::get_by_id($id_tricount);
        if($operation === false){
            Tools::abort("Tricount n'existe pas.");
        }
        if(!$member->has_access_to_tricount($operation->tricount)){
            $errors[]="Vous n'avez pas accès au tricount";
            $this->error_view($errors);
        }

        $list_tricount_subscribers = $operation->tricount->get_Participants();
        if (isset($_POST)) {

            if (isset($_POST['titleOperation'])  &&
                isset($_POST['amountOperation'])  &&
                isset($_POST['dateOperation']) &&
                isset($_POST['Paid_by']) && $_POST['Paid_by'] != "" )
            {
                /*if(!empty($_POST['dateOperation'])){
                    if($this->validate_date($_POST['dateOperation'])){
                        $formatted_date = $_POST['dateOperation'];
                    }
                    else{
                        $errors['date_format'] = "Use the correct a valid date format";
                        //formatted_date = date('Y/m/d');
                    }
                }*/
                $edited_operation = new Operation($_POST['titleOperation'],
                                                  $operation->tricount,
                                                  floatval($_POST['amountOperation']),
                                                  $_POST['dateOperation'],
                                                  Member::get_member_by_id($_POST['Paid_by']),
                                                  $operation->created_at,
                                                  $operation->id);
                $errors = $edited_operation->validate();

                if (isset($_POST['participates']) && !empty($_POST['participates']) ) {
                    $list_repartition = $this->creat_repartition($_POST['participates']);
                    foreach ($list_repartition as $repartition){
                        if(!$repartition->validate() && empty($errors ['repartition'])){
                            $errors ['repartition'] = "Give positif value to repartition weigth";
                        }
                    }
                }
                //Aucun participant déléctionnez pour la dépense
                else{
                    $errors = array_merge($errors);
                    $errors['fromWhom'] = "At least one participant";
                }

                if (empty($errors) ) {
                    $newOperation=$edited_operation->persist();
                    if ($newOperation->id !== null){
                        if (!empty($list_repartition)) {
                            $newOperation?->clean_repartition();
                            foreach ($list_repartition as $repartition){
                                $repartition->operation = $newOperation;
                                if($repartition->validate()){
                                    $repartition->persist();
                                }
                            }
                            $this->redirect("Operation", "show_operation",$newOperation->id);

                        }
                    }
                }
                //Erreur dans l'opération et aucun participant déléctionnez pour la dépense
                else{
                    $errors = array_merge($errors,$participates);
                    if(!isset($_POST['participates'])){
                        $errors['fromWhom'] = "At least one participant";
                    }
                    $form_input['titleOperation'] = $_POST['titleOperation'];
                    $form_input['amountOperation'] = $_POST['amountOperation'];
                    $form_input['dateOperation'] = $_POST['dateOperation'];
                    $form_input['Paid_by'] = intval($_POST['Paid_by']);

                }

            }

        }
        (new View("edit_operation"))->show(["operation" => $operation,
                                                    "form_input" => $form_input,
                                                    "list_repartition" => $list_repartition,
                                                    "errors" => $errors,
                                                    "tricount_subscribers" => $list_tricount_subscribers ]);
    }

    public function show_add_operation(): void {
        /*Récupérer le tricount séléctionner
        *verifier que l'utilisateur courant peux y accéder
        */
        $member = $this->get_user_or_redirect();
        $id_tricount = -1;
        $tricount = null;
        $list_tricount_subscribers = [];
        $errors = [];

        $operation = null;
        $newOperation= -20;
        /*$date = new DateTime();*/
        $title = null;
        $amount = null;
        $date_operation = null;
        $form_input = [];
        $result_repartition = [];
        $list_repartition = [];
        $participates = [];
        $formatted_date ="";

        $titleOperation = "";
        $amountOperation ="";
        $dateOperation = "";

        $id_tricount = $_GET['param1'];
        if(!is_numeric($id_tricount) ){
            Tools::abort("The url is invalid, paramter must be string");
        }
        $tricount = Tricount::get_by_id($id_tricount);
        if($tricount === false){
            Tools::abort("Tricount n'existe pas.");
        }
        if(!$member->has_access_to_tricount($tricount)){
            $errors[]="Vous n'avez pas accès au tricount";
            $this->error_view($errors);
        }
        $list_tricount_subscribers = $tricount->get_Participants();

        if (isset($_POST) && $_POST != "") {

            if (isset($_POST['titleOperation']) &&
                isset($_POST['amountOperation'])  &&
                isset($_POST['dateOperation']) &&
                isset($_POST['Paid_by']) && $_POST['Paid_by'] != ""
            ) {
                $operation = new Operation($_POST['titleOperation'],
                    $tricount,
                    floatval($_POST['amountOperation']),
                    $_POST['dateOperation'],
                    Member::get_member_by_id(intval($_POST['Paid_by'])),
                    date("Y/m/d H:i:s"));
                $errors = $operation->validate();

                if (isset($_POST['participates']) && !empty($_POST['participates']) ) {
                    $list_repartition = $this->creat_repartition($_POST['participates']);
                    foreach ($list_repartition as $repartition){
                        if(!$repartition->validate() && empty($errors ['repartition'])){
                            $errors ['repartition'] = "Give positif value to repartition weigth";
                        }
                    }
                }
                //Aucun participant déléctionnez pour la dépense
                else{
                    $errors = array_merge($errors);
                    $errors['fromWhom'] = "At least one participant";
                }

                if (empty($errors) ) {
                    $newOperation = $operation->persist();
                    if ($newOperation->id !== null) {
                        if (!empty($list_repartition)  ) {
                            $newOperation?->clean_repartition();
                            foreach ($list_repartition as $repartition){
                                $repartition->operation = $newOperation;
                                if($repartition->validate()){
                                    $repartition->persist();
                                }
                            }
                            $this->redirect("Tricount", "view_tricount", $id_tricount);
                        }
                    }
                }
                //Erreur dans l'opération et aucun participant déléctionnez pour la dépense
                else{
                    $errors = array_merge($errors);
                    $this->retrive_inputs_fields();
                    if(!isset($_POST['participates'])){
                        $errors['fromWhom'] = "At least one participant";
                    }
                    else{
                        $participates = $_POST['participates'];
                    }
                    $form_input['titleOperation'] = $_POST['titleOperation'];
                    $form_input['amountOperation'] = $_POST['amountOperation'];
                    $form_input['dateOperation'] = $_POST['dateOperation'];
                    $form_input['Paid_by'] = intval($_POST['Paid_by']);

                }
            }
        }
        (new View("add_operation"))->show(["operation" => $operation,
                                                "form_input" => $form_input,
                                                "list_repartition" => $list_repartition,
                                                "result_repartition" => $result_repartition,
                                                 "participates" => $participates,
                                                 "errors"=>$errors,
                                                 "tricount"=>$tricount,
                                                 "tricount_subscribers"=>$list_tricount_subscribers
            ]);
    }

    private function creat_repartition(array $participants) :bool |array{
        $list_repartition = [];
        $participates = $participants;
        for ($i = 0; $i < count($participates); $i++) {
            $pattern = sprintf('/weightUser_%d/', $participates[$i]);
            foreach ($_POST as $key => $value) {
                if (preg_match($pattern, $key)) {
                    // Correspondance trouvée
                    // Utilisez $key et $value comme bon vous semble
                    $repartition = new Repartition(Member::get_member_by_id($participates[$i]), intval($value));
                    //Remttre dans la méthode principale le validate
                    $list_repartition[$repartition->member->id] = $repartition;
                }
            }

        }

        return $list_repartition;
    }

    private function retrive_inputs_fields() {

    }

    public function show_operation(): void {
        /*Récupérer le tricount séléctionner
        *verifier que l'utilisateur courant peux y accéder
        */
        $id_operation=0;
        $member=$this->get_user_or_redirect();
        $operation = null;
        $list_participants = [];
        $tricount = null;
        $errors=[];
        $tab=[];
        if(isset($_GET['param1'])){
            $id_operation = $_GET['param1'];

            if( !is_numeric($id_operation)){
                Tools::abort("The url is invalid, paramter must be string");
            }
            $operation = Operation::get_by_id($id_operation);
            /*$tricount = Tricount::get_tricount_by_id($id_tricount);*/
            if($operation === false){
                Tools::abort("This operation doesn't exist");
            }
            if(!$member->has_access_to_tricount($operation->tricount)){
                $errors[]="Vous n'avez pas accès au tricount";
                $this->error_view($errors);
            }
            $list_participants = $operation->get_Participants();
            //recupérer la liste des participants à la dépense, avec le montant

        }
        else
            Tools::abort("Pas de id tricount ou id operation");

        (new View("operation"))->show(["list_participants"=>$list_participants,
                                                "member"=>$member,
                                                "tricount"=>$tricount,
                                                "operation"=>$operation]
        );
    }
    public function delete(): void
    {
        $member=$this->get_user_or_redirect();

        if (isset($_GET["param1"])) {
            $id_operation = $_GET["param1"];
            $operation = Operation::get_by_id($id_operation);
            if($operation === false){
                Tools::abort("No operation");
            }
            if(!$member->has_access_to_tricount($operation->tricount)){
                Tools::abort("Not the rigth to do this");
            }
            if (isset($_POST["delete"])){
                $this->deleted($operation);
                $this->redirect("tricount","view_tricount",$operation->tricount->id);
            }
            (new View("delete_operation"))->show(["operation"=>$operation]);

        }
        else
            Tools::abort("Pas d'ID");

    }

    private function deleted(Operation $operation): bool {
        $member = $this->get_user_or_redirect();
        if(!$member->has_access_to_tricount($operation->tricount)){
            Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
        }
        if($operation) {
            $operation->delete();
            return true;
        }

        return false;
    }
    public function delete_service(){
        $member = $this->get_user_or_false();
        $res = "false";
        if(isset($_POST["operation"]) && $_POST["operation"] !== ""){
            $operation = Operation::get_by_id($_POST["operation"]);
            if(!$member->has_access_to_tricount($operation->tricount)){
                Tools::abort("Le member". $member->full_name . " n'as pas d'acces au tricount.");
            }
            if($operation !== false){
                if($operation->delete()){
                    echo "true";
                    return;
                }

            }
        }
        echo "false";
    }

    private function error_view(array $errors):void{
        (new View("error"))->show(["errors" => $errors]);
    }
}