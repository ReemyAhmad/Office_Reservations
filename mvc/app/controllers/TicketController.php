<?php
// app/controllers/TicketController.php
class TicketController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function index() {
        $cities = $this->model->getTicket();
        
    }

    public function addTicket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $company_id=$_POST['company_id'];
            $city_id=$_POST['city_id'];
            $date_s=$_POST['date_s'];
            $date_e=$_POST['date_e'];
            $data = [
                'company_id' => $company_id ,
                'city_id' => $city_id ,
                'date_s' => $date_s ,
                'date_e' => $date_e ,
            ];

            if ($this->model->addTicket($data)) {
                echo json_encode(array('status'=>'ok','data'=>$data));
            } else {
                echo "soory";
            }
        }
    }

    public function deleteTicket($id){
        $this->model->where('id',$id)->delete();
    }
}
?>
