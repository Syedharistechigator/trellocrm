<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CustomerSheet\CustomerSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RequestTradeMarkController extends Controller
{
    public function index()
    {
        return view('request-trademark');
    }

    public function submit_form(Request $request)
    {
        try {


            $rules = [
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'order_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                    'after:' . Date::now()->subYears(10)->format('Y-m-d'),
                    'before_or_equal:' . Date::now()->addYears(10)->format('Y-m-d'),
                ],
                'order_type' => 'required|in:1,2,3',
                'filling' => 'required|in:1,2,3',
                'amount_charged' => 'required|string',
            ];
            $messages = [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name field must be a string.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid customer email address.',
                'phone.required' => 'The phone number field is required.',
                'phone.string' => 'The phone field must be a string.',
                'order_date.date' => 'Please enter a valid date for the order.',
                'order_date.after' => 'The order date must be after :date.',
                'order_date.before_or_equal' => 'The order date must be before or equal to :date.',
                'order_type.required' => 'The order type is required.',
                'order_type.in' => 'The order type must be one of the following: copyright, trademark, attestation.',
                'filling.required' => 'The filling type is required.',
                'filling.in' => 'The filling type must be one of the following: logo, slogan, business-name.',
                'amount_charged.required' => 'The amount charged is required.',
                'amount_charged.string' => 'The amount charged must be a string.',
            ];


            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $client = Client::where('email', $request->get('email'))->first();
            if (!$client) {
                $client = new Client();
                $client->team_key = 0000;
                $client->brand_key = 0000;
                $client->creatorid = 0;
                $client->name = $request->get('name');
                $client->email = $request->get('email');
                $client->phone = $request->get('phone');
                $client->agent_id = null;
                $client->client_created_from_leadid = 0;
                $client->client_description = "";
                $client->address = "";
                $client->status = '1';
                $client->save();
                $user = new User();
                $user->name = $client->name;
                $user->email = $client->email;
                $user->phone = $client->phone;
                $user->password = Hash::make('12345678');
                $user->type = 'tm-client';
                $user->clientid = $client->id;
                $user->save();
            }
            $customer_sheet = new CustomerSheet();
            $customer_sheet->customer_id = rand(1111, 9999) . substr(time(), 7, 3);
            $customer_sheet->customer_name = $request->input('name');
            $customer_sheet->customer_email = $request->input('email');
            $customer_sheet->customer_phone = $request->input('phone');
            $customer_sheet->order_date = $request->input('order_date');
            $customer_sheet->order_type = $request->input('order_type');
            $customer_sheet->filling = $request->input('filling');
            $customer_sheet->amount_charged = $request->input('amount_charged');
            $customer_sheet->creator_id = $client->id;
            $customer_sheet->creator_type = get_class($client);
            $customer_sheet->save();

            $customer_sheet->creator_name = $client->name;

            if ($request->hasFile('attachments')) {
                $this->process_attachments($client,$customer_sheet, $request);
            }
            return redirect()->route('request.trademark')->with('success', 'Form submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again.')->withErrors($validator)->withInput($request->all());
        }
    }

    private function process_attachments($client,$customer_sheet, $request)
    {
        foreach ($request->file('attachments') as $attachment) {
            if (!$attachment->isValid()) {
                throw new \RuntimeException('Invalid attachment file.');
            }
            $file_directory = str_contains($attachment->getMimeType(), 'image') ? 'images' : 'files';
            $file_directory_path = public_path("assets/{$file_directory}/customer-sheet/{$attachment->getMimeType()}/");
            $file_name = time() . '-' . $client->id . random_int(11, 20) . '.' . $attachment->getClientOriginalExtension();
            $customer_sheet->attachments()->create([
                'creator_id' => $client->id,
                'creator_type' => get_class($client),
                'customer_sheet_id' => $customer_sheet->id,
                'original_name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getMimeType(),
                'file_size' => $this->convert_filesize($attachment->getSize()),
                'extension' => $attachment->getClientOriginalExtension(),
                'file_name' => $file_name,
                'file_path' => $file_directory_path . $file_name,
            ]);
            /** Remember not to be placed before extracting values*/
            $attachment->move($file_directory_path, $file_name);
        }
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
