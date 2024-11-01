<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\IpAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IpAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $ip_addresses = IpAddress::all();
        return view('admin.ip-address.index', compact('ip_addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ip-address.create');
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
                'ip_address' => 'required',
                'list_type' => 'required|in:0,1',
                'detail' => 'required',
            ];
            $messages = [
                'list_type.in' => 'The list type must be either Black List or White List.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $ipAddress = IpAddress::create([
                'ip_address' => $request->ip_address,
                'list_type' => $request->list_type,
                'detail' => $request->detail,
            ]);
            if ($ipAddress) {
                return response()->json(['success' => 'created successfully']);
            }
            throw new \Exception('Failed to create IpAddress record.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\IpAddress $ip_address
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $ip_address = IpAddress::find($id);
        return view('admin.ip-address.edit', compact('ip_address'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\IpAddress $ip_address
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ip_address = IpAddress::find($id);
        return view('admin.ip-address.edit', compact('ip_address'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IpAddress $ip_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'ip_address' => 'required',
                'list_type' => 'required|in:0,1',
                'detail' => 'required',
            ];
            $messages = [
                'list_type.in' => 'The list type must be either Black List or White List.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $ip_address = IpAddress::find($id);

            if (!$ip_address) {
                return response()->json(['error' => 'IP address not found'], 404);
            }
            $ip_address->ip_address = $request->ip_address;
            $ip_address->list_type = $request->list_type;
            $ip_address->detail = $request->detail;
            $ip_address->save();

            return response()->json(['success' => 'IP address updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\IpAddress $ip_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $ip_address = IpAddress::find($id);
            if (!$ip_address) {
                return response()->json(['error' => 'IP address not found'], 404);
            }
            $ip_address->delete();
            return response()->json(['success' => 'IP address deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function trashed()
    {
        $ip_addresses = IpAddress::onlyTrashed()->get();
        return view('admin.ip-address.trashed', compact('ip_addresses'));
    }


    public function restore($id)
    {
        try {
            IpAddress::onlyTrashed()->whereId($id)->restore();
            return response()->json(['success' => 'IP address restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function restore_all()
    {
        try {
            IpAddress::onlyTrashed()->restore();
            return response()->json(['success' => 'All trashed IP addresses restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function force_delete($id)
    {
        try {
            IpAddress::onlyTrashed()->whereId($id)->forceDelete();
            return response()->json(['success' => 'IP address permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function change_status(Request $request)
    {
        $ip_address = IpAddress::find($request->ip_address_id);
        $ip_address->status = $request->status;
        $ip_address->save();
        return response()->json(['success' => 'Status change successfully.']);
    }

}
