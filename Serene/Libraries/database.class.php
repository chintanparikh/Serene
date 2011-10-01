<?php
/**
 * Basic class to deal with mySQL databases, can be easily changed to deal with other types of databases
 * 
 * @version 1.0
 * @author timtamboy63 (timtamboy63@gmail.com)
 * @license Creative Commons
 *
 * Changelog:
 *
 *
 */
class Database 
{

    /************PROPERTIES************/

    /**
     * The username for the database
     *
     * @var string
     */
    protected $username;

    /**
     * The password for the database
     *
     * @var string
     */
    protected $password;

    /**
     * The database host - usually 'localhost' will do fine
     *
     * @var string
     */
    protected $host;

    /**
     * The port for the database
     *
     * @var int
     */
    protected $port;

    /**
     * The name of the database
     *
     * @var string
     */
    protected $databaseName;

    /**
     * Holds the PDO Object
     *
     * @var object
     */
    public $connection;

    /**
     * Holds the Prepared Query
     *
     * @var NUL
     */
    public $statement;

    /**
     * Holds the status of transaction
     *
     * @var boolean
     */
    public $transaction = false;

    /**
     * Holds the status of whether the query has been prepared or not
     *
     * @var boolean
     */
    public $isPrepared = false;


    /**
     * Holds the instance of the Database class, used for the Singleton Design Pattern
     *
     * @var object
     */
    private static $instance;

    /**
     * Gets the current instance of the class, or initiates the class of no Instance is present (Singleton Design Pattern)
     *
     * @access public
     * @param array $databaseInfo
     * @return object
     */
    public static function getInstance()
    {

        if(!self::$instance){
            $args = func_get_args();
            if( isset($args[0]) )
            {
                if( is_array($args[0]) )
                {
                    self::$instance = new Database($args[0]);
                }
                else
                {
                    throw new Exception('Database Information is not in the correct format, it must be an array!');                    
                }
            }
            else
            {
                throw new Exception('Database Information not provided on first instantiation of class');
            }
            
        }
        return self::$instance;
    }

    /**
     * Constructor function, takes $databaseInfo and sets local class variables
     *
     * @access private
     * @param array $databaseInfo
     * @return void
     */
    private function __construct(array $databaseInfo)
    {
        extract($databaseInfo);
        $this->host = $host;
        $this->port = $port;
        $this->databaseName = $name;
        $this->username = $username;
        $this->password = $password;
        $this->connect();     
    }

    /**
     * Destructor function, disconnects the database
     *
     */
    function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connects to the database
     *
     * @access public
     * @return object (allows for chaining methods)
     */    
    function connect()
    {
        $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->databaseName";
        $this->connection = new PDO($dsn, $this->username, $this->password);
        return $this;
    }

    /**
     * Disconnects from the database
     *
     * @access public
     * @return object (allows for chaining methods)
     */
    function disconnect()
    {
        $this->connection = null;
        return $this;
    
    }

    /**
     * Prepares the query
     *
     * @access public
     * @param string $query The query that needs to be executed, with a '?' for each parameter that needs to be binded
     * @return object
     */
    function prepare($query)
    {
        $this->statement = $this->connection->prepare($query);
        $this->isPrepared = 1;
        return $this;
    }

    /**
     * Binds parameters to the query and executes the query
     *
     * @access public
     * @param array $param An array of the parameters that need to be binded
     * @return object
     */
    function execute()
    {
        $toBind = func_get_args();
        if($this->isPrepared == 1){        
            $paramNumber = 1;
            if($toBind){
                foreach($toBind as $bind){
                    $this->statement->bindValue($paramNumber, $bind);
                    $paramNumber++;
                }
            }
            $this->statement->execute();
            return $this->statement;
        }
        else{
            throw new Exception("Statement must be prepared before executing");
        }
    
    }
    /**
     * Starts a PDO Transaction (changes aren't made to the database until $this->commit is called, and changes can be rolled back via $this->rollBack)
     *
     * @access public
     * @return object
     */    
    function beginTransaction()
    {
        $this->connection->beginTransaction();
        $this->transaction = true;
        return $this;
    }

    /**
     * Commits a PDO Transaction (changes aren't made to the database until $this->commit is called, and changes can be rolled back via $this->rollBack)
     *
     * @access public
     * @return object
     */ 
    function commit()
    {
        if($this->transaction == true){            
            $this->connection->commit();
            $this->transaction = false;
            return $this;
        }
        else{
            throw new Exception('Transaction as not been initated');
        }
    }

    /**
     * Rolls back a PDO Transaction (changes aren't made to the database until $this->commit is called, and changes can be rolled back via $this->rollBack)
     *
     * @access public
     * @return object
     */ 
    function rollBack()
    {
        if($this->transaction == true){            
            $this->connection->rollBack();
            $this->transaction = false;
            return $this;
        }
        else{
            throw new Exception('Transaction as not been initated');
        }
    }

    /**
     * Makes whatever variables passed to it into an array, then returns that array. Used with $this->execute.
     *
     * @access public
     * @return array
     */ 
    function makeArray()
    {
        return func_get_args();
    }

    /**
     * Finds the number of rows in a table
     *
     * @access public
     * @param string $table 
     * @return object
     */ 
    function numRows($table)
    {
        $this->prepare("SELECT COUNT(*) FROM `?`");
        return count($this->execute($this->makeArray($table))->fetchAll());

    }


}

/* Example Usage


$databaseInfo = array('host' => 'localhost',
                      'port' => '3306',
                      'database' => 'testdb',
                      'username' => 'root',
                      'password' => '');
$db = Database::getInstance($databaseInfo);

try{
    $output = $db->connect()->prepare('SELECT Name FROM testing WHERE Job = "Dentist"')->execute()->fetchObject();
    foreach($output as $name){
        print $name . '<br />';
    }
}
catch(Exception $e){
    echo 'Caught Exception: ',  $e->getMessage(), "\n";
    $this->disconnect();
}

try{
    $db->prepare('INSERT INTO testing (Name, Job) VALUES (?,?)')->execute($db->makeArray('Calvin', 'Janitor'))->execute($db->makeArray('Kyle', 'Musician'));
}
catch(Exception $e){
    echo 'Caught Exception: ',  $e->getMessage(), "\n";
    $this->disconnect();
}

*/

?>