<?php

namespace Lacasera\HttpLogger\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Lacasera\HttpLogger\LogProfile;
use Lacasera\HttpLogger\LogWriter;

class HttpLogger
{
    protected $logProfile;

    protected $logWriter;

    public function __construct(LogProfile $logProfile, LogWriter $logWriter)
    {
        $this->logProfile = $logProfile;
        $this->logWriter = $logWriter;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->logProfile->shouldLogRequest($request)) {
            $request->merge(['uniqueId' => uniqid('req_')]);
            $this->logWriter->logRequest($request);
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $this->logWriter->logResponse($request, $response);
    }

}
