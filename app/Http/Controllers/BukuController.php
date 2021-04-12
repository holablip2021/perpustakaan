<?php

namespace App\Http\Controllers;

use App\BukuRepository;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Session;

class BukuController extends Controller
{
    protected $listrepo;

    //
    public function __construct()
    {
        $this->listrepo = new BukuRepository;
        session()->reflash('status');
    }
    
    public function index()
    {
        $results =  $this->listrepo->getBuku();
        return view('listing-buku', compact('results'));
    }
}
