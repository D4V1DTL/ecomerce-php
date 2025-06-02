<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || true) { // El "|| true" fuerza JSON siempre
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Datos inválidos.',
                    'errors' => $exception->errors()
                ], 422);
            }
    
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Recurso no encontrado.'
                ], 404);
            }
    
            return response()->json([
                'message' => 'Error interno del servidor.',
                'error' => config('app.debug') ? $exception->getMessage() : null
            ], 500);
        }
    
        return parent::render($request, $exception);
    }
}