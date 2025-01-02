<?php
require_once('Database.class.php');
class Signup{
    private $id;
    private $username;
    private $password;
    private $email;
    private $db; 
    public function __construct($username,$password,$email){
        $this->db = Database::getConnection();
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        if($this->user_exists($username,$email)){
            throw new Exception("User already exists");
        }
        $bytes = random_bytes(16);
        $token = bin2hex($bytes);
        $password = $this->hashPassword();
        $query = "INSERT INTO `auth` (`username`, `password`, `email`, `active`, `token`) VALUES ('$username', '$password', '$email', 0, '$token');";
        if(!mysqli_query($this->db,$query)){
            throw new Exception("unable to signup");
        }else{
            
            $this->id = mysqli_insert_id($this->db);

        }
    }
    public function getInsertID(){
        return $this->id;
    }
    public function hashPassword(){
        $options = [
            'cost'=>12,
        ];
        return password_hash($this->password,PASSWORD_BCRYPT,$options);
    }
    public function user_exists($username,$email){
        $sql = "SELECT COUNT(*) as user_count FROM `auth` WHERE `username` = '$username' AND `email` = '$email';";
        $check_email = mysqli_query($this->db,$sql);
        if ($check_email) {
            $row = mysqli_fetch_assoc($check_email);
            return $row['user_count'] > 0;
        } else {
            return false;
        }

    }
   
}