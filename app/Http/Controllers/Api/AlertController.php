<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para depuración


class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las alertas
            // Es crucial que la columna 'ubicacion' se lea de forma que Angular pueda entenderla.
            // MySQL guarda POINT como un tipo binario.
            // Usamos ST_X (longitude) y ST_Y (latitude) para extraer las coordenadas.
            $alerts = Alert::select(
                'id',
                'incident_type as incidentType',
                DB::raw('ST_Y(ubicacion) AS latitude'),   // Latitud (Y)
                DB::raw('ST_X(ubicacion) AS longitude'),  // Longitud (X)
                'created_at'
            )->orderBy('created_at', 'desc')->get(); // Ordenar por las más recientes

            return response()->json([
                'message' => 'Alertas obtenidas exitosamente',
                'alerts' => $alerts
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener alertas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error interno del servidor al obtener alertas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                
                'incidentType' => 'nullable|string|max:50',

            ]);
            
            // Asegúrate que los números no tengan comas para MySQL, solo puntos decimales.
            $pointWkt = "POINT({$validatedData['longitude']} {$validatedData['latitude']})";

            // Crear la alerta en la base de datos
            // $alert = Alert::create($validatedData);
            $alert = Alert::create([
                
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'incident_type' => $validatedData['incidentType'],
                // Usamos ST_SRID para especificar el Spatial Reference Identifier (SRID), 4326 es común para WGS84 (GPS)
                'ubicacion' =>  DB::raw("ST_GeomFromText('{$pointWkt}', 4326)"),
                // NOTA: MySQL POINT usa (longitude, latitude) - ¡es lo opuesto a como se piensa normalmente!
            ]);

            // Opcional: registrar la alerta para depuración
            Log::info('Nueva alerta recibida:', $alert->toArray());

            // Devolver una respuesta exitosa
            return response()->json([
                'message' => 'Alerta creada con éxito',
                'alert' => $alert
            ], 201); // 201 Created

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación
            Log::error('Error de validación al crear alerta:', $e->errors());
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            // Capturar otros errores
            Log::error('Error inesperado al crear alerta:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
