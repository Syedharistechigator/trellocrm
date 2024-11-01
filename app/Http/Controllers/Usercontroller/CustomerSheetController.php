<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerSheet\{CustomerSheet, CustomerSheetAttachment};
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CustomerSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!in_array(Auth::user()->type, ['tm-user', 'tm-client'])) {
            return back()->with(['error' => "Oops! You don't have enough privileges"]);
        }

        $customer_sheet = CustomerSheet::query();
        if (Auth::user()->type == 'tm-user') {
            $customer_sheet->authOrClientRecords();
        } elseif (Auth::user()->type == 'tm-client') {
            $customer_sheet->where('creator_id', Auth::user()->clientid)->where('creator_type', 'App\Models\Client');
        }
        $customer_sheets = $customer_sheet->get();

        return view('customer-sheet.index', compact('customer_sheets'));
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            if (!in_array(Auth::user()->type, ['tm-user', 'tm-client'])) {
                return response()->json(['errors' => "Oops! You don't have enough privileges"], 400);
            }

            [$prefix, $labelPrefix] = (Auth::user()->type === 'tm-client') ? ["", ""] : ["customer_", "Customer "];

            $rules = [
                $prefix . 'name' => 'required|string',
                $prefix . 'email' => 'required|email',
                $prefix . 'phone' => 'nullable|string',
                'order_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                    'after:' . Date::now()->subYears(10)->format('Y-m-d'),
                    'before_or_equal:' . Date::now()->addYears(10)->format('Y-m-d'),
                ],
                'order_type' => 'nullable|in:1,2,3',
                'filling' => 'nullable|in:1,2,3',
                'amount_charged' => 'nullable|string',
            ];
            $messages = [
                $prefix . 'name.required' => "The {$labelPrefix}name field is required.",
                $prefix . 'name.string' => "The {$labelPrefix}name must be a string.",
                $prefix . 'email.required' => "The {$labelPrefix}email field is required.",
                $prefix . 'email.email' => "Please enter a valid {$labelPrefix}email address.",
                $prefix . 'phone.string' => "The {$labelPrefix}phone must be a string.",
                'order_date.date' => 'Please enter a valid date for the order.',
                'order_date.after' => 'The order date must be after :date.',
                'order_date.before_or_equal' => 'The order date must be before or equal to :date.',
                'order_type.in' => 'The order type must be one of the following: copyright, trademark, attestation',
                'filling.in' => 'The filling type must be one of the following: logo, slogan, business-name',
                'amount_charged.string' => 'The amount charged must be a string.',
            ];


            if (Auth::user()->type === 'tm-user') {
                $rules += [
                    'order_status' => 'nullable|in:1,2,3,4,5,6,7',
                    'communication' => 'nullable|in:1,2,3,4,5,6,7',
                    'project_assigned' => 'nullable|string',
                ];

                $messages += [
                    'order_status.in' => 'The order status must be one of the following: requested, applied, received, rejected, objection, approved, delivered',
                    'communication.in' => 'The communication status must be one of the following: out-of-reached, skeptic, satisfied, refunded, refund requested, do-not-call, not-interested',
                    'project_assigned.string' => 'The project assigned must be a string.',
                ];
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $customer_sheet = new CustomerSheet();
            $customer_sheet->customer_id = rand(1111, 9999) . substr(time(), 7, 3);
            $this->extracted($request, $customer_sheet);
            $creator_id = Auth::id();
            $creator_type = get_class(Auth::user() ?? "");
            if (Auth::user()->type === 'tm-client' && Auth::user()->clientid) {
                $creator_id = Auth::user()->clientid;
                $creator_type = 'App\Models\Client';
            }
            $customer_sheet->creator_id = $creator_id;
            $customer_sheet->creator_type = $creator_type;
            $customer_sheet->save();
            if ($request->hasFile('attachments')) {
                $this->process_attachments($customer_sheet, $request);
            }
            $customer_sheet->load('attachments');
            /** Need to load after adding attachments*/
            if ($customer_sheet) {
                return response()->json(['status' => 1, 'success' => 'created successfully', 'data' => $customer_sheet, 'attachments' => $customer_sheet->attachments]);
            }
            throw new \Exception('Failed to create record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function process_attachments($customer_sheet, $request)
    {
        foreach ($request->file('attachments') as $attachment) {
            if (!$attachment->isValid()) {
                throw new \RuntimeException('Invalid attachment file.');
            }
            $file_directory = str_contains($attachment->getMimeType(), 'image') ? 'images' : 'files';
            $file_directory_path = public_path("assets/{$file_directory}/customer-sheet/{$attachment->getMimeType()}/");
            $file_name = time() . '-' . auth()->user()->id . random_int(11, 20) . '.' . $attachment->getClientOriginalExtension();
            $creator_id = Auth::id();
            $creator_type = get_class(Auth::user() ?? "");
            if (Auth::user()->type === 'tm-client' && Auth::user()->clientid) {
                $creator_id = Auth::user()->clientid;
                $creator_type = 'App\Models\Client';
            }

            $original_name = $attachment->getClientOriginalName();
            $mime_type = $attachment->getMimeType();
            $file_size = $this->convert_filesize($attachment->getSize());
            $extension = $attachment->getClientOriginalExtension();
            /** Remember not to be placed before extracting values*/
            $attachment->move($file_directory_path, $file_name);

            $file_contents = file_get_contents($file_directory_path . $file_name);
            $base64EncodedData = "data:{$mime_type};base64," . base64_encode($file_contents);

            $customer_sheet->attachments()->create([
                'creator_id' => $creator_id,
                'creator_type' => $creator_type,
                'customer_sheet_id' => $customer_sheet->id,
                'original_name' => $original_name,
                'mime_type' => $mime_type,
                'file_size' => $file_size,
                'extension' => $extension,
                'file_name' => $file_name,
                'file_path' => $file_directory_path . $file_name,
                'base_encode' => $base64EncodedData,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CustomerSheet\CustomerSheet $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        if (Auth::user()->type !== 'tm-user') {
            return response()->json(['errors' => "Oops! You don't have enough privileges"], 400);
        }
        $customer_sheet = CustomerSheet::where('id', $id)->authOrClientRecords()->first();

        if (!$customer_sheet) {
            return response()->json(['error' => 'Customer Sheet not found or you are not allowed to access it.'], 404);
        }
        return response()->json(['status' => 1, 'success' => 'Fetched successfully', 'data' => $customer_sheet]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CustomerSheet\CustomerSheet $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            if (Auth::user()->type !== 'tm-user') {
                return response()->json(['errors' => "Oops! You don't have enough privileges"], 400);
            }
            $rules = [
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
                'customer_phone' => 'nullable|string',
                'order_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                    'after:' . Date::now()->subYears(10)->format('Y-m-d'),
                    'before_or_equal:' . Date::now()->addYears(10)->format('Y-m-d'),
                ],
                'order_type' => 'nullable|in:1,2,3',
                'filling' => 'nullable|in:1,2,3',
                'amount_charged' => 'nullable|string',
                'order_status' => 'nullable|in:1,2,3,4,5,6,7',
                'communication' => 'nullable|in:1,2,3,4,5,6,7',
                'project_assigned' => 'nullable|string',
            ];
            $messages = [
                'customer_name.required' => 'The customer name is required.',
                'customer_name.string' => 'The customer name must be a string.',
                'customer_email.required' => 'The customer email field is required.',
                'customer_email.email' => 'Please enter a valid customer email address.',
                'customer_phone.string' => 'The customer phone must be a string.',
                'order_date.date' => 'Please enter a valid date for the order.',
                'order_date.after' => 'The order date must be after :date.',
                'order_date.before_or_equal' => 'The order date must be before or equal to :date.',
                'order_type.in' => 'The order type must be one of the following: copyright , trademark , attestation',
                'filling.in' => 'The filling type must be one of the following: logo , slogan , business-name',
                'amount_charged.string' => 'The amount charged must be a string.',
                'order_status.in' => 'The order status must be one of the following: requested , applied , received , rejected , objection , approved , delivered',
                'communication.in' => 'The communication status must be one of the following: out-of-reached , skeptic , satisfied , refunded , refund requested , do-not-call , not-interested',
                'project_assigned.string' => 'The project assigned must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $customer_sheet = CustomerSheet::where('id', $id)->authOrClientRecords()->first();
            if (!$customer_sheet) {
                return response()->json(['error' => 'Customer Sheet not found or you are not allowed to access it.'], 404);
            }
            $this->extracted($request, $customer_sheet);
            $customer_sheet->save();
            if ($customer_sheet) {
                return response()->json(['status' => 1, 'success' => 'updated successfully', 'data' => $customer_sheet, 'attachments' => $customer_sheet->attachments]);
            }
            throw new \Exception('Failed to update record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $customer_sheet
     * @return void
     */
    public function extracted(Request $request, $customer_sheet): void
    {
        $prefix = (Auth::user()->type === 'tm-client') ? "" : "customer_";
        $customer_sheet->customer_name = $request->input($prefix . 'name');
        $customer_sheet->customer_email = $request->input($prefix . 'email');
        $customer_sheet->customer_phone = $request->input($prefix . 'phone');
        $customer_sheet->order_date = $request->input('order_date');
        $customer_sheet->order_type = $request->input('order_type');
        $customer_sheet->filling = $request->input('filling');
        $customer_sheet->amount_charged = $request->input('amount_charged');

        if (Auth::user()->type === 'tm-user') {
            $customer_sheet->order_status = $request->input('order_status');
            $customer_sheet->communication = $request->input('communication');
            $customer_sheet->project_assigned = $request->input('project_assigned');
        }
    }


    public function add_attachment(Request $request, $id)
    {
        try {
            if (Auth::user()->type !== 'tm-user') {
                return response()->json(['errors' => "Oops! You don't have enough privileges"], 400);
            }
            $rules = [
                'attachments' => 'required|array',
                'attachments.*' => 'required|file|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:2048',
            ];
            $messages = [
                'attachments.required' => 'Please provide attachments.',
                'attachments.*.required' => 'Please provide all attachments.',
                'attachments.*.file' => 'Invalid attachment provided.',
                'attachments.*.mimes' => 'Attachments must be of type: jpeg, png, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt',
                'attachments.*.max' => 'Attachments must not exceed 2MB in size.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $customer_sheet = CustomerSheet::where('id', $id)->authOrClientRecords()->first();

            if (!$customer_sheet) {
                return response()->json(['error' => 'Customer Sheet not found or you are not allowed to access it.'], 404);
            }
            if ($request->hasFile('attachments')) {
                $this->process_attachments($customer_sheet, $request);
                if ($customer_sheet) {
                    return response()->json(['status' => 1, 'success' => 'attachment added successfully', 'data' => $customer_sheet]);
                }
            }
            throw new \Exception('Failed to create record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function view_attachment(Request $request, $id)
    {
        try {

            if (!in_array(Auth::user()->type, ['tm-user', 'tm-client'])) {
                return response()->json(['errors' => "Oops! You don't have enough privileges"], 400);
            }

            $customer_sheet = CustomerSheet::where('id', $id);
            if (Auth::user()->type === 'tm-user') {
                $customer_sheet->authOrClientRecords();
            } elseif (Auth::user()->type === 'tm-client') {
                $customer_sheet->clientRecords();
            }
            $customer_sheet = $customer_sheet->first();

            if (!$customer_sheet) {
                return response()->json(['error' => 'Record not found or you are not allowed to access it.'], 404);
            }
            if ($customer_sheet->attachments()->count() > 0) {
                $attachments = $customer_sheet->attachments()->orderBy('id', 'asc')->get();
                return response()->json(['status' => 1, 'success' => 'Attachments fetched successfully', 'data' => $attachments, 'count' => $customer_sheet->attachments()->count()]);
            }
            return response()->json(['status' => 1, 'success' => 'No attachments found for this record', 'count' => 0], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CustomerSheet\CustomerSheetAttachment $customer_sheet_attachment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_attachment($id)
    {
        try {
            $customer_sheet_attachment = CustomerSheetAttachment::where('id', $id)->authOrClientRecords()->first();
            if (!$customer_sheet_attachment) {
                return response()->json(['error' => 'Attachment not found or you are not allowed to access it.'], 404);
            }
            $customer_sheet_attachment->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CustomerSheet\CustomerSheet $customer_sheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $customer_sheet = CustomerSheet::where('id', $id)->authOrClientRecords()->first();
            if (!$customer_sheet) {
                return response()->json(['error' => 'Record not found or you are not allowed to access it.'], 404);
            }
            $customer_sheet->delete();
            foreach ($customer_sheet->attachments()->get() as $attachment) {
                $attachment->delete();
            }
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
