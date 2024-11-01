<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Validator};
use Illuminate\Http\Request;

class ApiTrelloAttachmentUrlController extends Controller
{
    public function index()
    {
        $url = DB::table('attachment_urls')->where('status', 0)->orderByRaw('RAND()')->first(['id', 'url', 'status']);
        if ($url) {
            return response()->json(['success' => 'url fetched successfully', 'url' => $url]);
        }
        return response()->json(['success' => 'Data Completed.']);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'urls' => 'required|string',
            ]);
            $urlString = $request->input('urls');
            $urls = array_map('trim', explode("\n", $urlString));
            $validator = Validator::make(['urls' => $urls], [
                'urls' => 'required|array|min:1',
                'urls.*' => 'required|url',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            $values = array_map(function ($url) {
                return "('" . addslashes($url) . "', 0)";
            }, $urls);
            $valuesString = implode(', ', $values);

            DB::statement("INSERT INTO attachment_urls (url, status) VALUES $valuesString");
            return response()->json(['success' => 'URLs inserted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),'line'=>$e->getLine()], 500);
        }
    }
}
