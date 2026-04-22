<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Hospital;
use App\Models\Corporate;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function schools() {
        return School::all();
    }
    
    public function hospitals() {
        return Hospital::all();
    }
    
    public function corporates() {
        return Corporate::all();
    }
}