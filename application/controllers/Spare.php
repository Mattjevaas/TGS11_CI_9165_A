<?php
use Restserver\Libraries\REST_Controller;
class Spare extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        
        parent::__construct();
        $this
            ->load
            ->model('SpareModel');
        $this
            ->load
            ->library('form_validation');
    }
    public function index_get()
    {
        return $this->returnData($this
            ->db
            ->get('spareparts')
            ->result() , false);
    }
    public function index_post($id = null)
    {
        $validation = $this->form_validation;
        $rule = $this
            ->SpareModel
            ->rules();
        if ($id == null)
        {
            array_push($rule, ['field' => 'merk', 'label' => 'merk', 'rules' => 'required'], ['field' => 'amount', 'label' => 'amound', 'rules' => 'required']);
        }

        $validation->set_rules($rule);
        if (!$validation->run())
        {
            return $this->returnData($this
                ->form_validation
                ->error_array() , true);
        }
        $spare = new UserData();
        $spare->name = $this->post('name');
        $spare->amount = $this->post('amount');
        $spare->merk = $this->post('merk');
        if ($id == null)
        {
            $response = $this
                ->SpareModel
                ->store($spare);
        }
        else
        {
            $response = $this
                ->SpareModel
                ->update($spare, $id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null)
    {
        if ($id == null)
        {
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this
            ->SpareModel
            ->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }
    public function returnData($msg, $error)
    {
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }
}
class UserData
{
    public $name;
    public $merk;
    public $amount;
}

