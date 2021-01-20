<?php

namespace Lacasera\HttpLogger;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface LogWriter
{
    public function logRequest(Request $request);

    public function logResponse(Request $request, $response);
}
