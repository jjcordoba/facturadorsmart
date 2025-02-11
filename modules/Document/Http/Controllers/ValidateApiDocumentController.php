<?php

namespace Modules\Document\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Tenant\Series;
use Illuminate\Http\Response;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use App\Models\Tenant\StateType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Establishment;
use Modules\Services\Data\ServiceData;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant\Catalogs\DocumentType;
use App\CoreFacturalo\Services\Extras\ValidateCpe2;
use Modules\Document\Http\Requests\ValidateDocumentRequest;
use Modules\Document\Http\Requests\ValidateApiDocumentsRequest;
use Modules\Document\Http\Resources\ValidateDocumentsCollection;

class ValidateApiDocumentController extends Controller
{
    protected $document_state = [
        '-' => '-',
        '0' => 'No Existe',
        '1' => 'Aceptado',
        '2' => 'Anulado',
        '3' => 'AUTORIZADO',
        '4' => 'NO AUTORIZADO'
    ];

    public function index()
    {
        return view('document::validate_documents.index');
    }

    public function records(ValidateApiDocumentsRequest $request)
    {
        $records = $this->getRecords($request);
        $validate_documents = $this->validateDocuments($records);

        return new ValidateDocumentsCollection($validate_documents);
    }

    public function validateDocumentsTxt(Request $request)
    {
        reValidate:
        $company = Company::first();
        $service = new ServiceData();

        $filename = $request->numero . "_" . $company->number . "_validarcpe.txt";
        if (file_exists(public_path("storage/txt/{$filename}"))) {
            $file = public_path("storage/txt/{$filename}");
            $res = $service->validar_cpe($company->number, substr($company->soap_username, 11, 8), $company->soap_password, $filename);    
            $data_response = json_decode($res);

            if (array_key_exists('data', json_decode($res, true))) {
                $res = json_decode($res);
                $data = $res->data;

                foreach ($data as $key => $value) {
                    $sale_find = Document::where('series', $value->Serie)->where('number', $value->Numero)->where('document_type_id', $value->TipoComprobante)->first();
                    $sales = Document::findOrFail($sale_find->id);
                    if ($value->EstadoComprobante == "NO EXISTE") {
                        $state_type_id = "01";
                    } else {
                        $state_type = StateType::where('description', 'like', "%{$this->document_state[$value->EstadoCodComprobante]}%")->first();
                        $state_type_id = $state_type->id;
                    }

                    $sales->state_type_id = $state_type_id;
                    $sales->save();
                }
            }
            return response()->json($res);
        } else {
            goto reValidate;
            return response()->json(["success" => false, "message" => $data_response]);
        }
    }

    public function validate_masivo(Request $request)
    {
        $records = $this->getValidate($request);
        return $records;
    }

    public function getValidate(Request $request)
    {
        $company = Company::first();
        $document_type_id = $request['document_type_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $user_id = $request['user_id'];
        $period = $request['period'];
        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start . '-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end . '-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        $records = Document::where('document_type_id', $document_type_id)->whereBetween('date_of_issue', [$d_start, $d_end])->whereIn('state_type_id', ['01', '03', '05', '07']);
        $correlativo = 0;
        $conteo = 0;
        $contenido = "";
        $cantidad_rows = $records->count();
        $num_cpe = $cantidad_rows > 1 ? 0 : -1;

        if ($records->count() == 0) {
            return [
                "success" => false,
                "message" => "No hay comprobantes para validar"
            ];
        } else {
            foreach ($records->get() as $row) {
                if ($num_cpe == $cantidad_rows) {
                    break;
                } else {
                    $conteo = $conteo + 1;
                    $contenido .= $company->number . "|";
                    $contenido .= $row->document_type_id . "|";
                    $contenido .= $row->series . "|";
                    $contenido .= intval($row->number) . "|";
                    $contenido .= substr($row->date_of_issue, 8, 2) . "/" . substr($row->date_of_issue, 5, 2) . "/" . substr($row->date_of_issue, 0, 4) . "|";
                    $contenido .= $row->total . "\n";
                    if ($conteo == 250) {
                        Storage::disk('public')->put("txt/" . $correlativo . "_" . $company->number . "_validarcpe.txt", $contenido);
                        $correlativo++;
                        $conteo = -1;
                        $contenido = "";
                    }
                    Storage::disk('public')->put("txt/" . $correlativo . "_" . $company->number . "_validarcpe.txt", $contenido);
                    $success = true;
                }
            }
        }
        return [
            "success" => true,
            "archivo_txt" => $correlativo + 1,
            "cantidad_cpe" => $records->count(),
            "message" => "Se generaron los archivos txt correctamente"
        ];
    }

    public function generateTxtFilesForValidationById(Request $request)
    {
        try {
            $company = Company::first();
            $document_id = $request->input('document_id');

            if (empty($document_id)) {
                return response()->json([
                    "success" => false,
                    "message" => "No se proporcionó el ID del documento para validar"
                ]);
            }

            $record = Document::where('id', $document_id)
                ->whereIn('state_type_id', ['01', '03', '05', '07'])
                ->first();

            if (is_null($record)) {
                return response()->json([
                    "success" => false,
                    "message" => "No hay comprobante para validar"
                ]);
            }

            $correlativo = 0;
            $contenido = "";

            $contenido .= $company->number . "|";
            $contenido .= $record->document_type_id . "|";
            $contenido .= $record->series . "|";
            $contenido .= intval($record->number) . "|";
            $contenido .= substr($record->date_of_issue, 8, 2) . "/" . substr($record->date_of_issue, 5, 2) . "/" . substr($record->date_of_issue, 0, 4) . "|";
            $contenido .= $record->total . "\n";

            Storage::disk('tenant')->put("txt/" . $correlativo . "_" . $company->number . "_validarcpe.txt", $contenido);

            // Realizar la validación
            $service = new ServiceData();
            $filename = $correlativo . "_" . $company->number . "_validarcpe.txt";
            if (file_exists(public_path("storage/txt/{$filename}"))) {
                $file = public_path("storage/txt/{$filename}");
                $res = $service->validar_cpe($company->number, substr($company->soap_username, 11, 8), $company->soap_password, $filename);    
                $data_response = json_decode($res);

                if (array_key_exists('data', json_decode($res, true))) {
                    $res = json_decode($res);
                    $data = $res->data;

                    foreach ($data as $key => $value) {
                        $sale_find = Document::where('series', $value->Serie)->where('number', $value->Numero)->where('document_type_id', $value->TipoComprobante)->first();
                        $sales = Document::findOrFail($sale_find->id);
                        if ($value->EstadoComprobante == "NO EXISTE") {
                            $state_type_id = "01";
                        } else {
                            $state_type = StateType::where('description', 'like', "%{$this->document_state[$value->EstadoCodComprobante]}%")->first();
                            $state_type_id = $state_type->id;
                        }

                        $sales->state_type_id = $state_type_id;
                        $sales->save();
                    }
                }
                return response()->json([
                    "success" => true,
                    "archivo_txt" => $correlativo + 1,
                    "cantidad_cpe" => 1,
                    "message" => "Se generó el archivo txt y se realizó la validación correctamente"
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Error al validar el archivo TXT"
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al generar el archivo TXT: " . $e->getMessage()
            ]);
        }
    }

    public function countdocumennt(Request $request)
    {
        $company = Company::first();
        $document_type_id = $request['document_type_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $user_id = $request['user_id'];
        $period = $request['period'];
        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start . '-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end . '-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }
        $aceptados = Document::where('document_type_id', $document_type_id)->whereBetween('date_of_issue', [$d_start, $d_end])->where('state_type_id', '05')->count();
        $anulados = Document::where('document_type_id', $document_type_id)->whereBetween('date_of_issue', [$d_start, $d_end])->where('state_type_id', '11')->count();
        $registrados = Document::where('document_type_id', $document_type_id)->whereBetween('date_of_issue', [$d_start, $d_end])->where('state_type_id', '01')->count();
        return compact('aceptados', 'anulados', 'registrados');
    }

    public function validateDocuments($records)
    {
        $records_paginate = $records->paginate(config('tenant.items_per_page'));

        foreach ($records_paginate->getCollection() as $document) {
            $validate_cpe = new ValidateCpe2();
            $response = $validate_cpe->search($document->company->number,
                $document->document_type_id,
                $document->series,
                $document->number,
                $document->date_of_issue,
                $document->total
            );
            if ($response['success']) {
                $response_code = $response['data']['comprobante_estado_codigo'];
                $response_description = $response['data']['comprobante_estado_descripcion'];
                $message = $document->number_full . '|Código: ' . $response_code . '|Mensaje: ' . $response_description;
                $document->message = $message;
                $document->state_type_sunat_description = $response_description;
                $document->code = $response_code;
            }
        }
        return $records_paginate;
    }

    public function getRecords($request)
    {
        $start_number = $request->start_number;
        $end_number = $request->end_number;
        $document_type_id = $request->document_type_id;
        $series = $request->series;

        if ($end_number) {
            $records = Document::where('document_type_id', $document_type_id)
                ->where('series', $series)
                ->whereBetween('number', [$start_number, $end_number])
                ->latest();
        } else {
            $records = Document::where('document_type_id', $document_type_id)
                ->where('series', $series)
                ->where('number', $start_number)
                ->latest();
        }

        return $records;
    }

    public function data_table()
    {
        $document_types = DocumentType::whereIn('id', ['01', '03', '07', '08'])->get();
        $series = Series::whereIn('document_type_id', ['01', '03', '07', '08'])->get();
        return compact('document_types', 'series');
    }

    public function regularize(ValidateDocumentsRequest $request)
    {
        $document_state = [
            'ACEPTADO' => '05',
            'ENVIADO' => '03',
            'OBSERVADO' => '07',
            'RECHAZADO' => '09',
            'ANULADO' => '11',
            'POR ANULAR' => '13',
        ];

        $records = $this->getRecords($request)->get();

        DB::connection('tenant')->transaction(function () use ($records, $document_state) {
            foreach ($records as $document) {
                reValidate:
                $validate_cpe = new ValidateCpe2();
                $response = $validate_cpe->search($document->company->number,
                    $document->document_type_id,
                    $document->series,
                    $document->number,
                    $document->date_of_issue,
                    $document->total
                );

                if ($response['success']) {
                    $response_description = mb_strtoupper($response['data']['comprobante_estado_descripcion']);
                    $state_type_id = isset($document_state[$response_description]) ? $document_state[$response_description] : null;

                    if ($state_type_id) {
                        $document->state_type_id = $state_type_id;
                        $document->update();
                    }
                } else {
                    goto reValidate;
                }
            }
        });

        return [
            'success' => true,
            'message' => 'Estados regularizados correctamente'
        ];
    }
}
