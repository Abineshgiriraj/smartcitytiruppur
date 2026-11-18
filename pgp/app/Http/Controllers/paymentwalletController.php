<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

use App\Models\passentrysub;
use App\Models\passentrymainlist;
use App\Models\FirmsRegistration;
use App\Models\CategoryCreation;

use App\Models\posPayment;
use App\Models\AcessZone;

use App\Models\CardPassIssue;
use App\Models\CompanyCreation;
use App\Models\PersonType;
use App\Models\PortAreaAccess;
use App\Models\rate_master;
use App\Models\Vehiclecreation;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Http; // Make sure this line is included
use Razorpay\Api\Api;


class paymentwalletController extends Controller
{
    public function index(Request $request)
    {

        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        $sublist_data = '';
        // Fetch pass entries with applied filters
        // Initialize the query with the basic condition
        $query = PassEntryMainList::where('is_delete', 0);

        // Add filters based on the request parameters
        if ($request->pass_id) {
            $query->where('pass_id', $request->pass_id);
        }
        if ($request->firms_agent) {
            $query->where('firms_agent', 'LIKE', "%{$request->firms_agent}%");
        }
        if ($request->agent_type) {
            $query->where('agent_type', $request->agent_type);
        }
        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->format('Y-m-d');
            $query->where('entry_date', '>=', $fromDate);
        }
        if ($request->to_date) {
            $toDate = Carbon::parse($request->to_date)->format('Y-m-d');
            $query->where('entry_date', '<=', $toDate);
        }

        // Fetch the filtered pass entry list
        $pass_entry_list = $query->get();

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'data' => $pass_entry_list->map(function ($item) {
                    return [
                        'pass_id' => $item->pass_id, // Make sure these keys match your DataTable columns
                        'firms_agent' => $item->firms_agent,
                        'agent_type' => $item->agent_type,
                        'entry_date' => $item->entry_date,
                        // Add other columns as necessary
                    ];
                }),
            ]);
        }

        // Fetch related data for dropdowns
        $firm_registration1 = FirmsRegistration::where('is_delete', 0)
            ->pluck('name', 'unique_id');

        $company_name = CompanyCreation::where('is_delete', 0)
            ->pluck('company_name', 'unique_id');

        $personTypes = PersonType::where('is_delete', 0)
            ->pluck('person_type', 'unique_id');

        // Fetch sublist counts and data
        foreach ($pass_entry_list as $passentrymain_list) {
            $sublist_count = passentrysub::where('screen_unique_id', $passentrymain_list->screen_unique_id)
                ->where('is_delete', 0)
                ->count();
            $passentrymain_list->sublist_count = $sublist_count;

            $sublist_data = DB::table('pass_entry_sublist')->where('screen_unique_id', $passentrymain_list->screen_unique_id)
                ->where('is_delete', 0)
                ->select('*')
                ->get();
            $passentrymain_list->sublist_data = $sublist_data;
        }

        // Fetch sublist entries for the first matched pass entry
        $passentrymain_lists = $query->first(); // Use only first result for the next logic
        if ($passentrymain_lists) {
            $sublist_entries = passentrysub::where('agent_name', $passentrymain_lists->firms_agent)
                ->where('is_delete', 0)
                ->get();
        } else {
            $sublist_entries = collect(); // No entries found
        }

        return view('content.payment_wallet.list', compact('pass_entry_list', 'firm_registration1', 'company_name', 'personTypes', 'sublist_data', 'sublist_entries'));

    }
    public function add(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        $passId = $request->query('passId'); // Retrieve the passId from the query parameter
        return view('content.payment_wallet.payment', ['passId' => $passId]);
    }

    public function show(Request $request)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }        
        $mainData = passentrymainlist::where('screen_unique_id',$request->passid)->select('tax_amount','firms_agent','group_id')->first();
        $firmDetails = FirmsRegistration::where('unique_id',$mainData->firms_agent)->select('name','port_sap_no','address')->first();

        // dd($mainData);
        return ['permitAmt' => $mainData->tax_amount,'orderId' => $mainData->group_id, 'companyData' => $firmDetails];

    }


    public function edit($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }
        return view('content.EmployeeDesignation.edit', compact('category'));
    }

    public function destroy($id)
    {
        if(session('user_id') ==''){
            \Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        }

        $category = CategoryCreation::find($id);
        $category->is_delete = 1;
        $category->save();

        return redirect()->route('category_list')->with('Success', 'Deleted Successfully');
    }

    // Online Payment create

    public function payment(Request $request)
    {
        // if(session('user_id') ==''){
        //     \Auth::logout();
        //     session()->flush();
        //     return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        // }

        $passentrymainlists = passentrymainlist::where('screen_unique_id', $request->payscreenId)
        ->where('is_delete', 0)
        ->firstOrFail();

        $roundedAmount = round($passentrymainlists->tax_amount); // Result: 13
        $amount_in_paise = $roundedAmount * 100;
        $key_id = 'rzp_test_ToRBj8kTVZb5iC'; // Replace with your Razorpay Key ID
        $key_secret = 'r1luI8qPvX0YdLJH25RvuzDz'; // Replace with your Razorpay Key Secret
        $url = "https://api.razorpay.com/v1/orders";

        // Data to send
        $data = [
            'amount' => $amount_in_paise, // Amount in the smallest currency unit (e.g., 50000 paise = INR 500)
            'currency' => 'INR', // Currency code
            'receipt' => $passentrymainlists->group_id, // Receipt ID
            'payment_capture' => 1 // 1 for automatic capture, 0 for manual capture
        ];
        $response = Http::withBasicAuth($key_id, $key_secret)
                        ->post($url, $data);

        // Check response
        if ($response->successful()) {
            // Success response
            $orderData = $response->json();
            $passentrymainlists->update([
                'rr_id' => $orderData['id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successfully',
                'order' => $orderData
            ]);
        } else {
            // Error response
            return response()->json([
                'success' => false,
                'message' => $response->body()
            ], $response->status());
        }

    }

    // Online Payment Verify

    public function verifyPayment(Request $request)
    {

        // if(session('user_id') ==''){
        //     \Auth::logout();
        //     session()->flush();
        //     return redirect('/')->with('error', 'Session Expired!. Please Login again.');
        // }

        $api = new Api('rzp_test_ToRBj8kTVZb5iC', 'r1luI8qPvX0YdLJH25RvuzDz'); // Use your actual Razorpay test credentials

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        try {
            // Verify Razorpay payment signature
            $api->utility->verifyPaymentSignature($attributes);

            // dd($api);

            // Fetch the order using the Razorpay order ID
            $order = PassEntryMainList::where('rr_id', $request->razorpay_order_id)->firstOrFail();

            // Update order details with payment information
            $order->update([
                'payment_status' => 1, // Payment successful
                'payment_mode' => 'Online',
                'pay_currency' => 'INR',
                'payment_date' => now(),
                'razorpay_sign' => $request->razorpay_signature,
                'payment_id' => $request->razorpay_payment_id
            ]);
    // dd('success');
                // return response()->json([
                // 'success' => true,
                // 'message' => 'Payment verified successfully.']);

            return view('content.payment_wallet.paysuccess', ['success' => true,'message' => 'Payment verified successfully.']);
            // return redirect()->route('paymentSuccess')->with('message', 'Payment was successful!');

        
        } catch (\Exception $e) {
    // dd('failed');

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Payment verification failed.',
            //     'error' => $e->getMessage(), // Include error message for debugging
            // ]);
            return view('content.payment_wallet.paysuccess', ['success' => false,'message' => 'Payment verified Failed.']);
            // return redirect()->route('paymentFailure')->with('message', 'Payment verification failed.');

        }
    }

    // Online Payment Create

    public function paymentPOS(Request $request){
       $passentrymainlists = passentrymainlist::where('screen_unique_id', $request->payscreenId)
        ->where('is_delete', 0)
        ->firstOrFail();

        $appKey = DB::table('auth_key')->where('key_name','=','Pos')->first('api_key');
        $username = "9810000099";
        $deviceId = "1491829348|ezetap_android";
        $mode = 'ALL';

        $apiUrl = 'https://demo.ezetap.com/api/3.0/p2padapter/pay';

        $payload = [
            'appKey' => $appKey->api_key,
            'username' => $username,
            'amount' => $passentrymainlists->tax_amount,
            'externalRefNumber' => $request->referenceId,
            'pushTo' => [
                'deviceId' => $deviceId,
            ],
            'mode' => $mode,
        ];
        // Send POST request to the API
        try {
            $response = Http::post($apiUrl, $payload);


            // Check response status and return accordingly
            if ($response->successful()) {
                $orderData = $response->json();

                $passentrymainlists->update([
                    'rr_id' => $orderData['p2pRequestId'],
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment request processed successfully.',
                    'data' => $response->json(),
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process payment request.',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the payment request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Online Payment Verify

    public function verifyPOSPayment(Request $request){
        $appKey = DB::table('auth_key')->where('key_name','=','Pos')->first('api_key');
        $username = "9810000099";
        $returnId = $request->returnId;

        // dd($returnId);

        $apiUrl = 'https://demo.ezetap.com/api/3.0/p2padapter/status';

        $payload = [
            'username' => $username,
            'appKey' => $appKey->api_key,
            'origP2pRequestId' => $returnId,
        ];

        // Send the POST request to the Ezetap API
        try {
            $response = Http::post($apiUrl, $payload);

            if ($response->successful()) {
                // Return a success response
                
                $orderData = $response->json();
                
                // dd($orderData['abstractPaymentStatus']);

                if ($orderData['abstractPaymentStatus'] == "PROCESSING") {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment status retrieved successfully.',
                        'data' => $response->json(),
                    ]);
                }else{
                    $order = PassEntryMainList::where('rr_id', $returnId)->firstOrFail();
                    $order->update([
                        'payment_status' => 1, // Payment successful
                        'payment_mode' => 'POS',
                        'pay_currency' => 'INR',
                        'payment_date' => millsectoDatetime($orderData['postingDate']),
                        'razorpay_sign' => $orderData['signatureId'],
                        'payment_id' => $orderData['txnId'],
                        'orderNo' => $orderData['orderNumber']
                    ]);
    
                    posPayment::create([
                        'success' => $orderData['success'],
                        'realCode' => $orderData['realCode'],
                        'username' => $orderData['username'],
                        'amount' => $orderData['amount'],
                        'currencyCode' => 'INR',
                        'customerReceiptUrl' => $orderData['customerReceiptUrl'],
                        'deviceSerial' => $orderData['deviceSerial'],
                        'externalRefNumber' => $orderData['externalRefNumber'],
                        'txnId' => $orderData['txnId'],
                        'merchantName' => $orderData['merchantName'],
                        'paymentCardType' => $orderData['paymentCardType'],
                        'paymentMode' => $orderData['paymentMode'],
                        'postingDate' => millsectoDatetime($orderData['postingDate']),
                        'signatureId'=> $orderData['signatureId'],
                        'userMobile'=> $orderData['userMobile'],
                        'chargeSlipDate' => dateFormat($orderData['chargeSlipDate']),
                        'readableChargeSlipDate' => $orderData['readableChargeSlipDate'],
                        'receiptUrl' => $orderData['receiptUrl'],
                        'p2pRequestId' => $orderData['p2pRequestId'],
                        'orderNo' => $orderData['orderNumber']
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment status retrieved successfully.',
                        'data' => $response->json(),
                    ]);
                }
            } else {
                // Handle API error response
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to retrieve payment status.',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while connecting to the Ezetap API.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}