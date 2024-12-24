<?php
namespace DB;

class DBAccess {

    private const HOST_DB = "mariadb";
    private const DATABASE_NAME = "db";
    private const USERNAME = "user";
    private const PASSWORD = "passwd";

    private $connection;

    public function openDBConnection() {

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);

        // Debug
        return mysqli_connect_error();

        // Prod
        //return $this->connection->connect_errno() ? false : true;

    }

    public function closeConnection() {
        mysqli_close($this->connection);
    }


    public function getOpere() {

        $query = "SELECT * FROM opera";
        $query_result = mysqli_query($this->connection, $query);

        if (!$query_result) {
            echo "Query Error: " . mysqli_error($this->connection);
            exit(1);
        }

        if ($query_result->num_rows > 0) {
            $result = array();
            while ($row = $query_result->fetch_array(MYSQLI_ASSOC)) {
                array_push($result, $row);
            }
            $query_result->free();
            return $result;
        }

        return null;

    }

    public function getUtenteLogin($username, $password) {
        $query = "SELECT * FROM utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            echo "Errore nella preparazione della query: " . $this->connection->error;
            exit(1);
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $utente = $result->fetch_assoc();
            if (password_verify($password, $utente['password'])) {
                return $utente; 
            }
        }
        return null; 
    }    
    
    public function verificaRegistrazione($username, $email){
        $query = "SELECT * FROM utente WHERE username = ? OR email = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            echo "Errore nella preparazione della query: " . $this->connection->error;
            exit(1);
        }
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return false;
        }
        return true;
    }

    public function registraUtente($username, $password, $email){
        $query = "INSERT INTO utente (username, password, email, isAdmin, saldo) VALUES (?, ?, ?, 0, 0)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            echo "Errore nella preparazione della query: " . $this->connection->error;
            exit(1);
        }
        $stmt->bind_param('sss', $username, password_hash($password, PASSWORD_DEFAULT), $email);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function hashConverter(){
        $query = "SELECT username, password FROM utente";
        $result = mysqli_query($this->connection, $query);
        if (!$result) {
            echo "Query Error: " . mysqli_error($this->connection);
            exit(1);
        }

        $affectedRows = 0;

        while ($row = $result->fetch_assoc()) {
            $username = $row['username'];
            $password = $row['password'];

            if (strlen($password) === 60 && preg_match('/^\$2y\$[0-9]{2}\$[A-Za-z0-9\.\/]{53}$/', $password)) {
                continue; //il regex dell'hash l'ho trovato su stackoverflow, da verificare se funziona, l'obiettivo sarebbe quello di saltare le password già convertite
            }

            $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            $updateQuery = "UPDATE utente SET password = ? WHERE username = ?";
            $stmt = $this->connection->prepare($updateQuery);
            $stmt->bind_param('ss', $hashedPassword, $username);
            $stmt->execute();
            $affectedRows += $stmt->affected_rows;
        }

        return $affectedRows;
    }
}

?>
