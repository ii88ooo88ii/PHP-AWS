<?php 
/* 
 * connect, fetch, insert, update record operations
 */ 
 
class Subscriber { 
    private $host     = DB_HOST; 
    private $username = DB_USERNAME; 
    private $password = DB_PASSWORD; 
    private $dbname     = DB_NAME; 
    private $subscribers    = 'subscribers'; 
     
    function __construct(){ 
        if(!isset($this->db)){ 
            // connect to db
            $conn = new mysqli($this->host, $this->username, $this->password, $this->dbname); 
            if($conn->connect_error){ 
                die("Connection failed: " . $conn->connect_error); 
            }else{ 
                $this->db = $conn; 
            } 
        } 
    } 
     
    /* 
     * fetch records
     * @param table name
     * @param array select, where, order_by, limit and return_type conditions 
     */ 
    public function getRows($conditions = array()){ 
        $sql = 'SELECT '; 
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*'; 
        $sql .= ' FROM '.$this->subscribers; 
        if(array_key_exists("where",$conditions)){ 
            $sql .= ' WHERE '; 
            $i = 0; 
            foreach($conditions['where'] as $key => $value){ 
                $pre = ($i > 0)?' AND ':''; 
                $sql .= $pre.$key." = '".$value."'"; 
                $i++; 
            } 
        } 
         
        if(array_key_exists("order_by",$conditions)){ 
            $sql .= ' ORDER BY '.$conditions['order_by'];  
        }else{ 
            $sql .= ' ORDER BY id DESC ';  
        } 
         
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];  
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['limit'];  
        } 
         
        $result = $this->db->query($sql); 
         
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){ 
            switch($conditions['return_type']){ 
                case 'count': 
                    $data = $result->num_rows; 
                    break; 
                case 'single': 
                    $data = $result->fetch_assoc(); 
                    break; 
                default: 
                    $data = ''; 
            } 
        }else{ 
            if($result->num_rows > 0){ 
                while($row = $result->fetch_assoc()){ 
                    $data[] = $row; 
                } 
            } 
        } 
        return !empty($data)?$data:false; 
    } 
     
    /* 
     * insert records
     * @param table name
     * @param array data 
     */ 
    public function insert($data){ 
        if(!empty($data) && is_array($data)){ 
            $columns = ''; 
            $values  = ''; 
            $i = 0; 
            if(!array_key_exists('created',$data)){ 
                $data['created'] = date("Y-m-d H:i:s"); 
            } 
            if(!array_key_exists('modified',$data)){ 
                $data['modified'] = date("Y-m-d H:i:s"); 
            } 
            foreach($data as $key=>$val){ 
                $pre = ($i > 0)?', ':''; 
                $columns .= $pre.$key; 
                $values  .= $pre."'".$this->db->real_escape_string($val)."'"; 
                $i++; 
            } 
            $query = "INSERT INTO ".$this->subscribers." (".$columns.") VALUES (".$values.")"; 
            $insert = $this->db->query($query); 
            return $insert?$this->db->insert_id:false; 
        }else{ 
            return false; 
        } 
    } 
     
    /* 
     * update records
     * @param table name
     * @param array data 
     * @param array condition
     */ 
    public function update($data, $conditions){ 
        if(!empty($data) && is_array($data)){ 
            $colvalSet = ''; 
            $whereSql = ''; 
            $i = 0; 
            if(!array_key_exists('modified',$data)){ 
                $data['modified'] = date("Y-m-d H:i:s"); 
            } 
            foreach($data as $key=>$val){ 
                $pre = ($i > 0)?', ':''; 
                $colvalSet .= $pre.$key."='".$this->db->real_escape_string($val)."'"; 
                $i++; 
            } 
            if(!empty($conditions)&& is_array($conditions)){ 
                $whereSql .= ' WHERE '; 
                $i = 0; 
                foreach($conditions as $key => $value){ 
                    $pre = ($i > 0)?' AND ':''; 
                    $whereSql .= $pre.$key." = '".$value."'"; 
                    $i++; 
                } 
            } 
            $query = "UPDATE ".$this->subscribers." SET ".$colvalSet.$whereSql; 
            $update = $this->db->query($query); 
            return $update?$this->db->affected_rows:false; 
        }else{ 
            return false; 
        } 
    } 
}