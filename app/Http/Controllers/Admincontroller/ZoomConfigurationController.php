<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ZoomConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ZoomConfigurationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $emails = ZoomConfiguration::all();
        foreach ($emails as $email){
            if ($email->parent_id != 0) {
                $parentEmail = ZoomConfiguration::find($email->parent_id);
//                $email['token_expire'] = $this->checkTokenExpiration($parentEmail);
            } else {
//                $email['token_expire'] = $this->checkTokenExpiration($email);
            }
        }
        return view('admin.zoom-configuration.index', compact('emails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::all();
        $parent_ids = ZoomConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.zoom-configuration.create', compact('brands', 'parent_ids'));
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
//                'provider' => 'required|integer',
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
//                'provider.required' => 'The provider field is required.',
//                'provider.integer' => 'The provider must be an integer.',
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either 1 or 0.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if ($request->parent_id > 0) {
                $parent_email = ZoomConfiguration::where('id', $request->get('parent_id'))->first(['client_id', 'client_secret']);

                if (!$parent_email) {
                    return response()->json(['error' => 'Oops! Parent email not found.'], 404);
                }

                $client_id = $parent_email->client_id;
                $client_secret = $parent_email->client_secret;
            } else {
                $client_id = $request->client_id;
                $client_secret = $request->client_secret;
            }

            $email_configuration = ZoomConfiguration::create([
                'created_by' => auth()->user()->id,
                'parent_id' => $request->parent_id,
                'brand_key' => $request->brand_key,
//                'provider' => $request->provider,
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
     * @param \App\Models\ZoomConfiguration $email
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $email = ZoomConfiguration::find($id);
        $brands = Brand::get(['id', 'name', 'brand_key']);
        $parent_ids = ZoomConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.zoom-configuration.edit', compact('email', 'brands', 'parent_ids'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ZoomConfiguration $email
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        Session::put('previous_url', url()->current());

        $email = ZoomConfiguration::find($id);
        $brands = Brand::get(['id', 'name', 'brand_key']);
        $parent_ids = ZoomConfiguration::where('parent_id', 0)->where('status', 1)->get(['id', 'email']);
        return view('admin.zoom-configuration.edit', compact('email', 'brands', 'parent_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ZoomConfiguration $email
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
//                'provider' => 'required|integer',
                'status' => 'required|in:1,0',
            ];
            $messages = [
                'brand_key.required' => 'The brand key is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'client_id.required' => 'The client ID is required.',
                'client_secret.required' => 'The client secret is required.',
                'parent_id.required' => 'The parent ID is required.',
//                'provider.required' => 'The provider field is required.',
                'status.required' => 'The status field is required.',
                'status.in' => 'The status field must be either "on" or "off".',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $email = ZoomConfiguration::find($id);

            if (!$email) {
                return response()->json(['error' => 'Zoom Configuration not found'], 404);
            }

            if ($request->parent_id > 0) {
                $parent_email = ZoomConfiguration::where('id', $request->get('parent_id'))->first(['client_id', 'client_secret']);

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
//            $email->provider = $request->provider;
            $email->status = $request->status;
            $email->save();
            return response()->json(['success' => 'Zoom Configuration updated successfully', 'data' => $email]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ZoomConfiguration $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $email = ZoomConfiguration::find($id);
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
        $emails = ZoomConfiguration::onlyTrashed()->get();
        return view('admin.zoom-configuration.trashed', compact('emails'));
    }


    public function restore($id)
    {
        try {
            ZoomConfiguration::onlyTrashed()->whereId($id)->restore();
            return response()->json(['success' => 'Record restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function restore_all()
    {
        try {
            ZoomConfiguration::onlyTrashed()->restore();
            return response()->json(['success' => 'All trashed records restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function force_delete($id)
    {
        try {
            ZoomConfiguration::onlyTrashed()->whereId($id)->forceDelete();
            return response()->json(['success' => 'Record permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function change_status(Request $request)
    {
        $email = ZoomConfiguration::find($request->id);
        $email->status = $request->status;
        $email->save();
        return response()->json(['success' => 'Status change successfully.']);
    }

}
