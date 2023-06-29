<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ArtisanSelfCommands extends Controller {
    
    public function optimize() {
        return Artisan::call( 'optimize' );
    }
    
}
