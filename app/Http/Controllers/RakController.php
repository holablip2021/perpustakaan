<?php

namespace App\Http\Controllers;

use App\RakRepository;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
class RakController extends Controller
{
    protected $crudservices, $rakRepo;

    //
    public function __construct()
    {
        $this->rakRepo = new RakRepository;
        session()->reflash('status');
    }

    public function index()
    {
        $results = $this->rakRepo->getRak();
        return view('listing-rak', compact('results'));
    }

    public function edit($id)
    {
        $results = $this->rakRepo->findById($id);
        return view('form-edit-rak',compact('results'));
    }

    public function createAndUpdateRak($id = null, Request $request)
    {

        $results = $this->rakRepo->createAndUpdateRak($id,$request);
        if (!$results) {
            return redirect()->back();
        }
        session()->flash('status', $results['pesan']);
        return redirect(url('/rak/list'));
    }

}
