<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Blog extends ResourceController
{
    protected $modelName = 'App\Models\BlogModel';
    protected $format = 'json';

    /**
     * Get All Blogs
     */
    public function index()
    {
        $posts = $this->model->findAll();
        return $this->respond($posts);
    }

    /**
     * Create Blog
     */
    public function create()
    {
        helper(['form']);
        $rules = [
            'title' => 'required|min_length[2]',
            'description' => 'required'
        ];

        if (!$this->validate($rules)) :
            return $this->failValidationErrors($this->validator->getErrors());
        endif;

        $data = [
            'post_title' => $this->request->getVar('title'),
            'post_description' => $this->request->getVar('description')
        ];
        $post_id = $this->model->insert($data);
        $data['post_id'] = $post_id;
        return $this->respondCreated($data);
    }

    /**
     * Get Blog
     */
    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $this->respond($data);
    }

    /**
     * Update Blog
     */
    public function update($id = null)
    {
        helper(['form']);
        $rules = [
            'title' => 'required|min_length[2]',
            'description' => 'required'
        ];
        if (!$this->validate($rules)) :
            return $this->failValidationErrors($this->validator->getErrors());
        endif;

        $input = $this->request->getRawInput();
        $data = [
            'post_id' => $id,
            'post_title' => $input["title"],
            'post_description' => $input["description"],
        ];

        $this->model->save($data);
        return $this->respond($data);
    }

    /**
     * Delete Blog
     */
    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if(!empty($data)):
            $this->model->delete($id);
            return $this->respondDeleted($data);
        endif;
        return $this->failNotFound('Item Not Found.');
    }
}
