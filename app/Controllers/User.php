<?php

namespace App\Controllers;

use \App\Libraries\Oauth;
use \OAuth2\Request;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class User extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $oauth = new Oauth();
        $request = new Request();
        $respond = $oauth->server->handleTokenRequest($request->createFromGlobals());
        $code = $respond->getStatusCode();
        $body = $respond->getResponseBody();
        return $this->respond(json_decode($body), $code);
    }

    public function register()
    {
        helper("form");
        if ($this->request->getMethod() != 'post') :
            return $this->fail('Only post request is allowed.');
        endif;

        $rules = [
            'firstname' => 'required|min_length[2]|max_length[70]',
            'lastname' => 'required|min_length[2]|max_length[70]',
            'email' => 'required|valid_email|is_unique[users.email]|max_length[255]|min_length[2]',
            'password' => 'required|min_length[6]|max_length[255]',
            'password_confirm' => 'matches[password]',
        ];

        if(!$this->validate($rules)):
            return $this->failValidationErrors($this->validator->getErrors());
        endif;

        $model = new UserModel();
        $data = [
            'firstname' => $this->request->getVar('firstname'),
            'lastname' => $this->request->getVar('lastname'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
        ];

        $user_id = $model->insert($data);
        $data['id'] = $user_id;
        unset($data["password"]);
        return $this->respondCreated($data); 
    }
}
