<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * BaseController constructor to handle session expiration logic.
     */
    public function __construct()
    {
        // Run session check for all controllers that extend BaseController
        $this->checkSession();
    }

  /**
     * Check if the session has expired and handle logout and response.
     */
    public function checkSession()
    {
        // Check if 'unique_id' session variable is missing or empty
        if (session('unique_id') == '') {
            // If session is expired, we don't redirect immediately, just set the response
            abort(401, 'Session Expired! Please Login again.');
        }
    }
}
