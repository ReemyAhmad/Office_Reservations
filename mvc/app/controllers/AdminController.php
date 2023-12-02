<?php
require_once __DIR__.'/../models/AdminModel.php';

class AdminController {
    private $model;

    public function __construct($db) {
        $this->model = new AdminModel($db);
    }
    public function addAdmin (){
        if($_SERVER['REQUEST_METHOD']==="POST"){
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            $password = isset($_POST['password']) ? $this->encode_password($_POST['password']) : "";
            $name = isset($_POST['name']) ? $_POST['name'] : "";
            $card = $this->create_card();
            $data=array(
                'email'=>$email,
                'password'=>$password,
                'name'=>$name,
                'card'=> $card
            );
            if($this->full_data($data)){
                if($this->valid_data($data)){
                    if($this->model->addAdmin($data)){
                        echo json_encode(array("status"=>"True" , "message"=>"Add Admin".json_encode($name)));
                    }else{
                        echo json_encode(array("status"=>"falid" , "message"=>"can't Add Admin".json_encode($name)));
                    }
                }
            }
        }
    }
    public function showAdmins (){
        $Admins = $this->model->getAdmins();
        echo json_encode(array("status"=>"True" , "data"=> json_encode(array($Admins))));
    }
    public function updateAdmin (){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id=isset($_POST['id']) ? $_POST['id']: "";
            if(!empty($id)){
                if(is_integer($id)){
                    $admin = $this->model->getAdminByid($id);
                    if($admin !=NULL){
                        $email=isset($_POST['email']) ? $_POST['email']:$admin['email'];
                        $password=isset($_POST['password']) ? $this->encode_password($_POST['password']) :$admin['password'];
                        $name=isset($_POST['name']) ? $_POST['name']:$admin['name'];
                        $data=array(
                            'email'=>$email,
                            'name'=>$name,
                            'password'=>$password
                        );
                        if($this->model->updateAdmin($id,$data)){
                            echo json_encode(array("status"=>"True" , "message"=>"update success"));
                        }else{
                            echo json_encode(array("status"=>"faild" , "message"=>"faild update"));
                        }
                    }else{
                        echo json_encode(array("status"=>"faild" , "message"=>"id not found"));
                    }
                }else{
                    echo json_encode(array("status"=>"faild" , "message"=>"id must be integer"));
                }
            }else{
            echo json_encode(array("status"=>"faild" , "message"=>"id is empty please enter id"));
            }
        }
    }
    public function deleteAdmin (){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $id = isset($_POST['id']) ? $_POST['id']:"";
            if(!empty($id)){
                if(is_integer($id)){
                    if($this->model->getAdminByid($id)){
                        if($this->model->deleteAdmin($id)){
                            echo json_encode(array("status"=>"True" , "message"=>"delete success"));
                        }else{
                            echo json_encode(array("status"=>"faild" , "message"=>"faild delete"));
                        }
                    }else{
                        echo json_encode(array("status"=>"faild" , "message"=>"id not found"));
                    }
                }else{
                    echo json_encode(array("status"=>"faild" , "message"=>"id must be integer"));
                }
            }else{
            echo json_encode(array("status"=>"faild" , "message"=>"id is empty please enter id"));
            }
        }
    }
    public function create_card (){
        $card = substr(str_shuffle("0123456789QWERTYUIOPASDFGHJKLMNBVCZXabcd"),0,10);
        return $card;
    }
    public function update_card ($id,$data){
        return $this->model->updateAdmin($id,$data);
    }
    public function full_data ($data){
        $res = array();
        foreach($data as $key => $value){
            if(empty($value)){
                array_push($res,$key);
            }
        }
        if (count($res)>0){
            echo json_encode(array("status"=>"faild" , "message"=>json_encode(array("data"=>$res))."is empty"));  
            return False;
        }else{
            return True;
        }
    }
    public function valid_data ($data){
        if ($this->valid_email($data['email'])
            &&$this->valid_password ($data['password'])
            &&$this->valid_name($data['name'])
        ){
            return True;
        }else{
            return False;
        }
    }
    public function valid_email ($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === true) {
            return True ;
        } else {
            echo json_encode(array("status"=>"faild" , "message"=>json_encode($email)."is not a valid email address"));
            return False;
        }
    }
    public function exist_email ($email){
        if($this->model->getAdminByEmail($email)){
            return True;
        }else{
            echo json_encode(array("status"=>"faild" , "message"=>"this email already exist"));
            return False;
        }
    }
    public function valid_password ($password){
        if(strlen($password)>=6)
        {
            return True;
        }else{
            echo json_encode(array("status"=>"faild" , "message"=>"password must br greather 6 charcters"));
        }
    }
    public function encode_password ($password){
        return md5($password);
    }
    public function valid_name ($name){
        $pattern = "/^([A-Za-z]+)$/";
        if (preg_match($pattern, $name)){
            return True;
        }else{
            echo json_encode(array("status"=>"faild" , "message"=>"name must be charcters"));
            return False ;
        }
    }
    public function login (){
        if($_SERVER['REQUEST_METHOD']==="POST"){
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            $password = isset($_POST['password']) ? $_POST['password'] : "";
            if ($this->full_data (array('email' => $email,'password' => $password))){
                if($this->valid_email ($email)){
                    $password = $this->encode_password ($password);
                    $admin = $this->model->getAdminBydata($email,$password);
                    if ($admin != NULL){
                        $name = $admin['name'];
                        // create new card 
                        $card = create_card();
                        $data = array(
                            'email' => $email,
                            'password' => $password,
                            'name' => $name,
                            'card' => $card,
                        );
                        $id = $admin['id'];
                        //update card in DataBase 
                        if ($this->update_card ($id,$data)){
                            echo json_encode(array("status"=>"True" , "message"=>"please enter".json_encode($card)." in header"));
                        }
                    }else{
                        echo json_encode(array("status"=>"faild" , "message"=>"faild to login please enter true data"));
                    }
                }
            }
        }
    }
}
?>
