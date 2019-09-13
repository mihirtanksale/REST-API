<?php

class DbOperations{
    private $con;

    function __construct(){
        require_once dirname(__FILE__) . '/DbConnect.php';

        $db =new DbConnect;

        $this->con = $db->connect();
    }

    public function createUser($Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak){
        if(!$this->isDalnameExist($Dal_name)){

            $stmt = $this->con->prepare("INSERT INTO jp_dal (Dal_name, Kishor, Kumar, Yuvak, Margadarshak) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiii", $Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak);
            if($stmt->execute()){
                return USER_CREATED;
            }else{
                return USER_FAILURE;
            }
        }
       return USER_EXISTS;
    }

    public function userLogin($Dal_name){
        if($this->isDalnameExist($Dal_name)){
            return USER_AUTHENTICATED;
        }else{
            return USER_NOT_FOUND;
        }
    }

    public function getAllUsers(){
        $stmt = $this->con->prepare("SELECT id, Dal_name, Kishor, Kumar, Yuvak, Margadarshak FROM jp_dal");
        $stmt->execute();
        $stmt->bind_result($id, $Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak);
        $users = array();
        while($stmt->fetch()){
            $user = array();
            $user['id'] = $id;
            $user['Dal_name'] = $Dal_name;
            $user['Kishor'] = $Kishor;
            $user['Kumar'] = $Kumar;
            $user['Margadarshak'] = $Margadarshak;
            array_push($users, $user);
        }
        return $users;
        
    }

    public function getUserByDalname($Dal_name){
        $stmt = $this->con->prepare("SELECT id, Dal_name, Kishor, Kumar, Yuvak, Margadarshak FROM jp_dal WHERE Dal_name = ?");
        $stmt->bind_param("s",$Dal_name);
        $stmt->execute();
        $stmt->bind_result($id, $Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak);
        $stmt->fetch();
        $user = array();
        $user['id'] = $id;
        $user['Dal_name'] = $Dal_name;
        $user['Kishor'] = $Kishor;
        $user['Kumar'] = $Kumar;
        $user['Margadarshak'] = $Margadarshak;
        return $user;
    }

    public function updateUser($Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak, $id){
        $stmt = $this->con->prepare("UPDATE jp_dal SET Dal_name = ?,Kishor = ?,Kumar = ?, Yuvak = ?, MArgadarshak = ? WHERE id = ?");
        $stmt->bind_param("siiiii", $Dal_name, $Kishor, $Kumar, $Yuvak, $Margadarshak,$id);
        if($stmt->execute())
            return true;
        return false;
    }

    public function deleteUser($id){
        $stmt = $this->con->prepare("DELETE FROM jp_dal WHERE id=?");
        $stmt->bind_param("i",$id);
        if($stmt->execute())
            return true;
        return false;
    }

    private function isDalnameExist($Dal_name){
        $stmt = $this->con->prepare("SELECT id FROM jp_dal WHERE Dal_name = ?");
        $stmt->bind_param("s", $Dal_name);  
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}

