<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\EmailConfiguration;
use Hybridauth\Provider\Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class EmailConfigurationController extends Controller
{
    private $provider;

    private function google_authenticate(EmailConfiguration $email)
    {
        try {
            $state = [
                'dev' => 'dev michael',
                'id' => $email->id
            ];

            $config = [
                'callback' => route('handle.google.call.back'),
                'keys' => [
                    'id' => $email->client_id,
                    'secret' => $email->client_secret
                ],
                'scope' => 'https://mail.google.com',
                'authorize_url_parameters' => [
                    'approval_prompt' => 'force',
                    'access_type' => 'offline',
                    'state' => base64_encode(json_encode($state)),
                ]
            ];

            return new Google($config);
        } catch (\Exception $e) {
//            dd($e->getMessage(), 'google_authenticate');
            return back()->with(['error' => $e->getMessage()], 500);
        }
    }
    private function checkTokenExpiration(EmailConfiguration $email)
    {
        $this->provider = $this->google_authenticate($email);
        return $this->provider->hasAccessTokenExpired();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $emails = EmailConfiguration::all();
        foreach ($emails as $email){
            if ($email->parent_id != 0) {
                $parentEmail = EmailConfiguration::find($email->parent_id);
                $email['token_expire'] = $this->checkTokenExpiration($parentEmail);
            } else {
                $email['token_expire'] = $this->checkTokenExpiration($email);
            }
        }
        return view('admin.email-configuration.index', compact('emails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();
        $parent_ids = EmailConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.email-configuration.create', compact('brands', 'parent_ids'));
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
                'brand_key' => 'required',
                'email' => 'required|email',
                'client_id' => 'required_if:parent_id,0|nullable|string',
                'client_secret' => 'required_if:parent_id,0|nullable|string',
                'api_key' => 'nullable|string',
                'parent_id' => 'required',
                'provider' => 'required|integer',
                'status' => 'required|in:1,0',
            ];
            $messages = [
                'brand_key.required' => 'The brand key is required.',
                'brand_key.string' => 'The brand key must be a string.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'client_id.required_if' => 'The client ID is required when parent ID is 0.',
                'client_id.string' => 'The client ID must be a string.',
                'client_secret.required_if' => 'The client secret is required when parent ID is 0.',
                'client_secret.string' => 'The client secret must be a string.',
                'api_key.string' => 'The API key must be a string.',
                'parent_id.required' => 'The parent ID is required.',
                'parent_id.integer' => 'The parent ID must be an integer.',
                'provider.required' => 'The provider field is required.',
                'provider.integer' => 'The provider must be an integer.',
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either 1 or 0.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if ($request->parent_id > 0) {
                $parent_email = EmailConfiguration::where('id', $request->get('parent_id'))->first(['client_id', 'client_secret']);

                if (!$parent_email) {
                    return response()->json(['error' => 'Oops! Parent email not found.'], 404);
                }

                $client_id = $parent_email->client_id;
                $client_secret = $parent_email->client_secret;
            } else {
                $client_id = $request->client_id;
                $client_secret = $request->client_secret;
            }

            $email_configuration = EmailConfiguration::create([
                'created_by' => auth()->user()->id,
                'parent_id' => $request->parent_id,
                'brand_key' => $request->brand_key,
                'provider' => $request->provider,
                'email' => $request->email,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'api_key' => $request->api_key,
                'status' => $request->status,
            ]);
            if ($email_configuration) {
                return response()->json(['success' => 'created successfully', 'data' => $email_configuration]);
            }
            throw new \Exception('Failed to create record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\EmailConfiguration $email
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $email = EmailConfiguration::find($id);
        $brands = Brand::get(['id', 'name', 'brand_key']);
        $parent_ids = EmailConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.email-configuration.edit', compact('email', 'brands', 'parent_ids'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\EmailConfiguration $email
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        Session::put('previous_url', url()->current());

        $email = EmailConfiguration::find($id);
        $brands = Brand::get(['id', 'name', 'brand_key']);
        $parent_ids = EmailConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.email-configuration.edit', compact('email', 'brands', 'parent_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\EmailConfiguration $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'brand_key' => 'required',
                'email' => 'required|email',
                'client_id' => 'required_if:parent_id,0|nullable|string',
                'client_secret' => 'required_if:parent_id,0|nullable|string',
                'api_key' => 'nullable|string',
                'parent_id' => 'required',
                'provider' => 'required|integer',
                'status' => 'required|in:1,0',
            ];
            $messages = [
                'brand_key.required' => 'The brand key is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'client_id.required' => 'The client ID is required.',
                'client_secret.required' => 'The client secret is required.',
                'parent_id.required' => 'The parent ID is required.',
                'provider.required' => 'The provider field is required.',
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either "on" or "off".',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $email = EmailConfiguration::find($id);

            if (!$email) {
                return response()->json(['error' => 'Email  Configuration not found'], 404);
            }

            if ($request->parent_id > 0) {
                $parent_email = EmailConfiguration::where('id', $request->get('parent_id'))->first(['client_id', 'client_secret']);

                if (!$parent_email) {
                    return response()->json(['error' => 'Oops! Parent email not found.'], 404);
                }

                $client_id = $request->client_id ?: $parent_email->client_id;
                $client_secret = $request->client_secret ?: $parent_email->client_secret;
            } else {
                $client_id = $request->client_id;
                $client_secret = $request->client_secret;
            }

            $email->brand_key = $request->brand_key;
            $email->email = $request->email;
            $email->client_id = $client_id;
            $email->client_secret = $client_secret;
            $email->api_key = $request->api_key;
            $email->parent_id = $request->parent_id;
            $email->provider = $request->provider;
            $email->status = $request->status;
            $email->save();
            return response()->json(['success' => 'Email  Configuration updated successfully', 'data' => $email]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\EmailConfiguration $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $email = EmailConfiguration::find($id);
            if (!$email) {
                return response()->json(['error' => 'Record not found'], 404);
            }
            $email->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function trashed()
    {
        $emails = EmailConfiguration::onlyTrashed()->get();
        return view('admin.email-configuration.trashed', compact('emails'));
    }


    public function restore($id)
    {
        try {
            EmailConfiguration::onlyTrashed()->whereId($id)->restore();
            return response()->json(['success' => 'Record restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function restore_all()
    {
        try {
            EmailConfiguration::onlyTrashed()->restore();
            return response()->json(['success' => 'All trashed records restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function force_delete($id)
    {
        try {
            EmailConfiguration::onlyTrashed()->whereId($id)->forceDelete();
            return response()->json(['success' => 'Record permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function change_status(Request $request)
    {
        $email = EmailConfiguration::find($request->id);
        $email->status = $request->status;
        $email->save();
        return response()->json(['success' => 'Status change successfully.']);
    }

}
