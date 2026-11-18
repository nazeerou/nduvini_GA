<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    /* ================================
     | DASHBOARD / INDEX
     ================================= */
    public function index()
    {
        $senderIds = DB::table('sender_ids')->orderBy('id', 'desc')->get();
        $logs = DB::table('sms_logs')->orderBy('id', 'desc')->limit(50)->get();

        return view('sms.index', compact('senderIds', 'logs'));
    }

    /* ================================
     | SENDER ID MANAGEMENT
     ================================= */
    public function storeSenderId(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);

        DB::table('sender_ids')->insert([
            'name' => strtoupper($request->name),
            'status' => 'pending',
            'created_at' => now()
        ]);

        return back()->with('success', 'Sender ID submitted for approval');
    }

       /* ================================
     | NextSMS BULK API
     ================================= */
    
    public function sendSms(Request $request)
    {
        
        $request->validate([
            'sender_id' => 'required|exists:sender_ids,id',
            'message'   => 'required|string|max:1000',
            'mobile'   => 'required'
        ]);

        $sender = DB::table('sender_ids')->find($request->sender_id);

        
        if ($sender->status !== 'approved') {
            return back()->withErrors('Sender ID not approved');
        }

        // Convert numbers to array
        $numbers = preg_split('/[\r\n,]+/', $request->mobile);

        $recipients = [];
        foreach ($numbers as $mobile) {
            $mobile = trim($mobile);
            if ($mobile === '') continue;

            $recipients[] = [
                'mobile' => $mobile,
                'message' => $request->message
            ];
        }

        // SEND BULK
        $response = $this->sendViaNextSms($sender->name, $recipients);

        
        // LOG EACH SMS
        foreach ($recipients as $row) {
            DB::table('sms_logs')->insert([
                'sender_id' => $sender->id,
                'recipient' => $row['mobile'],
                'message'   => $row['message'],
                'channel'   => 'sms',
                'status'    => $response['status'],
                'created_at'=> now(),
                'updated_at'=> now()
            ]);
        }

        return redirect()->back()->with('sucess', 'SMS sent successfully');
    }

    private function sendViaNextSms($senderId, array $recipients)
    {
        $apiUrl = "https://messaging-service.co.tz/api/sms/v1/text/multi";
        $apiKey = 'naziru';
        $apiSecret = '271646njg@NJG';
        // $senderId = 'MANANI TECH';

        $messages = [];

        foreach ($recipients as $recipient) {
            $messages[] = [
                'from' => $senderId,
                'to'   => $recipient['mobile'],
                'text' => $recipient['message']
            ];
        }

         $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("$apiKey:$apiSecret"),
            'Content-Type' => 'application/json'
        ])->post($apiUrl, [
            "messages" => $messages
        ]);

        if ($response->successful()) {
            return [
                'status' => 'sent',
                'response' => $response->json()
            ];
        }

        \Log::error('SMS Failed', ['body' => $response->body()]);

        return [
            'status' => 'failed',
            'response' => null
        ];
    }


    /* ================================
     | BULK SMS VIA CSV
     ================================= */
    public function sendBulkSmsCsv(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:sender_ids,id',
            'csv_file'  => 'required|mimes:csv,txt',
            'message'   => 'required|string'
        ]);

        $sender = DB::table('sender_ids')->find($request->sender_id);

        $file = fopen($request->file('csv_file'), 'r');
        $recipients = [];

        while (($row = fgetcsv($file)) !== false) {
            if (!isset($row[0])) continue;

            $recipients[] = [
                'phone' => trim($row[0]),
                'message' => $request->message
            ];
        }
        fclose($file);

        $response = $this->sendViaNextSms($sender->name, $recipients);

        foreach ($recipients as $row) {
            DB::table('sms_logs')->insert([
                'sender_id' => $sender->id,
                'recipient' => $row['mobile'],
                'message'   => $row['message'],
                'channel'   => 'sms',
                'status'    => $response['status'],
                'created_at'=> now(),
                'updated_at'=> now()
            ]);
        }

        return back()->with('message', 'Bulk SMS sent via CSV successfully');
    }

    /* ================================
     | BULK SMS (CSV)
     ================================= */
    public function bulkSms(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:sender_ids,id',
            'csv_file' => 'required|file|mimes:csv,txt',
            'message' => 'required|string'
        ]);

        $sender = DB::table('sender_ids')->find($request->sender_id);
        $file = fopen($request->file('csv_file'), 'r');

        while (($row = fgetcsv($file)) !== false) {
            $phone = $row[0];

            $this->sendViaNextSms($sender->name, $phone, $request->message);

            DB::table('sms_logs')->insert([
                'sender_id' => $sender->id,
                'recipient' => $phone,
                'message' => $request->message,
                'channel' => 'sms',
                'status' => 'sent',
                'created_at' => now()
            ]);
        }

        fclose($file);

        return back()->with('message', 'SMS sent');
    }

    /* ================================
     | SMS REPORT
     ================================= */
    public function report(Request $request)
    {
        $logs = DB::table('sms_logs')
            ->when($request->channel, fn($q) =>
                $q->where('channel', $request->channel)
            )
            ->when($request->date, fn($q) =>
                $q->whereDate('created_at', $request->date)
            )
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('sms.report', compact('logs'));
    }

    /* ================================
     | SEND FROM INVOICE
     ================================= */
    public function sendInvoiceSms($invoiceId)
    {
        $invoice = DB::table('tax_invoices')->find($invoiceId);

        if (!$invoice) {
            return back()->withErrors('Invoice not found');
        }

        $message = "Invoice #{$invoice->invoice_no}, Amount: "
            . number_format($invoice->total, 2)
            . ". Thank you for your business.";

        $sender = DB::table('sender_ids')->where('status', 'approved')->first();

        $this->sendViaNextSms($sender->name, $invoice->customer_phone, $message);

        DB::table('sms_logs')->insert([
            'sender_id' => $sender->id,
            'recipient' => $invoice->customer_phone,
            'message' => $message,
            'channel' => 'sms',
            'status' => 'sent',
            'created_at' => now()
        ]);

        return back()->with('success', 'Invoice SMS sent');
    }

    /* ================================
     | NextSMS API (Tanzania)
     ================================= */
    // private function sendViaNextSms($sender, $to, $message)
    // {
       
    

    /* ================================
     | WhatsApp API (Meta Cloud)
     ================================= */
    private function sendViaWhatsApp($to, $message)
    {
        // Placeholder for Meta WhatsApp Cloud API
        return [
            'status' => 'sent',
            'message_id' => uniqid('wa_')
        ];
    }
}
