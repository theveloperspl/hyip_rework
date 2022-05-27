<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailVerificationRequest;
use App\Http\Requests\UpdateEmailVerificationRequest;
use App\Models\EmailVerification;

class EmailVerificationController extends Controller
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
     * @param  \App\Http\Requests\StoreEmailVerificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmailVerificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailVerification  $emailVerification
     * @return \Illuminate\Http\Response
     */
    public function show(EmailVerification $emailVerification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailVerification  $emailVerification
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailVerification $emailVerification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmailVerificationRequest  $request
     * @param  \App\Models\EmailVerification  $emailVerification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmailVerificationRequest $request, EmailVerification $emailVerification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailVerification  $emailVerification
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailVerification $emailVerification)
    {
        //
    }
}
