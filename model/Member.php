<?php

require_once "framework/Model.php"; // C'est lui qui se connecte à la db
require_once "Operation.php";

class Member extends Model {

    public function __construct(public string $mail, public ?string $hashed_password = null, public ?string $full_name= null,
                                public ?string $role= null, public ?string $iban= null, public ?int $id=null) {}

    public static function get_member_by_mail(string $mail) : Member|false {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail"=>$mail]);
        $data = $query->fetch(); // un seul résultat au maximum
        // si l'utilisateur n'existe pas
        if ($query->rowCount() == 0) {
            return false;
        } else {
            // on retourne le membre
            return new Member($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data['iban'], $data['id']);
        }
    }
    public static function get_By_Id(int $userId) :Member | null{
        $query=self::execute("Select * 
                                    from users
                                    where users.id=:id_user",["id_user"=>$userId]);
        $data=$query->fetch();
        if($query->rowCount()>0){
            return new Member($data["mail"],
                                $data["hashed_password"],
                                $data["full_name"],
                                $data["role"],
                                $data['iban'],
                                $data["id"]);
        }
        else
            return null;
    }
    public static function get_all() :array{
        $list_member=[];
        $query=self::execute("Select * from users",[]);
        $tab=$query->fetchAll();
        if($query->rowCount()!=0){
            foreach ($tab as $data){
                 $list_member[]=new Member($data["mail"],
                                                $data["hashed_password"],
                                                $data["full_name"],
                                                $data["role"],
                                                $data['iban'],
                                                $data["id"]);
            }
        }
        return $list_member;
    }

    public function get_amout_for_operation(Operation $operation){
        $query1=self::execute("Select sum(repartitions.weight)
                                        from repartitions
                                        where repartitions.operation
                                        and repartitions.operation=:id_operation",["id_operation"=>$this->id]);
        $total_weight=$query1->fetch();
        $query2=self::execute("SELECT sum(operations.amount*(repartitions.weight/:totalWeight))
                                    FROM repartitions,operations
                                    WHERE repartitions.operation=operations.id
                                    and repartitions.operation=:id_operation
                                    and repartitions.user=:id_user",["id_user"=>$this->id,
                                                                     "id_operation"=>$operation->id,
                                                                     "totalWeigth"=>$total_weight
        ]);
    }

    public static function get_member_by_id(int $id) : Member|false {
        $query = self::execute("SELECT * FROM users where id = :id", ["id"=>$id]);
        $data = $query->fetch(); // un seul résultat au maximum
        // si l'utilisateur n'existe pas
        if ($query->rowCount() === 0) {
            return false;
        } else {
            // on retourne le membre
            return new Member($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data['iban'], $data['id']);
        }
    }
    
    public static function get_members() : array {
        // executer la requete, renvoie un tableau
        $query = self::execute("SELECT * FROM Users", []);
        // fetchAll renvoie un tableau à deux dimensions
        $data = $query->fetchAll();
        // pour stoquer les résultats
        $results = [];
        foreach ($data as $row) {
            // je mets dans mon tableau chaque membre avec ses colonnes
            $results[] = new Member($row["mail"], $row["hashed_password"], $row["full_name"], $row["role"], $row['iban'], $row['id']);
        }
        return $results;
    }

    public function get_email_member() :string{
        return $this->mail;
    }

    // Vérifier si le pseudo et le mdp sont correct
    public static function validate_login(string $mail, string $password) : array {
        $errors = [];
        // méthode qui vérifie si le membre existe et le récupère
        $member = Member::get_member_by_mail($mail);
        if ($member) {
            // reagrde le mot de passen entré comparé au mot de passe haché
            if (!self::check_password($password, $member->hashed_password)) {
                $errors['password_wrong'] = "Wrong password. Please try again.";
            }
        } else {
            $errors['member_missing'] = "Can't find a member with the pseudo : " . $mail;
        }
        return $errors;
    }

    public static function check_password(string $clear_password, string $hash) : bool {
        return $hash === Tools::my_hash($clear_password);
    }

    public function get_tricounts() : array {
        return  Tricount::get_tricounts($this);
    }

    private static function validate_password(string $password) : array {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors['password_length'] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors['password_format'] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_passwords(string $password, string $password_confirm) : array {
        $errors = Member::validate_password($password);
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors['password_length'] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors['password_format'] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        if ($password !== $password_confirm) {
            $errors['password_same'] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function user_already_exist(String $userFullName) :array {
    $error=[];
    $query = self::execute("SELECT * FROM users where full_name = :pseudo", ["pseudo"=>$userFullName]);
    $data = $query->fetch();
    if($query->rowCount() !== 0){
        $error[0]="The full name already exists in the database";
    }
    return $error;
    }

    public static function valid_email($email) :array{
        $errors=[];
        if(self::email_exist($email)){
            $error="This email already exists";
            array_push($errors,$error);
        }
        if(filter_var($email, FILTER_VALIDATE_EMAIL)===false){
            $error ="You have to enter a valid email format";
            array_push($errors,$error);
        }
        else if(empty($email)){
            $error="You must give an email address";
            array_push($errors,$error);
        }
        return $errors;
    }
    public static function update_email($email) :array{
        $error=[];
        if(filter_var($email, FILTER_VALIDATE_EMAIL)===false){
            $error[]="You have to enter a valid email format";
        }
        else if(empty($email)){
            $error[]="You must give an email address";
        }
        return $error;
    }
    public static function email_exist(string $email, ?string $id = null) :bool{
        if($id !== null){
            $query = self::execute("SELECT * FROM users WHERE mail =:mail AND id != :id",
                ["mail"=>$email,
                    "id"=>$id

                ]);
        }
        else{
            $query = self::execute("SELECT * FROM users WHERE mail =:mail",
                ["mail"=>$email]);
        }
        return $query->rowCount()>0;
    }
    public static function valid_userName($name): array {
        $errors = [];
        if(empty($name)) {
            $errors[] = "Full name is required.";
        }if(strlen($name) < 3) {
            $errors[] = "Full name length  must be minimum 3";
        }
        return $errors;
    }
    public static function isValidIban($iban) : array{
        $errors = [];
        if(!empty($iban)){
            if(!preg_match("/^[A-Z]{2}[0-9]{2}[\s]?[0-9]{4}[\s]?[0-9]{4}[\s]?[0-9]{4}$/", $iban)) {
                $errors[] = "The pattern of iban is like AA99 9999 9999 9999 (or whithout space)";
            }
        }
        return $errors ;
    }

    public static function updatePassword($id, $password) : array{
        $errors= [];
        self::execute("UPDATE users SET hashed_password =:password WHERE id = :id", ["password"=> $password,"id"=> $id]);

        if(empty($password)){
            $errors[] = "Le champ ne peut etre vide";
        }
        return $errors;
    }
    public  function updateMember( ) : bool{
        $query = self::execute("UPDATE users SET mail=:mail, full_name=:name, iban=:iban WHERE id =:id",
            ["mail"=> $this->mail, "name" => $this->full_name, "iban" => $this->iban, "id" => $this->id]);
         return $query->rowCount()>0;
    }
    public function my_expenses(Tricount $tricount) :float{
        $query = self::execute("SELECT SUM(amount) 
                                    FROM operations 
                                    WHERE operations.tricount = :id_tricount 
                                    and operations.initiator = :id_member",["id_tricount"=>$tricount->id,
                                                                            "id_member"=>$this->id]);
        $result = $query->fetchAll();
        $expenses = $result[0]['SUM(amount)'];
        return floatval($expenses);
    }


    public function  my_total(Tricount $tricount) :float{
        $total = 0;
        $operations = $tricount->get_operations();
        foreach ($operations as $operation){
            if($operation->is_participate($this)){
                $total += $operation->get_amount($this);
            }
        }
        return floatval($total);
    }

    public function  is_initiator(Operation $operation) :bool{
        $query = self::execute("SELECT * 
                                    FROM operations 
                                    WHERE operations.id =:id_operation 
                                    and operations.initiator=:id_member",["id_operation"=>$operation->id,
                                                                            "id_member"=>$this->id]);
        return $query->rowCount()>0;
    }

    public function has_access_to_tricount(Tricount $tricount) :bool{
        $participant = self::execute("SELECT * 
                                    FROM subscriptions
                                    WHERE subscriptions.tricount =:id_tricount
                                    and subscriptions.user =:id_member
                                    ",["id_member"=>$this->id,"id_tricount"=>$tricount->id]);
        $initiator = self::execute("SELECT *
                                        FROM tricounts
                                        WHERE tricounts.id = :id_tricount
                                        AND tricounts.creator = :id_member",["id_member"=>$this->id,"id_tricount"=>$tricount->id]);
        return $participant->rowCount()>0 || $initiator->rowCount()>0;
    }

    public static function exist(int $id) :bool{
        $member = Member::get_member_by_id($id);
        return $member != false;
    }

    public function persist() : Member {
        // méthode qui vérifie si le membre existe et le récupère
        if($this->id !== null){
            self::execute("UPDATE users 
                                SET mail=:mail,hashed_password =:password,full_name=:name,iban=:iban
                                WHERE id=:id", ["password"=> $this->hashed_password,
                                                "id"=> $this->id,
                                                "mail"=>$this->mail,
                                                "name"=>$this->full_name,
                                                "iban"=>$this->iban ]);
        }
        else{
            self::execute("INSERT INTO users (mail, hashed_password,full_name,iban)
                                 VALUES (:mail,:password,:name,:iban)",["mail"=>$this->mail,
                                                                        "password"=>$this->hashed_password,
                                                                        "name"=>$this->full_name,
                                                                        "iban"=>$this->iban
            ]);
            $this->id = self::lastInsertId();
        }
        return $this;
    }

    public static function add_user(String $userFullName,String $userPassword,String $userEmail,String $userIban):Member|false{
        $query=self::execute("INSERT INTO users(full_name,hashed_password,mail,iban) 
                                    VALUES(:full_name,:hashed_password,:mail,:iban)",
                                    ["full_name"=>$userFullName,
                                    "hashed_password"=>$userPassword,
                                    "mail"=>$userEmail,
                                    "iban"=>$userIban]);
        return self::get_member_by_mail($userEmail);
    }


    public function delete_member() :bool{
        $delete_operation_repartition=self::execute("DELETE
                                                            FROM 
                                                            where ",["id_user"=>$this->id]);

        $count = $delete_operation_repartition->rowCount();

        return false;
    }


    public function validate() :array{

        $errors = [];
        //function verification email
        if(filter_var($this->mail, FILTER_VALIDATE_EMAIL)===false){
            $errors ['email_format'] ="You have to enter a valid email format";
        }
        else if(empty($this->mail)){
            $errors ['email_empty'] ="You must give an email address";
        }
        // Vérification de la longueur du name du member
        if(empty($this->full_name)) {
            $errors["name_empty"] = "Full name is required.";
        }if(strlen($this->full_name) < 3) {
            $errors["name_size"] = "Full name length  must be minimum 3";
        }
        // vérification de l'iban
        if(!empty($this->iban)){
            if(!preg_match("/^[A-Z]{2}[0-9]{2}[\s]?[0-9]{4}[\s]?[0-9]{4}[\s]?[0-9]{4}$/", $this->iban)) {
                $errors['iban_format'] = "The pattern of iban is like AA99 9999 9999 9999 (or whithout space)";
            }
        }
        //verification existance dy mail
        if(Member::email_exist($this->mail,$this->id)){
            $errors ['email_unique'] = "This email is already taken";
        }
        return $errors ;
    }
}

