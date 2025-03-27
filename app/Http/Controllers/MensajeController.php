<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MensajeController extends Controller
{
    // Guarda un mensaje desde la interfaz y lo almacena en la base de datos
    public function guardarMensaje(Request $request)
    {
        $mensaje = $request->input('mensaje');

        DB::table('mensajes')->insert([
            'contenido' => $mensaje,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Mensaje guardado']);
    }

    // Envía el último mensaje guardado a todos los usuarios de la base de datos
    public function enviarMensaje()
    {
        $apikey = "gswp459jpy8x4wkf";
        $instanceId = "instance110083";
        $url = "https://api.ultramsg.com/instance110083/messages/chat";

        // Obtener el último mensaje guardado
        $mensaje = DB::table('mensajes')->latest('id')->first();
        if (!$mensaje) {
            return response()->json(['error' => 'No hay mensajes guardados']);
        }

        // Obtener los números de teléfono de la base de datos
        $numeros = DB::table('alumnos')->pluck('celular');

        foreach ($numeros as $numero) {
            // Validar que el número tenga el formato internacional correcto
            if (!preg_match('/^\+\d{10,15}$/', $numero)) {
                Log::warning("Número inválido omitido: $numero");
                continue; // Si el número no es válido, lo omite
            }

            // Enviar el mensaje a cada número
            $response = Http::post($url, [
                "token" => $apikey,
                "to" => $numero,
                "body" => $mensaje->contenido,
            ]);

            // Registrar errores en el log si la API falla
            if ($response->failed()) {
                Log::error("Error al enviar mensaje a $numero: " . $response->body());
            } else {
                Log::info("Mensaje enviado a $numero correctamente.");
            }

            // Esperar 2 segundos entre envíos para evitar bloqueos
            sleep(2);
        }

        return response()->json(['success' => true, 'message' => 'Mensajes enviados']);
    }
}
