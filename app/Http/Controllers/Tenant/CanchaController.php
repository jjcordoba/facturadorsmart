<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cancha;
use App\Models\Tenant\CanchasTipo;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class CanchaController extends Controller
{
    public function records(Request $request)
    {
        $query = Cancha::query();
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('reservante_nombre', 'LIKE', "%$search%")
                  ->orWhere('ticket', 'LIKE', "%$search%");
            });
        }
    
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query->whereBetween('fecha_reserva', [$startDate, $endDate]);
        }
    
        if ($request->has('tipo_reserva') && !empty($request->input('tipo_reserva'))) {
            $tipoReserva = $request->input('tipo_reserva');
            $query->whereHas('canchasTipo', function ($q) use ($tipoReserva) {
                $q->where('nombre', $tipoReserva);
            });
        }
    
        $records = $query->get();
    
        foreach ($records as $record) {
            $qrCode = new QrCode($record->ticket);
            $qrCode->setSize(300);
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode);
    
            $record->qr_code = $qrCodeImage->getDataUri();
        }
    
        // Cargar todos los tipos de reservas
        $tiposReservas = CanchasTipo::all();
    
        return view('canchas.index', compact('records', 'tiposReservas'));
    }

    public function reservaciones(Request $request)
    {
        $query = Cancha::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('reservante_nombre', 'LIKE', "%$search%")
                  ->orWhere('reservante_apellidos', 'LIKE', "%$search%");
        }

        $records = $query->get();

        foreach ($records as $record) {
            $qrCode = new QrCode($record->ticket);
            $qrCode->setSize(300);
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode);

            $record->qr_code = $qrCodeImage->getDataUri();
        }

        // Cargar todos los tipos de reservas
        $tiposReservas = CanchasTipo::all();

        return view('canchas.reservaciones', compact('records', 'tiposReservas'));
    }

    public function record($id)
    {
        $record = Cancha::findOrFail($id);
        $qrCode = new QrCode($record->ticket);
        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode);
        $qrCodeDataUri = $qrCodeImage->getDataUri();

        $record->qr_code = $qrCodeDataUri;

        return view('canchas.show', compact('record'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer',
            'reservante_nombre' => 'required|string|max:255',
            'reservante_apellidos' => 'required|string|max:255',
            'hora_reserva' => 'required|date_format:H:i',
            'fecha_reserva' => 'required|date',
            'tiempo_reserva' => 'required|integer',
        ]);

        $cancha = new Cancha();
        $cancha->nombre = $request->input('nombre');
        $cancha->ubicacion = $request->input('ubicacion');
        $cancha->capacidad = $request->input('capacidad');
        $cancha->reservante_nombre = $request->input('reservante_nombre');
        $cancha->reservante_apellidos = $request->input('reservante_apellidos');
        $cancha->hora_reserva = $request->input('hora_reserva');
        $cancha->fecha_reserva = $request->input('fecha_reserva');
        $cancha->tiempo_reserva = $request->input('tiempo_reserva');
        $cancha->ticket = $this->generateTicket();

        // Generate QR code and save to file
        $qrCode = new QrCode($cancha->ticket);
        $qrCode->setSize(300);
        $writer = new PngWriter();
        $qrCodeResult = $writer->write($qrCode);

        $qrCodePath = 'qr-codes/' . $cancha->ticket . '.png';
        $qrCodeResult->saveToFile(public_path($qrCodePath));

        $cancha->qr_code_path = $qrCodePath; // Guarda la ruta del código QR en la base de datos
        $cancha->save();

        return redirect()->route('tenant.canchas.index')->with('success', 'Cancha agregada con éxito');
    }

    private function generateTicket()
    {
        return strtoupper(bin2hex(random_bytes(4)));
    }

    public function destroy($id)
    {
        $cancha = Cancha::findOrFail($id);
        $cancha->delete();

        return redirect()->route('tenant.canchas.index')->with('success', 'Cancha eliminada con éxito');
    }
    public function publicStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer',
            'reservante_nombre' => 'required|string|max:255',
            'reservante_apellidos' => 'required|string|max:255',
            'hora_reserva' => 'required|date_format:H:i',
            'fecha_reserva' => 'required|date',
            'tiempo_reserva' => 'required|integer',
        ]);
    
        // Check if the time slot is already taken
        $existingReservation = Cancha::where('fecha_reserva', $request->input('fecha_reserva'))
            ->where('hora_reserva', $request->input('hora_reserva'))
            ->first();
    
        if ($existingReservation) {
            return response()->json(['success' => false, 'message' => 'Hora no disponible'], 409);
        }
    
        $cancha = new Cancha();
        $cancha->nombre = $request->input('nombre');
        $cancha->ubicacion = $request->input('ubicacion');
        $cancha->capacidad = $request->input('capacidad');
        $cancha->reservante_nombre = $request->input('reservante_nombre');
        $cancha->reservante_apellidos = $request->input('reservante_apellidos');
        $cancha->hora_reserva = $request->input('hora_reserva');
        $cancha->fecha_reserva = $request->input('fecha_reserva');
        $cancha->tiempo_reserva = $request->input('tiempo_reserva');
        $cancha->ticket = $this->generateTicket();
    
        // Generate QR code and save to file
        $qrCode = new QrCode($cancha->ticket);
        $qrCode->setSize(300);
        $writer = new PngWriter();
        $qrCodeResult = $writer->write($qrCode);
    
        $qrCodePath = 'qr-codes/' . $cancha->ticket . '.png';
        $qrCodeResult->saveToFile(public_path($qrCodePath));
    
        $cancha->qr_code_path = $qrCodePath; // Guarda la ruta del código QR en la base de datos
        $cancha->save();
    
        return response()->json(['success' => true, 'cancha' => $cancha]);
    }
    

    public function storeCanchasTipo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer',
        ]);

        $canchaTipo = new CanchasTipo();
        $canchaTipo->nombre = $request->input('nombre');
        $canchaTipo->ubicacion = $request->input('ubicacion');
        $canchaTipo->capacidad = $request->input('capacidad');
        $canchaTipo->save();

        return redirect()->route('tenant.canchas.index')->with('success', 'Tipo de reserva agregada con éxito');
    }

    public function filterCanchasTipo(Request $request)
    {
        $query = CanchasTipo::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'LIKE', "%$search%")
                  ->orWhere('ubicacion', 'LIKE', "%$search%")
                  ->orWhere('capacidad', 'LIKE', "%$search%");
        }

        $tiposReservas = $query->get();

        return view('canchas.tipo_reservas_list', compact('tiposReservas'));
    }
}
