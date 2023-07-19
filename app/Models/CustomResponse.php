<?php

namespace App\Models;

class CustomResponse
{
    private $status;
    private $data;
    private $message;
    private $error;
    private $version = '1.0';

    function __construct($status, $data, $message,  $error)
    {
        $this->status  = $status;
        $this->data    = $data;
        $this->message = $message;
        $this->error   = $error;
    }

    function get(): array
    {
        return [
            "status"  => $this->status,
            "message" => $this->message,
            "data"    => $this->data,
            "error"   => $this->error,
            "version" => $this->version
        ];
    }
}
