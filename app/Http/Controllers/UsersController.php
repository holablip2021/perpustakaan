<?php

namespace App\Http\Controllers;

use App\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    protected $userRepo;

    //
    public function __construct()
    {
        $this->userRepo = new UsersRepository;
        session()->regenerate();
        session()->reflash('status');
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        session()->flush();
        return redirect(url('/pengguna/login'));
    }

    public function cekLogin(Request $request)
    {
        $results = $this->userRepo->validasi_user($request->post());
        if (!$results) {
            return redirect(url('/pengguna/login'));
        }
        session()->flash('status', $results['pesan']);
        return redirect('/');
    }    

    public function index()
    {
        $results =  $this->userRepo->getUsers();
        return view('listing-users', compact('results'));
    }

    public function registrasi(Request $request)
    {   
        $results = $this->userRepo->create($request);
        if (!$results) {
            return redirect()->back();
        }
        session()->flash('status', $results['pesan']);
        return view('login',compact('results'));
    }


    public function transaksi()
    {
        $results = $this->userRepo->transaksiMember(session()->get('user_id'));
        if(!$results){
            return redirect()->back();
        }
        return view('listing-transaksi', compact('results'));
    }
    
}
