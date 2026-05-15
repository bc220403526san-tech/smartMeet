<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $organizerId = auth()->id();
        $today       = Carbon::today()->toDateString();
        $tz          = auth()->user()->timezone ?? 'Asia/Karachi';


    }
}
