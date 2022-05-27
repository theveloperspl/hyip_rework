<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailTypoRequest;
use App\Http\Requests\UpdateEmailTypoRequest;
use App\Models\EmailTypo;

class EmailTypoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmailTypoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmailTypoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailTypo  $emailTypo
     * @return \Illuminate\Http\Response
     */
    public function show(EmailTypo $emailTypo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailTypo  $emailTypo
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTypo $emailTypo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmailTypoRequest  $request
     * @param  \App\Models\EmailTypo  $emailTypo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmailTypoRequest $request, EmailTypo $emailTypo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailTypo  $emailTypo
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTypo $emailTypo)
    {
        //
    }
}
