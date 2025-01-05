<?php

class Database {

    private const HOST_DB = "mariadb";
    private const DATABASE_NAME = "db";
    private const USERNAME = "user";
    private const PASSWORD = "passwd";

    private $connection;

    public function openConnection() {

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->connection = mysqli_connect(Database::HOST_DB, Database::USERNAME, Database::PASSWORD, Database::DATABASE_NAME);

        // Debug
        return mysqli_connect_error();

        // Prod
        //return $this->connection->connect_errno() ? false : true;

    }

    public function closeConnection() {
        mysqli_close($this->connection);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getQueryResult($query_result){
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
        return array();
    }

    public function executeQuery($query){
        $query_result = mysqli_query($this->connection, $query);
        return $this->getQueryResult($query_result);
    }

    public function executePreparedStatement($query,$format_string,$value) {
        $stmt = $this->connection->prepare($query);
        if (!$stmt) throw new PrepareStatementException($this->connection->error);

        $stmt->bind_param($format_string, ...$value);

        $stmt->execute();
        $query_result = $stmt->get_result();
        $stmt->close();
        return $this->getQueryResult($query_result);
    }
}
?>
