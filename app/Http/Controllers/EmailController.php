<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendInquiryMail;
use App\Mail\SendMail;
use App\Traits\BrandEmailConfigurationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    use BrandEmailConfigurationTrait;

    public function send_email($subject, $name, $email)
    {
        try {
            $details['subject'] = $subject;
            $details['name'] = $name;
            $details['email'] = $email;
            Log::driver('email')->debug('test-email , details = ' . json_encode($details) . ', emailto = ' . $email);
            \Mail::to('leads@techigator.com')->send(new SendMail($details));
            \Mail::to('developer.michael.09@gmail.com')->send(new SendMail($details));

            return \Mail::to($email)->send(new SendMail($details));
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Email sending error: ' . $e->getMessage());

            // Return a response indicating that there was an issue with sending the email
            return response()->json([
                'message' => 'Failed to send the email. Please check your email settings.',
            ], 500);
        }
    }

    public function send_inquiry_email($subject, $lead, $brandName, $teamName)
    {
        $details = $lead;
        $details['subject'] = $subject;
        $details['brand_name'] = $brandName;
        $details['team_name'] = $teamName;
        Log::driver('email')->debug('inquiry-email , details = ' . json_encode($details));
        try {
            if ($lead['brand_key'] == 136788) {
                \Mail::to('naeem.online@live.com')->send(new SendInquiryMail($details));
            }
            \Mail::to('leads@techigator.com')->send(new SendInquiryMail($details));
            \Mail::to('developer.michael.09@gmail.com')->send(new SendInquiryMail($details));
            return true;
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Email sending error: ' . $e->getMessage());

            // Return a response indicating that there was an issue with sending the email
            return response()->json([
                'message' => 'Failed to send the email. Please check your email settings.',
            ], 500);
        }
    }
}
