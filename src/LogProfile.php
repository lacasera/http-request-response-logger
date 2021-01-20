<?php

namespace Lacasera\HttpLogger;

use Illuminate\Http\Request;

interface LogProfile
{
    public function shouldLogRequest(Request $request): bool;

}
