<?php

namespace App;
use App\UsersRepository;
use App\TransaksiRepository;

class CrudUsersServices
{
    protected $listrepo;protected $userTransRepo;

    public function __construct(){
        $this->listrepo = new UsersRepository;
    }


    //tampilkan list 
    public function index(){
        $results =  $this->listrepo->getUsers();
        return $results;
    }

}
