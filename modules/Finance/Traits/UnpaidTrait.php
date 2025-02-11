<?php

namespace Modules\Finance\Traits;

use App\Models\Tenant\BillOfExchange;
use App\Models\Tenant\BillOfExchangePayment;
use App\Models\Tenant\Document;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Person;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait UnpaidTrait
{

    public function transformRecords($records,$detail = null)
    {

        return $records->transform(function ($row, $key)  use($detail){
            $total_to_pay = $this->getTotalToPay($row);
            // $total_to_pay = (float)$row->total - (float)$row->total_payment;
            $delay_payment = null;
            $date_of_due = null;

            if ($total_to_pay > 0) {
                if ($row->document_type_id) {

                    $invoice = Invoice::where('document_id', $row->id)->first();
                    if ($invoice) {
                        $due =   Carbon::parse($invoice->date_of_due); // $invoice->date_of_due;
                        $date_of_due = $invoice->date_of_due->format('Y/m/d');
                        $now = Carbon::now();

                        if ($now > $due) {

                            $delay_payment = $now->diffInDays($due);
                        }
                    }
                }
            }

            $guides = null;
            $date_payment_last = '';
            $payments = [];
            if($detail){
                $payments = $this->getPayments($row);
            }
            if ($row->document_type_id) {
                $guides =  Dispatch::where('reference_document_id', $row->id)->orderBy('series')->orderBy('number', 'desc')->get()->transform(function ($item) {
                    return [
                        'id' => $item->id,
                        'external_id' => $item->external_id,
                        'number' => $item->number_full,
                        'date_of_issue' => $item->date_of_issue->format('Y-m-d'),
                        'date_of_shipping' => $item->date_of_shipping->format('Y-m-d'),
                        'download_external_xml' => $item->download_external_xml,
                        'download_external_pdf' => $item->download_external_pdf,
                    ];
                });

                $date_payment_last = DocumentPayment::where('document_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
            } else {
                $date_payment_last = SaleNotePayment::where('sale_note_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
            }
            $purchase_order = null;
            if ($row->type == 'document') {
                $document = Document::find($row->id);
                $web_platforms = $document->getPlatformThroughItems();
                $purchase_order = $document->purchase_order;
            } elseif ($row->type == 'sale_note') {
                $document = SaleNote::find($row->id);
                $web_platforms = $document->getPlatformThroughItems();
                $purchase_order = $document->purchase_order;
            } elseif ($row->type == 'bill_of_exchange') {
                $document = BillOfExchange::find($row->id);
                $web_platforms = [];
                $purchase_order = null;
            } else {
                $web_platforms = new \Illuminate\Database\Eloquent\Collection();
            }
            $customer_internal_code = null;
            $customer_trade_name  = null;
            $customer_zone_name = null;
            $customer_telephone = null;
            $customer_address = null;
            $seller_name = null;
            if ($document) {
                $customer_internal_code = $document->customer->internal_code;
                $customer_trade_name  = $document->customer->trade_name;
                $customer_telephone = $document->customer->telephone;
                $customer_address = $document->customer->address;
                $customer_id = $document->customer_id;
                $zone = Person::find($customer_id)->getZone();
                $seller_name = $document->user->name;
                if ($zone) {
                    $customer_zone_name = $zone->name;
                }
            }
            return [
                'id' => $row->id,
                'seller_name' => $seller_name,
                'date_of_issue' => $row->date_of_issue,
                'customer_name' => $row->customer_name,
                'customer_internal_code' => $customer_internal_code,
                'customer_telephone' => $customer_telephone,
                'customer_address' => $customer_address,
                'customer_zone_name' => $customer_zone_name,
                'customer_trade_name' => $customer_trade_name,
                'customer_id' => $row->customer_id,
                'number_full' => $row->number_full,
                'total' => number_format((float) $row->total, 2, ".", ""),
                'total_to_pay' => number_format($total_to_pay, 2, ".", ""),
                'type' => $row->type,
                'guides' => $guides,
                'payments' => $payments,
                'date_payment_last' => ($date_payment_last) ? $date_payment_last->date_of_payment->format('Y-m-d') : null,
                'delay_payment' => $delay_payment,
                'date_of_due' =>  $date_of_due,
                'currency_type_id' => $row->currency_type_id,
                'exchange_rate_sale' => (float)$row->exchange_rate_sale,
                "user_id" => $row->user_id,
                "username" => $row->username,
                "total_subtraction" => $row->total_subtraction,
                "total_payment" => $row->total_payment,
                "purchase_order" => $purchase_order,
                "web_platforms" => $web_platforms,
                "total_credit_notes" => $this->getTotalCreditNote($row),
            ];
        });
    }

    function getPayments($row){
        $id = $row->id;
        $type = $row->type;
        if($type == 'document'){
            $payments = DocumentPayment::where('document_id', $id);
        }else if($type == 'sale_note'){
            $payments = SaleNotePayment::where('sale_note_id', $id);
        }else if ($type == 'bill_of_exchange'){
            $payments = BillOfExchangePayment::where('bill_of_exchange_id', $id);
        }else{
            return [];
        }

        $payments = $payments->get()->transform(function($row){
            return [
                'id' => $row->id,
                'date_of_payment' => $row->date_of_payment->format('Y-m-d'),
                'payment' => $row->payment,
                'payment_method_type_description' => $row->payment_method_type->description,
            ];
        });

        return $payments;

    }
    /**
     * Obtener total por cobrar
     *
     * @param  object $row
     * @return float
     */
    public function getTotalToPay($row)
    {
        return (float)$row->total - (float)$row->total_payment - (float) $this->getTotalCreditNote($row);
    }


    /**
     * Validar y obtener total nota credito
     *
     * @param  object $row
     * @return float
     */
    public function getTotalCreditNote($row)
    {
        return ($row->total_credit_notes ?? 0);
    }
}
