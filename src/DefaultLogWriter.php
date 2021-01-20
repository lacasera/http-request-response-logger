<?php

namespace Lacasera\HttpLogger;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DefaultLogWriter implements LogWriter
{
    public function logRequest(Request $request)
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $headersAsJson = json_encode($request->headers->all());

        $files = (new Collection(iterator_to_array($request->files)))
            ->map([$this, 'flatFiles'])
            ->flatten()
            ->implode(',');

        $message = "{$method} {$uri} - Body: {$bodyAsJson} - Headers: {$headersAsJson} - Files: ".$files;

        Log::info($message);
    }

    public function logResponse(Request $request, $response)
    {
        $responseAsJson = json_encode(collect($response->getOriginalContent(true))->except(config('http-logger.except'))->all());

        $headers = json_encode($response->headers->all());

        $requestId = $request->uniqueId;

        $status = $response->status();

        $message = "requestId: {$requestId} - status: {$status} - Body: {$responseAsJson} - Headers: {$headers}";

        if ($response->isSuccessful()) {
            Log::info($message);
        } else {
            Log::error($message);
        }

    }

    public function flatFiles($file)
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }
        if (is_array($file)) {
            return array_map([$this, 'flatFiles'], $file);
        }

        return (string) $file;
    }
}
