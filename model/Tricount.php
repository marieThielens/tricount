<?php

require_once "framework/Model.php";
require_once "Member.php";
require_once "Operation.php";

class Tricount extends Model {

    public function __construct(public string $title,
                                public string $createdAt,
                                public Member $creator,
                                public ?string $description=null,
                                public ?int $id=null){}

    public static function get_tricounts(Member $member) : array {
        $query = self::execute("SELECT id FROM (
                                    SELECT DISTINCT tricounts.id as id, tricounts.created_at as created_at
                                    FROM tricounts
                                    WHERE tricounts.creator = :id_member
                                    UNION
                                    SELECT DISTINCT subscriptions.tricount as id, tricounts.created_at as created_at
                                    FROM subscriptions
                                    JOIN tricounts ON subscriptions.tricount = tricounts.id
                                    WHERE subscriptions.user = :id_member
                                ) AS combined
                                ORDER BY created_at DESC", ["id_member" => $member->id] );
        $data1 = $query->fetchAll();
        $tricounts = [];

        foreach ($data1 as $row) {
            $tricounts[] = self::get_by_id($row['id']);
        }

        return $tricounts;
    }
    public function validate(?string $title=null) :array{
        $errors = [];
        if(strlen($this->title) < 3) {
            $errors['title_empty'] = "The title must be bigger than 2  ";
        }
        if(empty($this->title) ) {
            $errors['title_size'] = "Title cannot be empty";
        }

        if($this->already_exiting_title_service($this->title,$this->creator,$this->id)){
            $errors['title_unique'] = "Not twice the same name of tricount by member";
        }


        if($this->description != null && $this->description != "" &&  strlen($this->description)<3){
            $errors['description_size'] = "If description is not empty, it must be bigger than 2";
        }
        /*if($this->already_exiting_title_service($this->title,$this->creator,$this->id)){
            $errors['description_size'] = "If description is not empty, it must be bigger than 2";
        }*/

        return $errors;
    }

    public static function get_by_id($id) :Tricount|false {
        $query = self::execute("SELECT * FROM tricounts where id=:id ", ["id" => $id]);
        $data = $query->fetch();
        if ($query->rowCount() != 0) {
            return new Tricount($data['title'],
                                $data['created_at'],
                                Member::get_member_by_id($data['creator']),
                                $data['description'],
                                $data['id']);
        }
        return false;
    }

    public function get_operations() : array{
        $query=self::execute("SELECT operations.title,
                                        operations.tricount,
                                         cast(operations.amount as Decimal(15,2)) as amount,
                                         operations.operation_date,
                                         operations.initiator,
                                         operations.created_at,
                                         operations.id
                                    from users,operations
                                    where operations.initiator=users.id
                                    and operations.tricount=:id_tricount",["id_tricount"=>$this->id]);
        $array = $query->fetchAll();
        $operations = [];
        foreach ($array as $row) {
            $operations[] = new Operation($row['title'],
                                            Tricount::get_by_id($row['tricount']),
                                            $row['amount'],
                                            $row['operation_date'],
                                            Member::get_member_by_id($row['initiator']),
                                            $row['created_at'],
                                            $row['id']);
        }
        return $operations;
    }

    public function get_number_participants() : array {
        // tricounts.id
        $query = self::execute("select count(*) as nombre 
                                    from tricounts, subscriptions 
                                    where subscriptions.tricount = tricounts.id 
                                    and tricounts.creator = :creator_id 
                                    group by tricounts.id", ["creator_id" => $this->creator->id]);
        return  $query->fetchAll();
    }

    public function nb_participants() : int {
        $result = self::execute("Select count(*) nbparticipant 
                                    FROM subscriptions
                                    where subscriptions.tricount= :tricount", ["tricount" => $this->id ]);
        $nb=$result->fetch();
        return $nb["0"] ;
    }

    public function nb_operations() : int {
        $tab = self::execute("Select count(*) nboperations 
                                    FROM operations
                                    where operations.tricount= :tricount", ["tricount" => $this->id ]);
        $nb=$tab->fetch();
        return $nb["0"];
    }

    // Vérifier si l'user à le droit de regarder le tricount
    public function is_user_right_access(int $id_user) :bool{
        $query=self::execute("SELECT count(*)
                                    FROM subscriptions 
                                    WHERE subscriptions.tricount=:id_tricount
                                    and subscriptions.user=:id_user",["id_tricount"=>$this->id,"id_user"=>$id_user]);
        $result=$query->fetch();
        return in_array(1,$result);
    }
    public function total_expenses() :float|null {
        $query=self::execute("SELECT CAST(SUM(operations.amount) AS DECIMAL(15, 2))  as total
                                    FROM operations 
                                    WHERE operations.tricount=:id_tricount",["id_tricount"=>$this->id]);
        $result=$query->fetch();
        return $result["0"];
    }

    public function delete() :bool{
        self::execute("DELETE FROM repartition_template_items
                                    WHERE repartition_template_items.repartition_template in ( 
                                        SELECT repartition_templates.id 
                                        FROM repartition_templates
                                        WHERE repartition_templates.tricount =:id)",
                                                    ["id"=>$this->id]);

        self::execute("DELETE FROM repartition_templates 
                                WHERE repartition_templates.tricount = :id",
                                                    ["id"=>$this->id]);

        self::execute("DELETE FROM repartitions
                                                    WHERE repartitions.operation IN (
                                                                                SELECT operations.id 
                                                                                FROM operations 
                                                                                WHERE operations.tricount=:id_tricount
                                                                            )",["id_tricount"=>$this->id]);

        self::execute("DELETE FROM operations
                            WHERE operations.tricount=:id_tricount",["id_tricount"=>$this->id]);

        self::execute("DELETE
                            FROM subscriptions
                            where subscriptions.tricount=:id_tricount",["id_tricount"=>$this->id]);

        $delete_tricount=self::execute("DELETE
                                                FROM tricounts
                                                where tricounts.id=:id_tricount",["id_tricount"=>$this->id]);


        return $delete_tricount->rowCount() > 0;
    }
    public function get_Participants() :array|null{
        $subscribers=null;
        $subscribers=self::execute("SELECT users.*
                                    FROM subscriptions,users
                                    where subscriptions.user=users.id
                                    and subscriptions.tricount=:id_tricount",["id_tricount"=>$this->id]
        );

        $tab=$subscribers->fetchAll();

        foreach ($tab as $row) {
            $participants[] = new Member($row["mail"],
                $row['hashed_password'],
                $row['full_name'],
                $row['role'],
                $row['iban'],
                $row['id']);
        }



        return $participants;
    }
    public function subscribes(Member $member) :bool{
        $query=self::execute("SELECT *
                                    FROM subscriptions
                                    where subscriptions.user=:id_user
                                    and subscriptions.tricount=:id_tricount",["id_tricount"=>$this->id,"id_user"=>$member->id]
        );
        return $query->rowCount()>0;
    }

    public function add_subscribers(array $ids) : bool {
        foreach ($ids as $id){
            $query=self::execute("INSERT into subscriptions (tricount, user) VALUES (:tricount, :user)",
            ['tricount' => $this->id, 'user' => $id]
            );
        }
       // var_dump($query->fetchAll());
        return $query->rowCount() == count($ids);
    }

    public  function is_title_exist() :bool{
        $query = self::execute("SELECT * FROM `tricounts` WHERE title =:title ",["title"=>$this->title]);
        return $query->rowCount()>0;
    }

    public function get_nb_participants() :int{
        $query = self::execute("SELECT count(*) FROM subscriptions WHERE subscriptions.tricount =:id",
                                    ["id"=>$this->id]);
        $res = $query->fetch();
        return $res[0]-1;
    }

    public function add_perticipant(Member $member){
        $query = self::execute("INSERT INTO subscriptions ( tricount, user)
                                    VALUES (:idTricount,:idUser)",
            ["idTricount"=>$this->id,"idUser"=>$member->id]);
    }

    public function remove_perticipant(Member $member){
        $query = self::execute("DELETE FROM subscriptions WHERE subscriptions.user =:idUser AND subscriptions.tricount=:idTricount",
                                    ["idTricount"=>$this->id,"idUser"=>$member->id]);
    }
    public function implicates(Member $member) :bool{
        $query_repartition = self::execute("SELECT * 
                                    FROM `repartitions` 
                                    WHERE repartitions.user=:idUser
                                    AND repartitions.operation in (SELECT operations.id
                                                                   FROM operations
                                                                   WHERE operations.tricount =:idTricount)",
            ["idTricount"=>$this->id,"idUser"=>$member->id]);
        $query_initiator =  self::execute("SELECT * 
                                                FROM operations 
                                                WHERE operations.tricount = :idTricount
                                                and operations.initiator = :idUser",
            ["idTricount"=>$this->id,"idUser"=>$member->id]);

        return $query_repartition->rowCount()>0 || $query_initiator->rowCount()>0;
    }

    //nommé avant get_by_title
    public  function already_exiting_title() :bool{

            $query = self::execute("SELECT * 
                                    FROM tricounts 
                                     WHERE tricounts.title = :title
                                       and tricounts.id != :id_tricount
                                     and tricounts.creator = :id_member",["title"=>$this->title,
                                                                            "id_tricount"=>$this->id,
                                                                            "id_member"=>$this->creator->id]);
            return $query->rowCount()>0;
    }

    public  function already_exiting_title_without_id() :bool{

        $query = self::execute("SELECT * 
                                    FROM tricounts 
                                     WHERE tricounts.title = :title
                                     and tricounts.creator = :id_member",["title"=>$this->title,
            "id_member"=>$this->creator->id]);
        return $query->rowCount()>0;
    }
    public static  function already_exiting_title_service(string $title, Member $member,?int $id=null) :bool{
        if($id === null){
            $query = self::execute("SELECT * 
                                    FROM tricounts 
                                     WHERE tricounts.title = :title
                                     and tricounts.creator = :id_member",["title"=>$title,
                "id_member"=>$member->id]);
        }
        else{
            $query = self::execute("SELECT * 
                                    FROM tricounts 
                                     WHERE tricounts.title = :title
                                     and tricounts.creator = :id_member
                                     and tricounts.id != :id_tricount",["title"=>$title,
                "id_member"=>$member->id,"id_tricount"=>$id]);
        }


        return $query->rowCount()>0;
    }
    public function persist() :Tricount{
        if($this->id === null){
            $query=self::execute("INSERT INTO tricounts (title, 
                                                                description, 
                                                                creator, 
                                                                created_at)
                                        VALUES (:title, :description, :creator, :created_at  )",
                ['title' => $this->title,
                    'description' => $this->description,
                    'creator' => $this->creator->id,
                    'created_at' => $this->createdAt
                ]
            );
            $this->id = self::lastInsertId();
        }
        else{
            $query=self::execute("UPDATE  tricounts 
                                        SET title=:title,  
                                            description=:description, 
                                            creator=:creator, 
                                            created_at=:created_at
                                        Where tricounts.id=:tricount_id",
                ['title' => $this->title,
                    'description' => $this->description,
                    'creator' => $this->creator->id,
                    'created_at' => $this->createdAt,
                    'tricount_id'=>$this->id
                ]
            );
        }
        return $this;
    }

    public function has_created_by(Member $member) :bool{
        $query = self::execute("SELECT * 
                                    FROM tricounts
                                    WHERE tricounts.id = :id_tricount
                                    and tricounts.creator = :id_member",["id_tricount"=>$this->id, "id_member"=>$member->id]);
        return $query->rowCount()>0;
    }

    public function balance_status(Member $member) :float{
        $my_total = $member->my_total($this); // 30 par personne
        $my_expenses = $member->my_expenses($this);
        $balance =  $my_expenses - $my_total ;
        return round($balance,2) ;
    }

    public function total_balance() : float{
        $participants = $this->get_Participants();
        $total_balance = 0;

        foreach ($participants as $participant){
            $total_balance += $this->balance_status($participant);
        }

        return round($total_balance,2) ;
    }

    public function balance_percentage(Member $member) :float{
        //Stocker toutes les balances
        //Trouver la balance la plus grande en valeur absolue
        $participants = $this->get_Participants();
        $max_balance = 0;

        foreach ($participants as $participant){
            if($this->balance_status($participant) < 0){
                $current_balance = -$this->balance_status($participant);
            }
            else{
                $current_balance = $this->balance_status($participant);
            }
            if($max_balance < $current_balance){
                $max_balance = $current_balance;
            }
        }
        //Divisier la balance du member par la plus grande balance
        ////multiplier par 100
        $percentage = round(($this->balance_status($member) / $max_balance) *100,2);
        if ($percentage < 0){
            $percentage = - $percentage;
        }
         return  $percentage;

    }
}