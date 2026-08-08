<?php
declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    /**
     * Display the website home page.
     */
    public function index(): void
    {
        view('website.home', ['title' => 'Welcome to Clinic Management System']);
    }
}
