<?php
class AdminModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAdminByid($id){
        $this->db->where('id',$id);
        return $this->db->getOne('admin');
    }
    public function getAdminBydata($email,$password){
        $this->db->where('email',$email);
        $this->db->where('password',$password);
        return $this->db->getOne('admin');
    }
    public function getAdmins(){
        return $this->db->get('admin');
    }
    public function getCard($card){
        $this->db->where('card',$card);
        return $this->db->getOne('admin');
    }
    public function updateAdmin($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('admin', $data);
    }
    public function addAdmin($data){
        return $this->db->insert('admin',$data);
    }
    public function deleteAdmin($id){
        $this->db->where('id',$id);
        return $this->db->delete('admin');
    }
      
}

?>
