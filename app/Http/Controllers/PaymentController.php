<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller{
    private $token = "TOKEN";
    private $merchantId = "Metchend_ID";
    private $all_status = [
        'REGISTRED' => 1,
        'ON_PROGRESS' => 1,
        'PAID' => 2,
        'RETURNED' => 3,
        'DECLINED' => 4,
        'PAY_ERROR' => 4,
        'EXPIRED' => 4,
    ];
    public function createTransaction(Request $request){
        $UserID = $request->input('UserID');
        $oson = $request->input('oson');
        if ($UserID && $oson) {
            $courseData = [
                'Summa' => 1000,
                'UserID' => 'Elshod Musurmonov'
            ];
            if ($courseData) {
                $transactionId = (string) (time() + 1);
                $data = [
                    "merchant_id" => $this->merchantId,
                    "transaction_id" => $transactionId,
                    "user_account" => $transactionId,
                    "amount" => $courseData['Summa'],
                    "currency" => "UZS",
                    "comment" => "ATKO o'quv markazi kurslari uchun : {$courseData['UserID']} so'm to'lov qilmoqdasiz",
                    "return_url" => "https://atko.tech/osonpay/public/chekkinTransaction/".$transactionId,
                    "lifetime" => 30,
                    "lang" => "uz"
                ];
                $response = $this->curlPost("https://api.oson.uz/api/invoice/create", $data);
                $transaction_data = [
                    'transaction_id'=>$response['transaction_id'],        //TransactionID
                    'UserID'=>$courseData['UserID'],                 //UserID
                    'Summa'=>$courseData['Summa'],         //CoursPrice
                    'status'=>$this->all_status['REGISTRED'],                //Status
                ];
                Payment::create($transaction_data);
                return redirect()->away($response['pay_url']);
            }
        }

        return response()->json(['error' => 'Malumotlar mos kelmadi'], 400);
    }

    public function chekkinTransaction($tr_id){
        $transaction_id = $tr_id;
        $admin = Payment::where('transaction_id','=',$transaction_id)->get()->first();
        $data = [
            "merchant_id"=> $this->merchantId, 
            "transaction_id"=> $transaction_id
        ];
        
        $response = $this->curlPost("https://api.oson.uz/api/invoice/status", $data);
        $transaction_data = [     //CoursPrice
            'status'=>$this->all_status[$response['status']],                //Status
        ];
        
        $admin->update($transaction_data);
        return view('welcome');
    }

    private function curlPost($url, $data){
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'token' => $this->token
        ])->post($url, $data);

        if ($response->failed()) {
            return ['error' => 'Request failed'];
        }

        return $response->json();
    }
}
