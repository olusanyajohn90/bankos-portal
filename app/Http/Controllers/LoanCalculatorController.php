<?php

namespace App\Http\Controllers;

class LoanCalculatorController extends Controller
{
    public function index()
    {
        return view('loans.calculator');
    }
}
