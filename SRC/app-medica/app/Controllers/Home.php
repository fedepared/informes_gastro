<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');

        
    }
    public function formulario(): string
    {
        return view('formulario');

        
    }
    public function coberturas(): string
    {
        return view('coberturas');

        
    }
    public function reportes(): string
    {
        return view('reportes');

        
    }
}
?>
