<?php

     /*
          @author İbrahim ÖZTÜRK
          @web http://ibrahimozturk.me/
          @mail work@ibrahimozturk.me
     */
     Class EasyDB extends PDO{

          /*
               SQL Proccess Type
               @var string $proccess
          */
          private $proccess;

          /*
               SQL Query
               @var string $sql
          */
          private $sql;

          /*
               Form Data
               @var array $data
          */
          private $data;

          /*

               Constructor
               @param string $database
               @param string $host
               @param string $username
               @param string $password
               @param string $charset

          */
          public function __construct($database, $host = 'localhost', $username = 'root', $password = '', $charset = 'utf8'){
               parent::__construct('mysql:host='.$host.';dbname='.$database, $username, $password);
               $this->query('SET CHARACTER SET '.$charset);
               $this->query('SET NAMES '.$charset);
               $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          }

          /*

               SQL
               @param string $proccess
               @param string $table

          */
          public function sql($proccess, $table){
               $this->proccess     = $proccess;

               if(!isset($table) || trim(isset($table)) == ''){
                    die('Please select your table.');
               }

               if($proccess == 'select'){
                    $this->sql     = 'SELECT * FROM '.$table.' ';
               }else if($proccess == 'insert'){
                    $this->sql     = 'INSERT INTO '.$table.' SET ';
               }else if($proccess == 'update'){
                    $this->sql     = 'UPDATE '.$table.' SET ';
               }else if($proccess == 'delete'){
                    $this->sql     = 'DELETE FROM '.$table.' ';
               }else{
                    die('Please select your proccess.');
               }

               return $this;

          }

          /*

               From
               @param string $fields

          */
          public function from($fields){
               $this->sql     = str_replace('*', $fields, $this->sql);
               return $this;
          }

          /*

               Serialize
               @param array $array

          */
          public function serialize($array = array()){
               $i = 0;
               if(is_array($array)){
                    foreach($array as $key => $value){
                         $i++;
                         if(count($array) == $i){
                              $this->sql     .= $key.' = :'.$key.' ';
                         }else{
                              $this->sql     .= $key.' = :'.$key.', ';
                         }
                         $this->data[':'.$key]    = $value;
                    }
               }
               return $this;
          }

          /*

               Additional
               @param string $type
               @param array $array

          */
          public function additional($type, $array = null){
               $this->sql     .= $type.' ';
               if(is_array($array)){
                    foreach($array as $key => $value){
                         $this->data[':'.$key]    = $value;
                    }
               }
               return $this;
          }

          /*

               Result

          */
          public function result(){
               $result             = array();
               $result['query']    = (string) $this->sql;

               if(is_array($this->data)){
                    $result['data']     = (object) $this->data;
                    $query    = $this->prepare($this->sql);
                    try{
                         $query->execute($this->data);
                    }catch(PDOException $e){
                         return $e->getMessage();
                    }
                    if($this->proccess == 'select'){
                         $result['count']    = (int) $query->rowCount();
                         $result['result']   = $query->fetch(parent::FETCH_OBJ);
                    }else if($this->proccess == 'insert'){
                         $result['last_id']  = (int) $this->lastInsertId();
                    }else{
                         $result['result']   = $query->execute($this->data);
                    }
               }else{
                    $result['data']     = null;
                    if($this->proccess == 'select'){
                         $query              = $this->query($this->sql);
                         $result['count']    = (int) $query->rowCount();
                         $result['result']   = (object) $query->fetchAll(parent::FETCH_OBJ);
                    }
               }

               $result['result']   = (empty($result['result'])) ? NULL : $result['result'];

               return (object) $result;
          }

          public function __destruct(){

          }

     }

?>
