[![EasyDB](http://ibrahimozturk.me/assets/img/article/cover_1490025776.jpg)](http://ibrahimozturk.me/yazi/8-easydb-pdo-kutuphanesi)

### Installing via Composer

`$ composer require "ibrahimozturkme/easydb":"dev-master"

### Constructor

- `$database` : String.
- `$host` : String.
- `$username` : String.
- `$password` : String.
- `$charset` : String.

Server Mode

     $db = new \EasyDB\Connection('database_name', 'localhost', 'ibrahimozturk', '12341234', 'utf8');

Localhost mode

     $db = new \EasyDB\Connection('database_name');

- - -

### SQL

- `$proccess` : String.
- `$table` : String.


     $db->sql('select', 'articles');
     
- - -

### From

- `$fields` : String.


     $db->sql('select', 'articles')->from('id, title, article');

- - -

### Serialize

- `$array` : Array. Form data


     $db->sql('insert', 'articles')->serialize($_POST)->result();

- - -
### Additional

- `$type` : String.
- `$array` : Array. Form data


     $db->sql('update', 'articles')->serialize($array)->additional('WHERE id = :id', ['id' => 2])->result();

- - -
### Result

Query result.

     $db->sql('select', 'articles')->result();

- - -
### Examples

__SELECT - Single__

     $query    = $db->sql('select', 'articles')->additional('WHERE id = :id', ['id' => 2])->result();
     echo $query->count;
     echo $query->result->title;


__SELECT - Multiple__

     $query    = $db->sql('select', 'articles')->result();
     echo $query->count;

     foreach($query->result as $row){
          echo $row->title.'<br>';
     }

__SELECT - From__

     $query    = $db->sql('select', 'articles')->from('id, title, article')->result();
     echo $query->count;
     
     foreach($query->result as $row){
          echo $row->title.'<br>';
     }

__INSERT__

     $insert   = $db->sql('insert', 'articles')->serialize($array)->result();
     echo $insert->last_id;
     echo ($insert->result) ? 'Has been added.' : 'Could not be added.';


__UPDATE__

     $update   = $db->sql('update', 'articles')->serialize($array)->additional('WHERE id = :id', ['id' => 2])->result();
     echo ($update->result) ? 'Have been updated.' : 'Update failed.';


__DELETE__

     $delete   = $db->sql('delete', 'articles')->additional('WHERE id = :id', ['id' => 2])->result();
     echo ($update->result) ? 'Has been deleted.' : 'Could not be deleted.';
