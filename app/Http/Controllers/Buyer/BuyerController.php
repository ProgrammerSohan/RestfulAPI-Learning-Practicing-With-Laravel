<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class BuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $buyers = Buyer::has('transactions')->get();

        //return response()->json(['data'=> $buyers], 200);
        return $this->showAll($buyers);


    }

    public function show($id)
    {
        $buyer = Buyer::has('transactions')->findOrFail($id);

        //return response()->json(['data'=> $buyer], 200);
        return $this->showOne($buyer);

    }

}
