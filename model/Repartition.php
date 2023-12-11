<?php

class Repartition extends Model
{
    public function __construct(public Member $member,
                                public int $weight,
                                public ?Operation $operation = null


    ) {}

    public function persist() :Repartition| null{
        $array = [];
        $recherche=self::execute("SELECT *
                                    FROM repartitions
                                    Where repartitions.operation=:id_operation
                                    and repartitions.user=:id_user",["id_operation"=>$this->operation->id,
                                                                     "id_user"=>$this->member->id]);
        if($recherche->rowCount()>0){
            $query=self::execute("UPDATE  repartitions 
                                        SET repartitions.weight=:weight 
                                        Where repartitions.operation=:id_operation 
                                        and repartitions.user =:id_user",[
                                            "id_operation"=>$this->operation->id,
                                            "id_user"=>$this->member->id,
                                            "weight"=>$this->weight
            ]);
            return $this;
        }
        else{
            $query=self::execute("INSERT INTO repartitions (operation,user,weight)
                                        VALUES (:id_operation, :user, :weight)",
                ["id_operation"=>$this->operation->id,
                    "user"=>$this->member->id,
                    "weight"=>$this->weight]);
            return $this;
        }
        return null;

    }
    public static function get_for_operation(Operation $operation, Member $member){
        $query = self::execute("SELECT * 
                                    FROM repartitions 
                                    WHERE repartitions.operation =:id_operation 
                                          and repartitions.user =:id_member",
                ["is_operation"=>$operation->id,"id_member"=>$member->id]);
    }

    public static function get_total_repartitions(Operation $operation) :int{
        $query = self::execute("SELECT SUM(weight) as total_repartitions 
                                    FROM repartitions  
                                    WHERE repartitions.operation = :id_operation",
                                                            ["id_operation"=>$operation->id]);
        $tab = $query->fetch();
        return $tab[0];
    }

    public function validate() :bool{
        if($this->weight === 0 || $this->weight === ""){
            return false;
        }
        return true;
    }


}