<?php
use Restserver\Libraries\REST_Controller;
class Spare extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Authorization,Content-Type, ContentLength, Accept-Encoding");
        
        parent::__construct();
        $this
            ->load
            ->model('SpareModel');
        $this
            ->load
            ->library('form_validation');
        $this->load->helper(['jwt', 'authorization']); 
    }
    public function index_get()
    {
        $data = $this->verify_data();

        if($data)
        {
            return $this->returnData($this
                ->db
                ->get('spareparts')
                ->result() , false);
        }
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
    }

    public function index_post($id = null)
    {

        $data = $this->verify_data();

        if($data)
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
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
    }

    public function index_delete($id = null)
    {
        $data = $this->verify_data();

        if($data)
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
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
    }
    public function returnData($msg, $error)
    {
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }

    public function verify_data()
    {
        // $cookie = $this->input->cookie('TOKEN',true);

        // if(!empty($cookie))
        // {
        //     $token = $cookie;
        // }
        // else
        // {
        //     return false;
        // }

        $headers = $this->input->request_headers();

        if(!empty($headers['Authorization']))
        {
            $token = $headers['Authorization'];
        }
        else
        {
            return false;
        }


        try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            $data2 = AUTHORIZATION::validateTimestamp($token);

            if ($data === false || $data2 === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                return false;
            } 
            else 
            {
                return $data;
            }
        }
        catch (Exception $e) 
        {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            return false;
        }
    }
}
class UserData
{
    public $name;
    public $merk;
    public $amount;
}

