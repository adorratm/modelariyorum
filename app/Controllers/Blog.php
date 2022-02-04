<?php

namespace App\Controllers;

use CodeIgniter\Images\Exceptions\ImageException as ExceptionsImageException;
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
        helper(['form', 'tools']);
        $rules = [
            'title' => 'required|min_length[2]',
            'description' => 'required',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,5120]|is_image[featured_image]'
        ];

        if (!$this->validate($rules)) :
            return $this->failValidationErrors($this->validator->getErrors());
        endif;

        /**
         * Get File
         */
        $file = $this->request->getFile('featured_image');
        if (!$file->isValid()) :
            return $this->failValidationErrors($file->getErrorString());
        endif;
        $newName = $file->getRandomName();
        $file->move("./public/uploads", $newName);
        $newName = pathinfo("./public/uploads/" . $file->getName());
        /**
         * Image Manipulation
         */
        if ($file->hasMoved()) :
            $webpImage =  Webp2("./public/uploads/" . $file->getName());

        endif;

        $data = [
            'post_title' => $this->request->getVar('title'),
            'post_description' => $this->request->getVar('description'),
            'post_featured_image' => ($webpImage == NULL ? NULL : $newName["filename"] . ".webp")
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
        helper(['form', 'array', 'tools']);
        $rules = [
            'title' => 'required|min_length[2]',
            'description' => 'required',
        ];
        $fileName = dot_array_search('featured_image.name', $_FILES);
        if (!empty($fileName)) :
            $img = ['featured_image' => 'uploaded[featured_image]|max_size[featured_image,5120]|is_image[featured_image]'];
            $rules = array_merge($rules, $img);
        endif;

        if (!$this->validate($rules)) :
            return $this->failValidationErrors($this->validator->getErrors());
        endif;
        /**
         * Get Old Data
         */
        $oldData = $this->model->find($id);

        $data = [
            'post_id' => $id,
            'post_title' => $this->request->getVar("title"),
            'post_description' => $this->request->getVar("description"),
        ];

        /**
         * Get File
         */
        if (!empty($fileName)) :
            $file = $this->request->getFile('featured_image');
            if (!$file->isValid()) :
                return $this->failValidationErrors($file->getErrorString());
            endif;
            $newName = $file->getRandomName();
            $file->move("./public/uploads", $newName);
            $newName = pathinfo("./public/uploads/" . $file->getName());
            /**
             * Image Manipulation
             */
            if ($file->hasMoved()) :
                $webpImage =  Webp2("./public/uploads/" . $file->getName());
                /**
                 * Unlink Old Source
                 */
                if (file_exists("./public/uploads/" . $oldData["post_featured_image"])) :
                    @unlink("./public/uploads/" . $oldData["post_featured_image"]);
                endif;
                $data["post_featured_image"] = ($webpImage == NULL ? NULL : $newName["filename"] . ".webp");
            endif;
        endif;

        $this->model->save($data);
        return $this->respond($data);
    }

    /**
     * Delete Blog
     */
    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if (!empty($data)) :
            $this->model->delete($id);
            /**
             * Unlink Old Source
             */
            if (file_exists("./public/uploads/" . $data["post_featured_image"])) :
                @unlink("./public/uploads/" . $data["post_featured_image"]);
            endif;
            return $this->respondDeleted($data);
        endif;
        return $this->failNotFound('Item Not Found.');
    }
}
