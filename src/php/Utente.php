<?php
enum ErroreUtente{
    case REGISTER_ALREADY_EXIST;
    case REGISTER_ERROR;
}

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
            echo "Errore nella preparazione della query: " . $this->dbConnection->error;
            exit(1);
        }
        $stmt->bind_param('ss', $this->username, $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result && $result->num_rows > 0) {
            return true;
        }
        return false;
    }
    
    public function register(){
        if($this->checkIfUserAlreadyExist()) return ErroreUtente::REGISTER_ALREADY_EXIST;
        $query = "INSERT INTO utente (username, password, email, isAdmin, saldo) VALUES (?, ?, ?, 0, 0)";
        $stmt = $this->dbConnection->prepare($query);
        if (!$stmt) {
            echo "Errore nella preparazione della query: " . $this->dbConnection->error;
            exit(1);
        }
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bind_param('sss', $this->username, $hashed_password, $this->email);
        $stmt->execute();
    
        return $stmt->affected_rows == 1 ? true : ErroreUtente::REGISTER_ERROR;
    }

    public function login() {
        $query = "SELECT * FROM utente WHERE username = ?";
        $stmt = $this->dbConnection->prepare($query);
        if (!$stmt) {
            echo "Errore nella preparazione della query: " . $this->dbConnection->error;
            exit(1);
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


