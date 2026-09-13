<?php

use Cloudinary\Cloudinary;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    public function uploadImage(string $filePath)
    {
        return $this->cloudinary
            ->uploadApi()
            ->upload($filePath, [
                'folder' => 'employee_profiles',
                'resource_type' => 'image',
            ]);
    }

    public function deleteImage(string $id):bool{
        $result =
        $this->cloudinary
            ->uploadApi()
            ->destroy($id, [
                'resource_type' => 'image'
            ]);

        return isset($result['result'])
            && $result['result'] === 'ok';
    }

}