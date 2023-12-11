<?php

class Operation extends Model
{

    public function __construct(public string $title,
                                public Tricount $tricount,
                                public float $amount,
                                public string $operation_date,
                                public Member $initiator,
                                public  string $created_at,
                                public ?int $id=null

    ) {}

    public function  validate() :array{
        $errors = [];
        if(strlen($this->title)<3){
            $errors['title_length']="Title must have at least 3 characters";
        }

        if(empty($this->title) ) {
            $errors['title_empty'] = "Title cannot be empty";
        }
        if($this->amount <= 0){
            // il faut mettre que c'est un tableau sinon plante et dit que c'est une string
            $errors['amount_size'] ="Amount must be bigger than 0";
        }
        if(!is_numeric($this->amount)){
            $errors['amount_format'] = "Amount must be a positive integer";
        }

        if($this->amount == "" || empty($this->amount) ) {
            $errors['amount_empty'] ="Amount cannot be empty";
        }
        $paid_by = Member::get_member_by_id($this->initiator->id);
        if($paid_by === false){
            $errors['member_exist']="The member who paid have to exist";
        }
        // Créer un objet DateTime à partir de la chaîne de caractères
        $patern = '/^(0[1-9]|[1-2]\d|30)\/(0[1-9]|1[0-2])\/([1-9]\d{3}|[2-9]\d{1})$/';
        if(preg_match($patern,$this->operation_date) === false || preg_match($patern,$this->operation_date) === 0){
            $errors ['date_format'] = "Wrong date format. Date format example: 05/03/2023 or 05/03/23";
        }
        else{
            $dateStringFormatted = str_replace('/', '-', $this->operation_date);
            $date = DateTime::createFromFormat('d-m-Y', $dateStringFormatted );
            $this->operation_date = $date->format('Y-m-d');
        }

        return $errors;

    }

    public function repartition_weight(Member $member):int{
        $query=self::execute("SELECT repartitions.weight as weight
                                    FROM repartitions 
                                    WHERE repartitions.operation=:id_operation
                                    and repartitions.user=:id_user",["id_operation"=>$this->id,"id_user"=>$member->id]);
        $tab=$query->fetch();
        return $tab['weight'];
    }
    public function is_participate(Member $member):bool{
        $query=self::execute("SELECT *
                                    FROM repartitions 
                                    WHERE repartitions.operation=:id_operation
                                    and repartitions.user=:id_user",["id_operation"=>$this->id,"id_user"=>$member->id]);
        return $query->rowCount()>0;
    }

    private static function arrayToOperation(array $array) :Operation|null{
        return new Operation($array['title'],
                                Tricount::get_by_id($array['tricount']),
                                $array['amount'],
                                $array['operation_date'],
                                $array['created_at'],
                                $array['id']

        );

    }
    public static function add_repartion(int $id_operation,int $id_user, int $weight) :bool{
        $query=self::execute("INSERT into repartitions (repartitions.operation,repartitions.user,repartitions.weight) 
                                    VALUES(:id_operation,:id_user,:wieht)",["id_operation"=>$id_operation,
                                                                            "id_user"=>$id_user,
                                                                            "weight"=>$weight]);
        $query->fetch();
        return false;
    }
    /*public static function get_by_id(int $id) :Operation| false{
        $query=self::execute("SELECT *
                                    FROM operations
                                    Where operations.id=:id_operation",["id_operation"=>$id]);
        $array=$query->fetch();
        if($query->rowCount()>0){
           //self::arrayToOperation($array);
            return new Operation($array['title'],
                                    Tricount::get_by_id($array['tricount']),
                                    $array['amount'],
                                    $array['operation_date'],
                                    Member::get_member_by_id( $array['initiator']),
                                    $array['created_at'],
                                    $array['id']);
        }
        return false;
    }*/

    public function clean_repartition() : bool{
        $query = self::execute("DELETE FROM repartitions WHERE repartitions.operation = :id_operation",["id_operation"=>$this->id]);
        $check = self::execute("SELECT * FROM repartitions WHERE repartitions.operation = :id_operation", ["id_operation"=>$this->id]);

        return $check->rowCount() === 0;

    }
    /*public function getNbParticipantsbyOperation() :array {
        $query=self::execute("SELECT COUNT(*)
                                    from repartitions
                                    where repartitions.operation=4",["id"=>$this->id]
        );
        return $query->fetch();
    }*/

    public function get_Participants() :array|null{
        $participants = [];
        $query=self::execute("SELECT repartitions.user FROM repartitions WHERE repartitions.operation =:id",["id"=>$this->id]
        );
       $tab=$query->fetchAll();
       if($query->rowCount()>0)
        foreach ($tab as $row) {
            if(Member::exist($row['user'])){
                $participants[] = Member::get_member_by_id($row['user']);
            }
        }

        return $participants;
    }
    public function get_tricount() :Tricount|null{
        $tricount=null;
        $query=self::execute("SELECT COUNT(*)
                                    from repartitions
                                    where repartitions.operation=4",["id"=>$this->id]
        );
        $data = $query->fetch();
        if ($query->rowCount() != 0) {
            return new Tricount($data['title'],
                $data['created_at'],
                Member::get_member_by_id($data['creator']),
                $data['description'],
                $data['id']);
        }

        return $tricount;
    }
    public function participates(Member $participant):bool{
        $query=self::execute("SELECT COUNT(*)
                                    FROM repartitions
                                    WHERE repartitions.user=:id_user
                                    AND repartitions.operation=:id_operation",
                                        ["id_operation"=>$this->id,
                                        "id_user"=>$participant->id]
        );
        return $query->rowCount();
    }
    public function get_number_participants(){
        $query=self::execute("SELECT COUNT(*) as nb_participants
                                    from repartitions
                                    where repartitions.operation=:id",["id"=>$this->id]
        );
        $result=$query->fetch();
        return $result["nb_participants"];
    }
    public function get_amount(Member $participant) :float | null{
        $query=self::execute("SELECT SUM(repartitions.weight) as total_weight
                                    FROM repartitions
                                    WHERE repartitions.operation=:id",["id"=>$this->id]
        );
        $toal_weight=$query->fetch();
        $toal_weight=$toal_weight["0"];
        $query2=self::execute("SELECT cast(operations.amount*(repartitions.weight/:total_weight) as DECIMAL(15,2)) as total
                                    FROM repartitions,operations
                                    WHERE operations.id=repartitions.operation
                                    and repartitions.operation=:id_operation
                                    AND repartitions.user=:id_user",["id_operation"=>$this->id,
                                                                    "id_user"=>$participant->id,"total_weight"=>$toal_weight]);
        $result=$query2->fetch();
        if($result === null){$total = floatval(0);}
        else
            $total = $result["total"];
        return $total;
    }
    public static function get_by_id ( int $id_operation) :Operation| false{
        $query=self::execute("SELECT operations.title,
                                            operations.tricount,
                                            cast(operations.amount as Decimal(15,2)) as amount,
                                            operations.operation_date,
                                            operations.initiator,
                                            operations.created_at,
                                            operations.id
                                    from operations
                                    where operations.id = :id_operation
                                    ",["id_operation"=>$id_operation]);
        $array=$query->fetch();
        if($query->rowCount()>0){
            return new Operation($array['title'],
                                Tricount::get_by_id($array['tricount']),
                                $array['amount'],
                                $array['operation_date'],
                                Member::get_member_by_id( $array['initiator']),
                                $array['created_at'],
                                $array['id']);
        }
        return false;
    }


    public function persist() :Operation|null {
        if($this->id === null){
            $query=self::execute("INSERT INTO operations (title, 
                                                                tricount, 
                                                                amount, 
                                                                operation_date, 
                                                                initiator, 
                                                                created_at)
                                        VALUES (:title, :tricount, :amount, :operation_date, :initiator, :created_at  )",
                ['title' => $this->title,
                    'tricount' => $this->tricount->id,
                    'amount' => (float) $this->amount,
                    'operation_date' => $this->operation_date,
                    'initiator' => $this->initiator->id,
                    'created_at' => $this->created_at
                ]
            );
            $this->id = self::lastInsertId();
        }
        else{
            $query=self::execute("UPDATE  operations 
                                        SET title=:title, 
                                            tricount=:tricount, 
                                            amount=:amount, 
                                            operation_date=:operation_date, 
                                            initiator=:initiator, 
                                            created_at=:created_at
                                        Where operations.id=:operation_id",
                ['title' => $this->title,
                    'tricount' => $this->tricount->id,
                    'amount' => (float) $this->amount,
                    'operation_date' => $this->operation_date,
                    'initiator' => $this->initiator->id,
                    'created_at' => $this->created_at,
                    'operation_id'=>$this->id
                ]
            );
        }
        return $this;
    }

    public function is_Creator() :bool{
        $query=self::execute("SELECT * 
                                    FROM operations
                                    WHERE operations.initiator=:initiator
                                    and operations.id=:operation_id",
            ['initiator' => $this->initiator,
                'operation_id'=>$this->id
            ]);
        return $query->rowCount()>0;
    }
    public function get_initiator() :Member{
        $query = self::execute("SELECT operations.initiator FROM `operations` WHERE id=:id",["id"=>$this->id]);
        $array = $query->fetch();
        $id = $array[0];
        return Member::get_member_by_id($id);
    }

    public function delete() :bool{
        $delete_repartition = self::execute("DELETE FROM repartitions
                                                    WHERE repartitions.operation = :id_operation",
                                                    ["id_operation"=>$this->id]);
        $delete_operation = self::execute("DELETE FROM operations
                                                    WHERE operations.id = :id_operation",
                                                ["id_operation"=>$this->id]);

        return $delete_repartition->rowCount()>0 && $delete_operation->rowCount()>0;
    }

    public function next_one() : int | false{
        $query = self::execute("SELECT *
                                    FROM operations
                                    WHERE operations.operation_date >= :operation_date
                                    and operations.id > :id_operation
                                    and operations.tricount =:id_tricount
                                    ORDER BY operations.operation_date ASC
                                    LIMIT 1;",["operation_date"=> $this->operation_date,
                                                "id_operation" => $this->id,
                                                "id_tricount"=>$this->tricount->id]);
        $array = $query->fetch();
        return  ($array !== false ) ? ($array["0"]) : (false);
    }

    public function previous_one() : int | false{
        $query = self::execute("SELECT *
                                    FROM operations
                                    WHERE operations.operation_date <= :operation_date
                                    and operations.id < :id_operation
                                    and operations.tricount =:id_tricount
                                    ORDER BY operations.operation_date DESC 
                                    LIMIT 1;",["operation_date"=> $this->operation_date,
            "id_operation" => $this->id,"id_tricount"=>$this->tricount->id]);
        $array = $query->fetch();
        return  ($array !== false ) ? ($array["0"]) : (false);
    }



}