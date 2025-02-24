<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Report\Exports\NoPaidExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\QuotationCollection;
use Modules\Dashboard\Helpers\DashboardView;
use Modules\Finance\Traits\UnpaidTrait;
use Modules\Report\Exports\NoPaidDetailExport;

class ReportUnpaidController extends Controller
{

    use UnpaidTrait;

    public function excel(Request $request)
    {

        $records = (new DashboardView())->getUnpaidFilterUser($request->all())->get();
        $records = $this->transformRecords($records);

        $company = Company::first();
        $noPaidExport = new NoPaidExport();
        $noPaidExport
            ->company($company)
            ->records($records);
        return $noPaidExport->download('Reporte_Cuentas_Por_Cobrar' . Carbon::now() . '.xlsx');
    }

    public function excel_detail(Request $request)
    {

        $records = (new DashboardView())->getUnpaidFilterUser($request->all())->get();
        $records = $this->transformRecords($records,true);
        $company = Company::first();
        $noPaidExport = new NoPaidDetailExport();
        $noPaidExport
            ->company($company)
            ->records($records);
        return $noPaidExport->download('Reporte_Cuentas_Por_Cobrar_Detallado_' . Carbon::now() . '.xlsx');
    }
}
