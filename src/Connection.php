<?php
   
namespace src\EasyDB\Connection;

/**
 * @author İbrahim ÖZTÜRK
 * @web http://ibrahimozturk.me
 * @mail work@ibrahimozturk.me
 */
Class Connection extends \PDO
{
    /**
     * SQL Proccess Type
     *
     * @var string $proccess
     */
    private $proccess;

    /**
     * SQL Query
     *
     * @var string $sql
     */
    private $sql;

    /**
     * Form Data
     *
     * @var array $data
     */
    private $data;

    /**
     * EasyDB constructor.
     *
     * @param string $database
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $charset
     */
    public function __construct($database, $host = 'localhost', $username = 'root', $password = '', $charset = 'utf8')
    {
        parent::__construct('mysql:host=' . $host . ';dbname=' . $database, $username, $password);
        $this->query('SET CHARACTER SET ' . $charset);
        $this->query('SET NAMES ' . $charset);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * SQL
     *
     * @param string $proccess
     * @param string $table
     *
     * @return $this
     */
    public function sql($proccess, $table)
    {
        $this->proccess = $proccess;

        if ( ! isset($table) || trim(isset($table)) == '') {
            die('Please select your table.');
        }

        switch ($proccess) {
            case 'select':
                $this->sql = 'SELECT * FROM ' . $table . ' ';
                break;
            case 'insert':
                $this->sql = 'INSERT INTO ' . $table . ' SET ';
                break;
            case 'update':
                $this->sql = 'UPDATE ' . $table . ' SET ';
                break;
            case 'delete':
                $this->sql = 'DELETE FROM ' . $table . ' ';
                break;
            default:
                die('Please select your proccess.');
                break;
        }

        return $this;
    }

    /**
     * From
     *
     * @param string $fields
     *
     * @return $this
     */
    public function from($fields)
    {
        $this->sql = str_replace('*', $fields, $this->sql);

        return $this;
    }

    /*

         Serialize
         @param array $array

    */
    /**
     * Serialize
     *
     * @param array $array
     *
     * @return $this
     */
    public function serialize($array = [])
    {
        if (is_array($array)) {

            // for key value sql pairs
            $sqls = [];

            foreach ($array as $key => $value) {

                // add key value sql pair
                $sqls[] = $key . ' = :' . $key;

                // add key value to data
                $this->data[':' . $key] = $value;
            }

            // generate sql
            $this->sql .= implode(', ', $sqls) . ' ';
        }

        return $this;
    }

    /**
     * Additional
     *
     * @param string $type
     * @param null $array
     *
     * @return $this
     */
    public function additional($type, $array = null)
    {
        $this->sql .= $type . ' ';

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->data[':' . $key] = $value;
            }
        }

        return $this;
    }

    /**
     * Result
     *
     * @return object
     */
    public function result()
    {
        $result = [];
        $result['query'] = (string) $this->sql;

        if (is_array($this->data)) {

            $result['data'] = (object) $this->data;

            $query = $this->prepare($this->sql);

            try {
                $query->execute($this->data);
            } catch (\PDOException $e) {
                return $e->getMessage();
            }

            if ($this->proccess == 'select') {

                $result['count'] = (int)$query->rowCount();
                $result['result'] = $query->fetch(parent::FETCH_OBJ);

            } else {

                if ($this->proccess == 'insert') {

                    $result['last_id'] = (int) $this->lastInsertId();

                } else {

                    $result['result'] = $query->execute($this->data);

                }
            }

        } else {

            $result['data'] = null;

            if ($this->proccess == 'select') {

                $query = $this->query($this->sql);

                $result['count'] = (int) $query->rowCount();
                $result['result'] = (object) $query->fetchAll(parent::FETCH_OBJ);
            }
        }

        $result['result'] = empty($result['result']) ? null : $result['result'];

        return (object) $result;
    }
}
