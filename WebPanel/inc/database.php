<?php
require_once 'inc.php';

class DB
{
    private static $m_pInstance;
    private static $conn;
    private static $success;

    public static function connect()
    {
        try {
            $config = parse_ini_file("../config/db.ini");
            self::$conn = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db_name'], $config['username'], $config['password']);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$success = true;
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            self::$success = false;
        }
    }

    public static function getInstance()
    {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new DB();
        }
        return self::$m_pInstance;
    }
    /*
    Checks if the DB is connected
    */
    public static function isConnected()
    {
        return self::$success;
    }
    
    /*
    Selects query with params
    EXAMPLE Usage:
    select('SELECT * FROM myTable WHERE name = :name'), array(':name' => $name));
    */
    public static function select($query, $params)
    {
        try {
            $rows = array();
            $stmt = self::$conn->prepare($query);
            $result = $stmt->execute($params);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($rows, $row);
            }
            return $rows;
        } catch (PDOException $e) {
            return 'ERROR: ' . $e->getMessage();
        }
    }

    /*
    Executes query with params
    EXAMPLE Usage:
    query('DELETE FROM myTable WHERE name = :name'), array(':name' => $name));
    */
    public static function query($query, $params)
    {
        try {
            $stmt = self::$conn->prepare($query);
            $result = $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            return 'ERROR: ' . $e->getMessage();
        }
    }

    
    public function __sleep()
    {
        return array('conn');
    }

    public function __wakeup()
    {
        self::connect();
    }
}
