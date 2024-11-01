<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use App\Models\Client;
use App\Models\Add_phones;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;
class generalController extends Controller
{
public function cronjob_call_logs(){
    $assigneeIds = $call_logs = $response_data = $call_logs_data = $client_call_log_list = [];
    $client1 = new GuzzleClient();
    $client2 = new GuzzleClient();            
    $headers = [
        'headers' => [
            'Authorization' => 'Bearer eyJzdiI6IjAwMDAwMSIsImFsZyI6IkhTNTEyIiwidiI6IjIuMCIsImtpZCI6IjgyYzVlZDUyLWViNjUtNDFmNC1iNTUxLTkwMzNjMjZiNDk1OSJ9.eyJ2ZXIiOjksImF1aWQiOiJiNzMxOGMwZjM1ZDEzYzY2NTAwODcyYWI4ODNlNDg0ZCIsImNvZGUiOiJxMnpXc0s1a2ttbHdVa3B5UFNpUy02UmZtcFRCdktRb1EiLCJpc3MiOiJ6bTpjaWQ6SEM0TEJFS0VTRzJkY0ZTb1lBZmxLQSIsImdubyI6MCwidHlwZSI6MCwidGlkIjozNCwiYXVkIjoiaHR0cHM6Ly9vYXV0aC56b29tLnVzIiwidWlkIjoidzlWWm1Sb1VTTm04OUN6NFY2dTM4ZyIsIm5iZiI6MTcwNjE0NDM4NSwiZXhwIjoxNzA2MTQ3OTg1LCJpYXQiOjE3MDYxNDQzODUsImFpZCI6IkxhRWFMM2NCUTNha0hObzJrY1Fta2cifQ.HTZoRa-sbUdSpCT-LDmlIZiQtHik48BlxKu0pu6xmrO__779Vr5Qs8GsP5BIYeri4pp6RXOh2RIIfUQUkmPdrQ', // Replace with your actual access token
        ],
    ];
    $url = 'https://api.zoom.us/v2/phone/numbers';
    $response = $client1->request('GET', $url, $headers);
    $statusCode = $response->getStatusCode();
    $jsonData = $response->getBody()->getContents();
    $data = json_decode($jsonData, true);

foreach ($data['phone_numbers'] as $phoneNumber) {
        if (isset($phoneNumber['assignee']['id'])) {
            $assigneeIds[] = $phoneNumber['assignee']['id'];
        }
    }

    $today = date("Y-m-d");
    $fromday = date("Y-m-d", strtotime("-1 days"));

foreach ($assigneeIds as $key2 => $assigneeId) {
            $url2 = 'https://api.zoom.us/v2/phone/users/' . $assigneeId . '/call_logs?from=' . $fromday . '&to=' . $today;
            try {
                $response2 = $client2->request('GET', $url2, $headers);
                $statusCode2 = $response2->getStatusCode();
                $jsonData2 = $response2->getBody()->getContents();
                $response_data = json_decode($jsonData2, true);

                if (array_key_exists('call_logs', $response_data)) {
                    $call_logs[] = $response_data;
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $statusCode2 = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                continue;
            }
        }

foreach ($call_logs as $call_log_data) {
        if (isset($call_log_data['call_logs'])) {
            $call_logs_data = array_merge($call_logs_data, $call_log_data['call_logs']);
        }
    }
            $client_call_logs = array_values($call_logs_data);
foreach ($client_call_logs as $key_client_cal => $client_call_log) {
            $clients = Client::where('status', 1)->where('phone', $client_call_log['caller_number'])->orwhere('phone',$client_call_log['callee_number'])->first();
            $clientadd_phones = DB::table('add_phones')->where('phone', $client_call_log['caller_number'])->orwhere('phone',$client_call_log['callee_number'])->first();
if($clients){
            $client_call_log_list[$key_client_cal]['client_id']=isset($clients->id) ? $clients->id : 0;
            $client_call_log_list[$key_client_cal]['caller_name']=isset($client_call_log['caller_name']) ? $client_call_log['caller_name'] : 'N/A';
            $client_call_log_list[$key_client_cal]['caller_number']=isset($client_call_log['caller_number']) ? $client_call_log['caller_number'] : 'N/A';
            $client_call_log_list[$key_client_cal]['callee_name']=isset($client_call_log['callee_name']) ? $client_call_log['callee_name'] : 'N/A'; 
            $client_call_log_list[$key_client_cal]['callee_number']=isset($client_call_log['callee_number']) ? $client_call_log['callee_number'] : 'N/A';
            $client_call_log_list[$key_client_cal]['date_time']=isset($client_call_log['date_time']) ? $client_call_log['date_time'] : 'N/A';  
            $client_call_log_list[$key_client_cal]['direction']=isset($client_call_log['direction']) ? $client_call_log['direction'] : 'N/A';  
            $client_call_log_list[$key_client_cal]['duration']=isset($client_call_log['duration']) ? $client_call_log['duration'] : 'N/A';  
            }elseif($clientadd_phones){
            $client_call_log_list[$key_client_cal]['client_id']=isset($clientadd_phones->client_id) ? $clientadd_phones->client_id : 0;
            $client_call_log_list[$key_client_cal]['caller_name']=isset($client_call_log['caller_name']) ? $client_call_log['caller_name'] : 'N/A';
            $client_call_log_list[$key_client_cal]['caller_number']=isset($client_call_log['caller_number']) ? $client_call_log['caller_number'] : 'N/A';
            $client_call_log_list[$key_client_cal]['callee_name']=isset($client_call_log['callee_name']) ? $client_call_log['callee_name'] : 'N/A'; 
            $client_call_log_list[$key_client_cal]['callee_number']=isset($client_call_log['callee_number']) ? $client_call_log['callee_number'] : 'N/A';
            $client_call_log_list[$key_client_cal]['date_time']=isset($client_call_log['date_time']) ? $client_call_log['date_time'] : 'N/A';  
            $client_call_log_list[$key_client_cal]['direction']=isset($client_call_log['direction']) ? $client_call_log['direction'] : 'N/A';  
            $client_call_log_list[$key_client_cal]['duration']=isset($client_call_log['duration']) ? $client_call_log['duration'] : 'N/A'; 
}else{

      }
}
foreach ($client_call_log_list as $callLog) {
    DB::table('call_logs')->insert([
        'client_id' => $callLog['client_id'],
        // 'caller_name' => $callLog['caller_name'],
        // 'caller_number' => $callLog['caller_number'],
        // 'callee_name' => $callLog['callee_name'],
        // 'callee_number' => $callLog['callee_number'],
        // 'date_time' => $callLog['date_time'],
        // 'direction' => $callLog['direction'],
        // 'duration' => $callLog['duration'],
        'response' => serialize($callLog),
    ]);
}
    return 'call_logs_insert_in_db';
}
    public function clearCache(){
//        Artisan::call('optimize:clear');
//        Artisan::call('cache:clear');
//        Artisan::call('route:cache');
//        Artisan::call('view:clear');

        // return redirect('/admin/dashboard');

        return 'cxmCacheClear';

    }
}
