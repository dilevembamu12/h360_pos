<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\Charts\CommonChart;
use App\System;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Routing\Controller;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


use App\Utils\BusinessUtil;

class H360GptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {






        if (request()->ajax()) {



            $headers = [
                'Api-key' => env('SMS_ADMIN_API_KEY'),
                'Content-Type' => 'application/json',
            ];
            $postdata = [];
            $client = new \GuzzleHttp\Client([
                \GuzzleHttp\RequestOptions::VERIFY => false
            ]);
            $_response = $client->get(env('SMS_ADMIN_API_URL') . '/users', [
                'headers' => $headers,
                'body' => json_encode($postdata)
            ]);
            $_sms_users = json_decode($_response->getBody()->getContents());
            $sms_users = collect($_sms_users); //convertir en collection afin de faire les recherche dedans
            //dd($sms_users);
            //dd($sms_users->firstWhere('name', 'USER1')->credit);



            //on retoruve tous les business
            $businesses = Business::where('business.is_active', 1)->get();

            //dd($businesses[0]->sms_settings['nexmo_from']);
            //$businesses->where('business.is_active', 1);


            return Datatables::of($businesses)

                /*
                { data: 'credit', name: 'credit', searchable: false},
                { data: 'email_credit', name: 'email_credit' },
                { data: 'whatsapp_credit', name: 'whatsapp_credit' },
				{ data: 'api_key', name: 'api_key', searchable: false },
                { data: 'sms_gateway', name: 'sms_gateway' },
                { data: 'gateway_credentials', name: 'gateway_credentials' },
                */
                ->addColumn('credit', function ($row) use ($client, $headers, $sms_users) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id))) {
                        $sender_id=!empty($row->sms_settings['nexmo_from']) ? $row->sms_settings['nexmo_from'] : "H360 ETS";
                        $postdata = ["sender_id" => $sender_id, "business_id" => $row->id];
                        $_response = $client->put(env('SMS_ADMIN_API_URL') . '/storeh360user', [
                            'headers' => $headers,
                            'body' => json_encode($postdata)
                        ]);
                        $response = json_decode($_response->getBody()->getContents());
                        //dd($response );
                        //dd($response->data->sms_gateways->sender_id);
                        if (empty($response->status)) {
                            return $response->data->credit;
                        }
                        return 0;
                    } else {
                        return $sms_users->firstWhere('name', 'USER_H360_' . $row->id)->credit;
                    }
                    return 52;
                })
                ->addColumn('email_credit', function ($row) use ($sms_users) {
                    return !empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id)) ?
                        $sms_users->firstWhere('name', 'USER_H360_' . $row->id)->email_credit : 0;
                })
                ->addColumn('whatsapp_credit', function ($row) use ($sms_users) {
                    return !empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id)) ?
                        $sms_users->firstWhere('name', 'USER_H360_' . $row->id)->whatsapp_credit : 0;
                })
                ->addColumn('api_key', function ($row) use ($client, $headers,$sms_users) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (!empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id))) {
                        $item=$sms_users->firstWhere('name', 'USER_H360_' . $row->id);
                        if (empty($item->api_key)) {
                            $postdata = ["api_key" => (string) Str::orderedUuid(), "user_id" => $item->id];
                            $_response = $client->put(env('SMS_ADMIN_API_URL') . '/savegeneratedkey', [
                                'headers' => $headers,
                                'body' => json_encode($postdata)
                            ]);
                            $response = json_decode($_response->getBody()->getContents());
                            if (empty($response->status)) {
                                return $response->data->api_key;
                            }
                            return $response->message;
                        } else {
                            return $item->api_key;
                        }
                    }
                    return 0;
                })
                ->addColumn('sms_gateway', function ($row) use ($client, $headers,$sms_users) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (!empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id))) {
                        $item=$sms_users->firstWhere('name', 'USER_H360_' . $row->id);
                        if (empty($item->sms_gateway)) {
                            return 0;
                        } else {
                            return $item->sms_gateway;
                        }
                    }
                    return 0;
                })
                ->addColumn('gateway_credentials', function ($row) use ($client, $headers,$sms_users) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (!empty($sms_users->firstWhere('name', 'USER_H360_' . $row->id))) {
                        $item=$sms_users->firstWhere('name', 'USER_H360_' . $row->id);
                        if (empty($item->gateway_credentials)) {
                            $postdata = ["sender_id" => "h360 BABA", "user_id" => $item->id];
                            $_response = $client->put(env('SMS_ADMIN_API_URL') . '/savegsatewaycredential', [
                                'headers' => $headers,
                                'body' => json_encode($postdata)
                            ]);
                            $response = json_decode($_response->getBody()->getContents());
                            //dd($response->data->sms_gateways->sender_id);
                            if (empty($response->status)) {
                                return $response->data->sms_gateways->sender_id;
                            }
                            return $response->message;
                        } else {
                            return $item->gateway_credentials->sms_gateways->sender_id;
                        }
                    }
                    return 0;
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal" data-href="' . action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'show'], $row->id) . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</button>';
                })
                ->make(true);


            return Datatables::of($sms_users)
                ->editColumn('api_key', function ($row) use ($client, $headers) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (empty($row->api_key)) {
                        $postdata = ["api_key" => (string) Str::orderedUuid(), "user_id" => $row->id];
                        $_response = $client->put(env('SMS_ADMIN_API_URL') . '/savegeneratedkey', [
                            'headers' => $headers,
                            'body' => json_encode($postdata)
                        ]);
                        $response = json_decode($_response->getBody()->getContents());
                        if (empty($response->status)) {
                            return $response->data->api_key;
                        }
                        return $response->message;
                    } else {
                        return $row->api_key;
                    }
                })
                ->editColumn('gateway_credentials', function ($row) use ($client, $headers) {
                    //on verifie si il n'a jamais eu de clé alors on le genere 
                    if (empty($row->gateway_credentials)) {
                        $postdata = ["sender_id" => "h360 BABA", "user_id" => $row->id];
                        $_response = $client->put(env('SMS_ADMIN_API_URL') . '/savegsatewaycredential', [
                            'headers' => $headers,
                            'body' => json_encode($postdata)
                        ]);
                        $response = json_decode($_response->getBody()->getContents());
                        //dd($response->data->sms_gateways->sender_id);
                        if (empty($response->status)) {
                            return $response->data->sms_gateways->sender_id;
                        }
                        return $response->message;
                    } else {
                        return $row->gateway_credentials->sms_gateways->sender_id;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal" data-href="' . action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'show'], $row->id) . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</button>';
                })
                ->make(true);
        }

        return view('superadmin::sms.index');
    }

    /**
     * Returns the monthly sell data for chart
     *
     * @return array
     */
    protected function _monthly_sell_data()
    {
        $start = Carbon::today()->subYear();
        $end = Carbon::today();
        $subscriptions = Subscription::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start, $end])
            ->select('package_price', 'created_at')
            ->orderBy('created_at')
            ->get();
        $subscription_formatted = [];
        foreach ($subscriptions as $value) {
            $month_year = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('M-Y');
            if (!isset($subscription_formatted[$month_year])) {
                $subscription_formatted[$month_year] = 0;
            }
            $subscription_formatted[$month_year] += (float) $value->package_price;
        }

        return $subscription_formatted;
    }

    /**
     * Returns the stats for superadmin
     *
     * @param $start date
     * @param $end date
     * @return json
     */
    public function stats(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $start_date = $request->get('start');
        $end_date = $request->get('end');

        $subscription = Subscription::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start_date, $end_date])
            ->where('status', 'approved')
            ->select(DB::raw('SUM(package_price) as total'))
            ->first()
            ->total;

        $registrations = Business::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start_date, $end_date])
            ->select(DB::raw('COUNT(id) as total'))
            ->first()
            ->total;

        return [
            'new_subscriptions' => $subscription,
            'new_registrations' => $registrations,
        ];
    }
}
