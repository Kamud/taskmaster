<?php
class Database{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct(){
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    public function check_id($table,$id){
        $sql = "SELECT * FROM $table WHERE _id = :id";
        $this->stmt = $this->dbh->prepare($sql);
        $this->stmt->bindParam('id',$id);
        $this->stmt->execute();

        if ($this->stmt->rowCount() < 1){
            return false;
        }
        else{
            return true;
        }
    }


    // Prepare statement with query
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind values
    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute(){
        return $this->stmt->execute();
    }
    // Get result set as array of objects
    public function resultSet(){
        $this->execute();

        $results = $this->stmt->fetchAll(PDO::FETCH_OBJ);

        //FILTER COLUMNS WITH NULL VALUES
        foreach ($results as $result){
            foreach ($result as $key => $value){
                if($value === null){
                    unset($result->$key );
                }
            }
        }
        return  $results;
    }

    // Get single record as object
    public function single(){
        $this->execute();
        if($this->stmt->rowCount() === 0){
            return false;
        }
        else{
            $result = $this->stmt->fetch(PDO::FETCH_OBJ);
            foreach ($result as $key => $value){
                if($value === null || $value === ''){
                    unset($result->$key );
                }
            }
            return $result;
        }
    }
    //GET NUMBER OF MATCHED ROWS
    public function rowCount(){
        return $this->stmt->rowCount();
    }
}