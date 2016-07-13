<?php
class csrf {
   public  $action='unspecified'; // action page the script is good for
   public  $life = 720; // minutes for which key is good
   private $table = 'token_table_name';
   private $sid; // session id of user
   private $mdb2; // mdb2 database object
   
   public function csrf($mdb2) {
      $sid = session_id();
      $this->mdb2 =& $mdb2;
      $this->sid  = preg_replace('/[^a-z0-9]+/i','',$sid);
      }
      
   public function csrfkey() {
      $key = md5(microtime() . $this->sid . rand());
      $stamp = time() + (60 * $this->life);
      $q = "INSERT INTO $this->table (sid,mykey,stamp,action) VALUES (?,?,?,?)";
      $types = Array('text','text','integer','text');
      $args  = Array($this->sid,$key,$stamp,$this->action);
      $sql = $this->mdb2->prepare($q,$types,MDB2_PREPARE_MANIP);
      $sql->execute($args);
      $sql->free();
      return $key;
      }
      
   public function checkcsrf($key) {
      $this->cleanOld();
      $cleanKey = preg_replace('/[^a-z0-9]+/','',$key);
      if (strcmp($key,$cleanKey) != 0) {
         return false;
         } else {
         $q = "SELECT id FROM $this->table WHERE sid=? AND mykey=? AND action=?";
         $types = Array('text','text','text');
         $args  = Array($this->sid,$cleanKey,$this->action);
         $sql = $this->mdb2->prepare($q,$types,MDB2_PREPARE_RESULT);
         $rs = $sql->execute($args);
         while($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
            $valid = $row->id;
            }
         if (! isset($valid)) {
            $sql->free();
            return false;
            } else {
            $q = "DELETE FROM $this->table WHERE id=?";
            $types = Array('integer');
            $args  = Array($valid);
            $sql = $this->mdb2->prepare($q,$types,MDB2_PREPARE_MANIP);
            $sql->execute($args);
            $sql->free();
            return true;
            }
         }
      }
      
   private function cleanOld() {
      // remove expired keys
      $exp = time();
      $q = "DELETE FROM $this->table WHERE stamp < ?";
      $types[] = 'integer';
      $args[]  = $exp;
      $sql = $this->mdb2->prepare($q,$types,MDB2_PREPARE_MANIP);
      $sql->execute($args);
      $sql->free();
      return true;
      }
      
   public function logout() {
      $types = Array('text');
      $args  = Array($this->sid);
      $q = "DELETE FROM $this->table WHERE sid=?";
      $sql = $this->mdb2->prepare($q,$types,MDB2_PREPARE_MANIP);
      $sql->execute($args);
      $sql->free();
      return true;
      }
   }
?>