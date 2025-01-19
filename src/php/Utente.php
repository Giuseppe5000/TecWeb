<?php

require_once "../../exceptions.php";

class Utente {
    
    private $dbConnection;
    private $username;
    private $password;
    private $email;

    public function __construct($dbConnection, $username, $password, $email){
        $this->dbConnection = $dbConnection;       
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUsername(){
        return $this->username;
    }

    private function checkIfUserAlreadyExist(){
        $query = "SELECT * FROM utente WHERE username = ? OR email = ?";
        $stmt = $this->dbConnection->prepare($query);
        if (!$stmt) {
            throw new PrepareStatementException($this->dbConnection->error);
        }
        $stmt->bind_param('ss', $this->username, $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result && $result->num_rows > 0) {
            throw new UserAlredyExistsException();
        }
    }
    
    public function register(){
        $this->checkIfUserAlreadyExist();
        $query = "INSERT INTO utente (username, password, email, isAdmin, saldo) VALUES (?, ?, ?, 0, 0)";
        $stmt = $this->dbConnection->prepare($query);
        if (!$stmt) {
            throw new PrepareStatementException($this->dbConnection->error);
        }
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bind_param('sss', $this->username, $hashed_password, $this->email);
        $stmt->execute();
    
        if ($stmt->affected_rows != 1) {
            throw new UserRegisterGenericException();
        }
    }

    public function login() {
        $query = "SELECT * FROM utente WHERE username = ?";
        $stmt = $this->dbConnection->prepare($query);
        if (!$stmt) {
            throw new PrepareStatementException($this->dbConnection->error);
        }
        $stmt->bind_param('s', $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $utente = $result->fetch_assoc();
            return password_verify($this->password, $utente['password']);
        }
        return false;
    }
}


