<?php

class Subscription extends Model
{
    public function __construct(public Tricount $tricount,
                                public Member $member


    ) {}

    public function persist() :Subscription|bool{
        $recherche=self::execute("SELECT *
                                    FROM subscriptions
                                    Where subscriptions.tricount=:id_tricount
                                    and subscriptions.user=:id_user",["id_tricount"=>$this->tricount->id,
                                                                        "id_user"=>$this->member->id]);
        if($recherche->rowCount()>0){
            $query = self::execute("UPDATE  subscriptions 
                                        SET tricount=:id_tricount, 
                                            user=:id_user",[
                "id_operation"=>$this->tricount->id,
                "id_user"=>$this->member->id
            ]);
            $array = $query->fetch();
            return new Subscription($array['tricount'],
                                    $array['user']);
        }
        else{
            $query = self::execute("INSERT INTO repartitions (tricount,user)
                                        VALUES (:tricount, :user)",
                ["tricount"=>$this->tricount->id,
                    "user"=>$this->member->id]);
            $array = $query->fetch();
            if($query->rowCount()>0){
                return new Subscription($array['tricount'],
                                        $array['user']);

            }
        }
        return false;
    }


}