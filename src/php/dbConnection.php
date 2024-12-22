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

    public function getUtente($username, $password) {
        $query = "SELECT * FROM utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
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
    
}

?>
