<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\CustomerSheet\{CustomerSheet, CustomerSheetAttachment};
use App\Models\LogAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Date, Validator};
use Illuminate\Support\Carbon;

class CustomerSheetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $customer_sheets = CustomerSheet::all();
        return view('admin.customer-sheet.index', compact('customer_sheets'));
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
            $customer_sheet = new CustomerSheet();
            $customer_sheet->customer_id = rand(1111, 9999) . substr(time(), 7, 3);
            $this->extracted($request, $customer_sheet);
            $customer_sheet->creator_id = Auth::id();
            $customer_sheet->creator_type = get_class(Auth::user() ?? "");
            $customer_sheet->save();

            $customer_sheet->creator_name = Auth::user()->name;
            $customer_sheet->order_date = Carbon::parse($customer_sheet->order_date)->format('j F, Y');

            if ($request->hasFile('attachments')) {
                $this->process_attachments($customer_sheet, $request);
            }
            $customer_sheet->load('attachments'); /** Need to load after adding attachments*/
            if ($customer_sheet) {
                return response()->json(['status' => 1, 'success' => 'Record created successfully.!', 'data' => $customer_sheet,'attachments'=> $customer_sheet->attachments]);
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
            $customer_sheet->attachments()->create([
                'creator_id' => Auth::id(),
                'creator_type' => get_class(Auth::user() ?? ""),
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CustomerSheet\CustomerSheet $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $customer_sheet = CustomerSheet::find($id);
        if (!$customer_sheet) {
            return response()->json(['error' => 'Record not found.!']);
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
            $customer_sheet = CustomerSheet::find($id);

            if (!$customer_sheet) {
                return response()->json(['error' => 'Customer Sheet not found'], 404);
            }
            $this->extracted($request, $customer_sheet);
            $customer_sheet->save();
            $customer_sheet->creator_name = isset($customer_sheet->creator->name) ? $customer_sheet->creator->name : "" ;
            $customer_sheet->order_date = Carbon::parse($customer_sheet->order_date)->format('j F, Y');

            $customer_sheet->load('attachments');
            if ($customer_sheet) {
                return response()->json(['status' => 1, 'success' => 'Record updated successfully.!', 'data' => $customer_sheet,'attachments'=> $customer_sheet->attachments]);
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
        $customer_sheet->customer_name = $request->input('customer_name');
        $customer_sheet->customer_email = $request->input('customer_email');
        $customer_sheet->customer_phone = $request->input('customer_phone');
        $customer_sheet->order_date = $request->input('order_date');
        $customer_sheet->order_type = $request->input('order_type');
        $customer_sheet->filling = $request->input('filling');
        $customer_sheet->amount_charged = $request->input('amount_charged');
        $customer_sheet->order_status = $request->input('order_status');
        $customer_sheet->communication = $request->input('communication');
        $customer_sheet->project_assigned = $request->input('project_assigned');
    }

    public function add_attachment(Request $request, $id)
    {
        try {
            $rules = [
                'attachments' => 'required|array',
                'attachments.*' => 'required|file|mimes:jpeg,png,gif,pdf,doc,docx|max:2048',
            ];
            $messages = [
                'attachments.required' => 'Please provide attachments.',
                'attachments.*.required' => 'Please provide all attachments.',
                'attachments.*.file' => 'Invalid attachment provided.',
                'attachments.*.mimes' => 'Attachments must be of type: jpeg, png, gif, pdf, doc, docx.',
                'attachments.*.max' => 'Attachments must not exceed 2MB in size.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $customer_sheet = CustomerSheet::find($id);

            if (!$customer_sheet) {
                return response()->json(['error' => 'Customer Sheet not found'], 404);
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
    public function view_attachment(Request $request, CustomerSheet $id)
    {
        try {
            if ($id->attachments()->count() > 0) {
                $attachments = $id->attachments()->orderBy('id', 'asc')->get();
                return response()->json(['status' => 1, 'success' => 'Attachments fetched successfully', 'data' => $attachments, 'count' => $id->attachments()->count()]);
            }
            return response()->json(['status' => 1, 'success' => 'No attachments found for this customer sheet', 'count' => 0], 200);
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
            $customer_sheet_attachment = CustomerSheetAttachment::find($id);
            if (!$customer_sheet_attachment) {
                return response()->json(['error' => 'Record not found'], 404);
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
            $customer_sheet = CustomerSheet::find($id);
            if (!$customer_sheet) {
                return response()->json(['error' => 'Record not found'], 404);
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

    public function trashed()
    {
        $customer_sheets = CustomerSheet::onlyTrashed()->get();
        return view('admin.customer-sheet.trashed', compact('customer_sheets'));
    }


    public function restore($id)
    {
        try {
            CustomerSheet::onlyTrashed()->whereId($id)->restore();
            return response()->json(['success' => 'Record restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function restore_all()
    {
        try {
            CustomerSheet::onlyTrashed()->restore();
            return response()->json(['success' => 'All trashed records restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function force_delete($id)
    {
        try {
            CustomerSheet::onlyTrashed()->whereId($id)->forceDelete();
            return response()->json(['success' => 'Record permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function log(Request $request)
    {
        try {
            $logs = LogAction::where('loggable_type', CustomerSheet::class)
//                ->whereHasMorph('loggable', [CustomerSheet::class], function (Builder $query) {
//                    $query->whereNull('deleted_at');
//                })
                ->get();
            return view('admin.customer-sheet.log', compact('logs'));
        } catch (\Exception $e) {
            return back()->with("error", $e->getMessage());
        }
    }

}
