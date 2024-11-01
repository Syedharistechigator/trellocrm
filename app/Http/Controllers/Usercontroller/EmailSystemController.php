<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignUserBrandEmail;
use App\Models\UserEmailSignature;
use DateTime;
use DateTimeZone;
use Exception;
use http\Exception\RuntimeException;
use Hybridauth\Provider\Google;
use Illuminate\Http\Request;
use App\Models\EmailConfiguration;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Str;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class EmailSystemController extends Controller
{
    function convertArraysToObjects($data)
    {
        if (is_array($data)) {
            // Check if it's a numeric array and has more than one element
            if (array_values($data) === $data && count($data) > 1) {
                return array_map(function ($item) {
                    return $this->convertArraysToObjects($item);
                }, $data);
            } else {
                // If it's a numeric array with one or zero elements, keep the original value
                return reset($data);
            }
        }

        if ($data instanceof \Illuminate\Support\Collection) {
            return $data->map(function ($item) {
                return $this->convertArraysToObjects($item);
            });
        }

        if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->{$key} = $this->convertArraysToObjects($value);
            }
            return $data;
        }

        return $data;
    }

    private function get_email_configuration($email)
    {
        $base_email = EmailConfiguration::where('email', $email)->first();
        if (!$base_email) {
            throw new \RuntimeException('Invalid URL');
        }
        $email_assigned = AssignUserBrandEmail::where('email_configuration_id', $base_email->id)
            ->where('user_id', auth()->user()->id)
            ->first();
        if (!$email_assigned) {
            throw new \RuntimeException('Gotcha! Email not assigned to you.');
        }
        return $base_email;
    }

    private function get_email_token($email_configuration)
    {
        $email_token = json_decode($email_configuration->access_token, true, 512, JSON_THROW_ON_ERROR);
        if (!isset($email_token['access_token'])) {
            throw new \RuntimeException('Access token not found in the decoded JSON.');
        }
        return $email_token['access_token'];
    }

    private function make_fetch_messages_gmail_api_request($email, $token, $query_parameters)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://gmail.googleapis.com/gmail/v1/users/me/messages' . $query_parameters);
    }

    private function make_fetch_message_by_id_gmail_api_request($email, $token, $id)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $id);
    }

    private function make_message_status_change_gmail_api_request($email, $token, $message_id, $add_label_arr, $remove_label_arr)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $message_id . '/modify', [
            'addLabelIds' => $add_label_arr,
            'removeLabelIds' => $remove_label_arr,
        ]);
    }

    private function make_other_contact_api($token, $search)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get("https://people.googleapis.com/v1/otherContacts:search?query={$search}&readMask=emailAddresses");
    }

    /**
     * Send a Gmail message.
     *
     * @param string $email
     * @param string $token
     * @param array $message
     * @return \Illuminate\Http\Client\Response
     */
    private function make_send_message_gmail_api_request($email, $token, $message)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', $message);
    }

    private function make_fetch_threads_gmail_api_request($email, $token, $thread_id)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get("https://gmail.googleapis.com/gmail/v1/users/me/threads/{$thread_id}");
    }

    private function fetch_main_message_by_thread_id($email, $token, $threadId)
    {
        $response = $this->make_fetch_threads_gmail_api_request($email, $token, $threadId);
        $result_arr = $response->json();
        if ($result_arr && isset($result_arr['messages']) && !empty($result_arr['messages']) && isset($result_arr['messages'][0]) && !empty($result_arr['messages'][0])) {
            return $result_arr['messages'][0];
        }
        return null;
    }

    private function fetch_messages($result_arr, $email, $token, $unique_message_ids = []): array
    {
        $messages_arr = [];
        $messages_arr['messages'] = [];
        $uniqueThreadIds = [];
        if (is_array($result_arr) || is_object($result_arr)) {
            if (isset($result_arr['messages'])) {
                foreach ($result_arr as $msgData) {
                    if (is_array($msgData) || is_object($msgData)) {
                        foreach ($msgData as $key => $MsgList) {
                            $id = Arr::get($MsgList, 'id');
                            $threadId = Arr::get($MsgList, 'threadId');
                            if ($id === $threadId) {
                                $message = $this->make_fetch_message_by_id_gmail_api_request($email, $token, $id)->json();
                                if (!in_array($id, $unique_message_ids, true)) {

                                    if ($this->check_thread_has_unread_label($email, $token, $threadId)) {
                                        $message['labelIds'][] = 'UNREAD';
                                    }
                                    $messages_arr['messages'][] = $message;
                                    $unique_message_ids[] = $id;

                                    if (!in_array($threadId, $uniqueThreadIds, true)) {
                                        $uniqueThreadIds[] = $threadId;
                                    }
                                }
                            } elseif (!in_array($threadId, $uniqueThreadIds, true)) {
                                $mainMessage = $this->fetch_main_message_by_thread_id($email, $token, $threadId);
                                if ($mainMessage) {
                                    if (!in_array($mainMessage['id'], $unique_message_ids, true)) {
                                        if ($this->check_thread_has_unread_label($email, $token, $threadId)) {
                                            $mainMessage['labelIds'][] = 'UNREAD';
                                        }
                                        $messages_arr['messages'][] = $mainMessage;
                                        $unique_message_ids[] = $mainMessage['id'];
                                        $uniqueThreadIds[] = $threadId;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $messages_arr['unique_ids'] = $uniqueThreadIds;
        $messages_arr['message_ids'] = $unique_message_ids;
        return $messages_arr;
    }

    private function check_thread_has_unread_label($email, $token, $threadId): bool
    {
        $thread_messages = $this->make_fetch_threads_gmail_api_request($email, $token, $threadId);
        if (!$thread_messages || $thread_messages->failed() || $thread_messages->status() === 401) {
            throw new \RuntimeException("Emails not fetched.");
        }
        $thread_messages = $thread_messages->json();

        if (!empty($thread_messages) && isset($thread_messages['messages']) && (is_array($thread_messages['messages']) || is_object($thread_messages['messages']))) {
            foreach ($thread_messages['messages'] as $key => $thread_message) {
                if (isset($thread_message['id'])) {
                    if (isset($thread_message['labelIds']) && in_array('UNREAD', $thread_message['labelIds'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function get_mail_box_type($routeName)
    {
        if (Str::contains($routeName, 'sent')) {
            return '&q=in:sent';
        }
        if (Str::contains($routeName, 'spam')) {
            return '&q=in:spam';
        }
        if (Str::contains($routeName, 'trash')) {
            return '&q=in:trash';
        }
        return '&q=in:inbox';
    }

    private function get_query_parameters($base_email, $request)
    {
        $max_results = 8;
        if ($request->has('max_results')) {
            $max_results = $request->max_results;
        }
        $queryParameters = '?maxResults=' . $max_results . $this->get_mail_box_type($request->route()->getName());
        if ($base_email->parent_id > 0) {
            $queryParameters .= rawurlencode(' ' . ' label:' . $base_email->email);
        }
        if ($request->has('next_page_token')) {
            $queryParameters .= '&pageToken=' . $request->next_page_token;
        }
        if ($request->has('search')) {
            $queryParameters .= '&q=' . rawurlencode(' ' . $request->search);
        }
        return $queryParameters;
    }


//    public function get_messages_with_thread_id($email, $token, $thread_id)
//    {
//        try {
//            $response = $this->make_fetch_threads_gmail_api_request($email, $token, $thread_id);
//            return response()->json(['status' => 1, 'message' => 'message fetched']);
//        } catch (\Exception $e) {
//            Log::driver('email_system')->debug('Error fetching thread messages : ' . $e->getMessage());
//            throw new \RuntimeException('thread messages not fetched');
//        }
//    }
    /**
     * @throws \JsonException
     */
    public function index(Request $request, $email = null)
    {
        $this->validate($request, [
            'qs' => 'nullable|string',
        ]);
        $all_messages = $next_page_token = $base_label_email = $route_name = null;
        $unique_message_ids = [];
        if ($request->has('unique_message_ids')) {
            $unique_message_ids = $request->unique_message_ids;
        }
        try {
            if ($request->has('next_page_token') && $request->next_page_token == 'last-page') {
                throw new \RuntimeException('Fetched all records');
            }
            $base_email = $this->get_email_configuration($email);
            $base_label_email = $base_email->email;
            $email_configuration = ($base_email->parent_id != 0) ? EmailConfiguration::where('id', $base_email->parent_id)->first() : $base_email;
            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }
            $email = $email_configuration->email;

            $email_signature = UserEmailSignature::where('email_configuration_id', $base_email->id)->where('user_id', auth()->user()->id)->where('status', 1)->first();
            $signature = $email_signature ? $email_signature->signature : null;

            $token = $this->get_email_token($email_configuration);

            $query_parameters = $this->get_query_parameters($base_email, $request);

            $route_name = Str::contains(request()->url(), 'inbox') ? 'Inbox' : (Str::contains(request()->url(), 'sent') ? 'Sent' : (Str::contains(request()->url(), 'spam') ? 'Spam' : (Str::contains(request()->url(), 'trash') ? 'Trash' : 'Inbox')));
            $response = $this->make_fetch_messages_gmail_api_request($email, $token, $query_parameters);
            if (!$response || $response->failed() || $response->status() === 401) {
                if ($response->tooManyRequests()) {
                    throw new \RuntimeException($response->reason());
                }
                try {
                    $refreshed_token = $this->refresh_and_retry($email_configuration);
                    if (!$refreshed_token) {
                        throw new \RuntimeException("Token Expire : 270");
                    }
                    $response = $this->make_fetch_messages_gmail_api_request($email, $refreshed_token, $query_parameters);
                    if (!$response || $response->failed() || $response->status() === 401) {
                        throw new \RuntimeException("Emails not fetched.");
                    }
                    return $this->index($request, $base_email->email);
                } catch (Exception $e) {
                    throw new \RuntimeException('Limit Exceeded');
                }
            }
            $result_arr = $response->json();
            if (isset($result_arr['nextPageToken'])) {
                $next_page_token = $result_arr['nextPageToken'];
            } else {
                $next_page_token = 'last-page';
            }
            $messages_arr = $this->fetch_messages($result_arr, $email, $token, $unique_message_ids);
            if (!empty($messages_arr['messages'])) {
                $all_messages = $this->append_messages($messages_arr['messages'], $base_label_email, $route_name, $token);
            }
            if (!empty($messages_arr['message_ids'])) {
                $unique_message_ids = $messages_arr['message_ids'];
            }
            if ($request->ajax()) {
                return response()->json(['all_messages' => $all_messages, 'next_page_token' => $next_page_token, 'unique_message_ids' => $unique_message_ids, 'status' => 1]);
            }
            return view('email-system.index', compact('signature', 'unique_message_ids', 'email', 'base_label_email', 'all_messages', 'next_page_token', 'route_name'));
        } catch (\Exception $e) {
//            dd($e->getMessage());
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            $errorMessage = 'An error occurred during API request.';
            if ($e instanceof \RuntimeException) {
                $errorMessage = $e->getMessage();
            }
            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage, 'status' => 0]);
            }
            return view('email-system.index', compact('signature', 'unique_message_ids', 'email', 'base_label_email', 'all_messages', 'next_page_token', 'route_name'))->withErrors(['error' => $errorMessage]);
        }
    }

    function fetchEmailBody($payload)
    {
        $emailBody = '';

        if (isset($payload['body']) && isset($payload['body']['size']) && $payload['body']['size'] > 0) {
            $update = str_replace('-', '+', $payload['body']['data']);
            $decode = str_replace('_', '/', $update);
            $emailBody .= base64_decode($decode);
        }

        if (isset($payload['parts'])) {
            foreach ($payload['parts'] as $part) {
                if (isset($part['body']) && isset($part['body']['size']) && $part['body']['size'] > 0 && isset($part['body']['data'])) {
                    $emailBody = '';
                    $update = str_replace('-', '+', $part['body']['data']);
                    $decode = str_replace('_', '/', $update);
                    $emailBody .= base64_decode($decode);
                } elseif (isset($part['parts'])) {
                    $partBody = $this->fetchEmailBody($part);
                    if (!empty($partBody)) {
                        $emailBody .= $partBody;
                    }
                }
            }
        }
        return $emailBody;
    }

    /**
     * Extracts email addresses from the header string.
     *
     * @param string $headerString
     * @return array
     */
    private function extractEmailFromHeader($headerString)
    {
        $emails = [];

        preg_match_all('/[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}/', $headerString, $matches);

        foreach ($matches[0] as $email) {
            if (!in_array($email, $emails, true)) {
                $emails[] = $email;
            }
        }

        return $emails;
    }

    public function get_name_alphabet($email)
    {
        $nameAlphabet = '';
        if (isset($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $nameAlphabet = substr($email, 0, 2);
            } else {
                foreach (explode(' ', trim($email)) as $word) {
                    $nameAlphabet .= strtoupper($word[0]);
                }
                $nameAlphabet = substr($nameAlphabet, 0, 2);
            }
        }
        return $nameAlphabet;
    }

    public function get_color_class($email)
    {
        $fnl = strtolower(substr($email ?? "", 0, 1));
        if ($fnl >= 'a' && $fnl <= 'e') {
            $colorClass = 'color1';
        } elseif ($fnl >= 'f' && $fnl <= 'j') {
            $colorClass = 'color2';
        } elseif ($fnl >= 'k' && $fnl <= 'o') {
            $colorClass = 'color3';
        } elseif ($fnl >= 'p' && $fnl <= 't') {
            $colorClass = 'color4';
        } elseif ($fnl >= 'u' && $fnl <= 'x') {
            $colorClass = 'color5';
        } else {
            $colorClass = 'color6';
        }
        return $colorClass;
    }

    public function read_message(Request $request, $label_email, $type, $message_id)
    {
        $route_name = $type ?? (Str::contains(request()->url(), 'inbox') ? 'Inbox' : (Str::contains(request()->url(), 'sent') ? 'Sent' : (Str::contains(request()->url(), 'spam') ? 'Spam' : (Str::contains(request()->url(), 'trash') ? 'Trash' : 'Inbox'))));
        $all_messages = null;
        try {
            if (!$message_id || !$label_email) {
                return back()->with('error', 'Oops! Message not found.');
            }
            $label_email_configuration = $this->get_email_configuration($label_email);
            $email_configuration = ($label_email_configuration->parent_id != 0) ? EmailConfiguration::where('id', $label_email_configuration->parent_id)->first() : $label_email_configuration;
            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }
            $email = $email_configuration->email;
            $token = $this->get_email_token($email_configuration);
            $message_response = $this->make_fetch_message_by_id_gmail_api_request($email_configuration->email, $token, $message_id);
            if (!$message_response || $message_response->failed() || $message_response->status() === 401) {

                if ($message_response->tooManyRequests()) {
                    throw new \RuntimeException($message_response->reason());
                }
                try {
                    if (!$this->refresh_and_retry($email_configuration)) {
                        throw new \RuntimeException("Token Expire : 334");
                    }
                    return redirect()->route('user.email.system.read.message', ['email' => $label_email, 'type' => strtolower($route_name), 'message_id' => $message_id]);
                } catch (Exception $e) {
                    throw new \RuntimeException('Limit Exceeded');
                }
            }
            $message_response = $message_response->json();
            $all_messages['email_headers'] = $this->extract_email_headers($message_response);

            $thread_id = $message_response['threadId'] ?? null;
            if ($thread_id) {
                try {
                    $thread_messages = $this->make_fetch_threads_gmail_api_request($label_email, $token, $thread_id);
                    if (!$thread_messages || $thread_messages->failed() || $thread_messages->status() === 401) {
                        throw new \RuntimeException("Emails not fetched.");
                    }
                    $thread_messages = $thread_messages->json();

                    if ($label_email_configuration->parent_id == 0) {
                        $extract_color_name_parent_email = substr(strrchr($label_email, "@"), 1);
                        $thread_messages['name_alphabet'] = $this->get_name_alphabet($extract_color_name_parent_email);
                        $thread_messages['color_class'] = $this->get_color_class($extract_color_name_parent_email);
                    } else {
                        $thread_messages['name_alphabet'] = $this->get_name_alphabet($label_email);
                        $thread_messages['color_class'] = $this->get_color_class($label_email);
                    }

                    if (!empty($thread_messages) && isset($thread_messages['messages']) && (is_array($thread_messages['messages']) || is_object($thread_messages['messages']))) {
                        foreach ($thread_messages['messages'] as $key => $thread_message) {
                            if (isset($thread_message['id'])) {
                                if (isset($thread_messages['messages'][$key]['labelIds']) && in_array('UNREAD', $thread_messages['messages'][$key]['labelIds'])) {
                                    $remove_label = ['UNREAD'];
                                    $this->make_message_status_change_gmail_api_request($label_email, $token, $thread_messages['messages'][$key]['id'], [], $remove_label);
                                }

                                $thread_messages['messages'][$key]['message_headers'] = $thread_message_headers = $this->extract_email_headers($thread_message);
                                $thread_messages['messages'][$key]['message_from'] = isset($thread_message_headers['From']) ? $this->extract_email_subject($thread_message_headers['From']) : "Not Found";
                                $thread_messages['messages'][$key]['message_to'] = isset($thread_message_headers['To']) ? $this->extract_email_subject($thread_message_headers['To']) : "Not Found";
                                $thread_messages['messages'][$key]['message_subject'] = isset($thread_message_headers['Subject']) ? $this->format_subject($thread_message_headers['Subject']) : "No Subject";
                                $thread_messages['messages'][$key]['message_snippet'] = $this->process_snippet($thread_message['snippet']);
                                $thread_messages['messages'][$key]['message_date'] = $thread_message_headers['Date'];
                                $thread_messages['messages'][$key]['message_body'] = $this->fetchEmailBody($thread_message['payload']);
                                $from = $this->extractEmailFromHeader($thread_message_headers['From'] ?? "");
                                $to = $this->extractEmailFromHeader($thread_message_headers['To'] ?? "");
                                $cc = $this->extractEmailFromHeader($thread_message_headers['Cc'] ?? "");
                                $bcc = $this->extractEmailFromHeader($thread_message_headers['Bcc'] ?? "");

                                if ($thread_messages['messages'][$key]['message_from'] == $email_configuration->email) {
                                    $extract_color_name_email = substr(strrchr($label_email, "@"), 1);
                                } else {
                                    $extract_color_name_email = $thread_messages['messages'][$key]['message_from'];
                                }

                                $thread_messages['messages'][$key]['name_alphabet'] = $this->get_name_alphabet($extract_color_name_email);
                                $thread_messages['messages'][$key]['color_class'] = $this->get_color_class($extract_color_name_email);

                                $thread_messages['messages'][$key]['From'] = $from;
                                $thread_messages['messages'][$key]['To'] = $to;
                                $thread_messages['messages'][$key]['Cc'] = $cc;
                                $thread_messages['messages'][$key]['Bcc'] = $bcc;
                                $thread_messages['messages'][$key]['message_attachments'] = [];

                                if (isset($thread_message['payload']['parts'])) {
                                    foreach ($thread_message['payload']['parts'] as $part) {
                                        if (isset($part['body']['attachmentId'])) {
                                            try {
                                                $response = Http::withHeaders([
                                                    'Content-Type' => 'application/json',
                                                    'Authorization' => 'Bearer ' . $token,
                                                ])->get('https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $thread_message['id'] . '/attachments/' . $part['body']['attachmentId']);

                                                $attachment_info = json_decode($response, true);
                                                $attachment_info['attachmentId'] = $part['body']['attachmentId'];
                                                $attachment_info['filename'] = $part['filename'] ?? 'Unknown';
                                                $attachment_info['file'] = "Unknown";
                                                if (isset($attachment_info['data'])) {
                                                    $attachment_file = str_replace(array('-', '_'), array('+', '/'), $attachment_info['data']);
                                                    $attachment_info['file'] = $attachment_file;
                                                }
                                                $attachment_info['mimeType'] = $part['mimeType'] ?? 'Unknown';
                                                $file_name = $attachment_info['filename'];
                                                $attachment_info['file_extension'] = pathinfo($file_name, PATHINFO_EXTENSION);

                                                $thread_messages['messages'][$key]['message_attachments'][] = $attachment_info;
                                            } catch (\Exception $e) {
                                                Log::driver('email_system')->debug('Error retrieving attachment: ' . $e->getMessage());
                                                throw new \RuntimeException('Failed to retrieve attachment' . $e->getMessage());
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (isset($all_messages['email_headers']) && isset($all_messages['email_headers']['Subject'])) {
                        $thread_messages['Subject'] = $all_messages['email_headers']['Subject'];
                    }
                    $all_messages = $thread_messages;
                } catch (\Exception $e) {
                    Log::driver('email_system')->debug('Error fetching thread messages : ' . $e->getMessage());
                    throw new \RuntimeException('thread messages not fetched' . $e->getMessage());
                }
            }
            return view('email-system.read-mail', compact('email','all_messages', 'route_name', 'label_email', 'message_id'));

        } catch (\Exception $e) {
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            $errorMessage = 'An error occurred during API request.';
            if ($e instanceof \RuntimeException) {
                $errorMessage = $e->getMessage();
            }
            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage, 'status' => 0]);
            }
            return view('email-system.read-mail', compact('email','all_messages', 'route_name', 'label_email', 'message_id'))->withErrors(['error' => $errorMessage]);
        }
    }


    public function reply_message_body(Request $request)
    {
        try {
            if (!$request->message_id || !$request->label_email || !$request->thread_id) {
                return back()->with('error', 'Oops! Message not found.');
            }
            $message_id = $request->message_id;
            $message_key = $request->message_key;
            $label_email = $request->label_email;
            $thread_id = $request->thread_id;
            $label_email_configuration = $this->get_email_configuration($label_email);
            $email_configuration = ($label_email_configuration->parent_id != 0) ? EmailConfiguration::where('id', $label_email_configuration->parent_id)->first() : $label_email_configuration;
            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }
            $token = $this->get_email_token($email_configuration);
            $thread_messages = $this->make_fetch_threads_gmail_api_request($label_email, $token, $thread_id);
            if (!$thread_messages || $thread_messages->failed() || $thread_messages->status() === 401) {
                return response()->json(['error'=> 'Api error']);
            }
            $thread_messages = $thread_messages->json();
            foreach ($thread_messages['messages'] as $key => $thread_message) {
                if (isset($thread_message['id']) && $thread_message['id'] == $message_id && $thread_message['threadId'] == $thread_id && $message_key == $key) {
                    $thread_message_headers = $this->extract_email_headers($thread_message);
                    $from = $this->extractEmailFromHeader($thread_message_headers['From'] ?? "");
                    $cc = $this->extractEmailFromHeader($thread_message_headers['Cc'] ?? "");
                    $bcc = $this->extractEmailFromHeader($thread_message_headers['Bcc'] ?? "");
                    $message_body = $this->fetchEmailBody($thread_message['payload']);
                    return response()->json(['body' => $message_body, 'from' => $from, 'cc' => $cc, 'bcc' => $bcc]);
                }
            }

        } catch (\Exception $e) {
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            return response()->json(['error'=> 'Api error']);
        }
    }

    /**
     * Send attachments.
     * @param Request $request
     * @param $message
     */
    public function add_attachment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attachments.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,mp4', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {

                $file_directory_path = public_path("assets/files/email-system-temp/{$attachment->getMimeType()}/");

                $file_name = time() . '-' . auth()->user()->id . rand(11, 20) . '.' . $attachment->getClientOriginalExtension();
                $attachment->move($file_directory_path, $file_name);
                $attachments[] = $file_name;
            }
            return response()->json(['message' => 'Files uploaded successfully.', 'attachments' => $attachments]);
        }
        return response()->json(['message' => 'No files uploaded.']);
    }

    /**
     * Add attachments.
     * @param Request $request
     * @param $message
     */
    public function send_attachments(Request $request, $message)
    {
        if ($request->hasFile('attachments')) {

            foreach ($request->file('attachments') as $attachment) {
                $attachmentPath = $attachment->path();
                $attachmentName = $attachment->getClientOriginalName();
                $mimeType = $attachment->getClientMimeType();

                $contentDisposition = "attachment";
                if (Str::startsWith($mimeType, 'image/')) {
                    $contentDisposition = "inline";
                }

                $swiftAttachment = Swift_Attachment::fromPath($attachmentPath)
                    ->setFilename($attachmentName)
                    ->setContentType($mimeType)
                    ->setDisposition($contentDisposition);
                $message->attach($swiftAttachment);
            }
        }
    }

    /**
     * Send a compose message.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \JsonException
     */
    public function compose_message(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'to' => 'required|array',
                'to.*' => 'required|email',
                'cc' => 'array',
                'cc.*' => 'email',
                'bcc' => 'array',
                'bcc.*' => 'email',
//                'subject' => 'string',
//                'compose_message' => 'string',
                'email' => 'required|email',
            ]);


            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $base_email = $this->get_email_configuration($request->email);
            $email_configuration = ($base_email->parent_id != 0)
                ? EmailConfiguration::where('id', $base_email->parent_id)->first()
                : $base_email;

            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }

            $email = $email_configuration->email;
            $token = $this->get_email_token($email_configuration);
            $from_email_subject = $this->extract_email_subject($base_email->email);

            $transport = (new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
                ->setUsername($email)
                ->setPassword($token);

            $mailer = new Swift_Mailer($transport);

            $message = (new Swift_Message($request->get('subject')))
                ->setFrom([$base_email->email => $from_email_subject])
                ->setTo($request->to)
                ->setCc($request->cc)
                ->setBcc($request->bcc)
                ->setBody($request->compose_message, 'text/html');

            $this->send_attachments($request, $message);

            $result = $mailer->send($message);
            return response()->json(['status' => 1, 'result' => $result, 'message' => 'Compose message successfully']);
        } catch (\Exception $e) {
            Log::driver('email_system')->debug('Error sending reply: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'status' => 0]);
        }
    }

    /**
     * Send a reply message.
     *
     * @param Request $request
     * @param string $email
     * @param string $messageId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \JsonException
     */
    public function reply_message(Request $request)
    {
        try {

            $rules = [
                'to' => 'array',
                'to.*' => 'email',
                'cc' => 'array',
                'cc.*' => 'email',
                'bcc' => 'array',
                'bcc.*' => 'email',
//                'reply_message' => 'required',
                'email' => 'required|email',

            ];
            $messages = [
                'email.required' => 'The Email field is required.',
                'message_id.required' => 'The Message Id field is required.',
                'reply_message.required' => 'The Reply Message field is required.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $base_email = $this->get_email_configuration($request->email);
            $email_configuration = ($base_email->parent_id != 0)
                ? EmailConfiguration::where('id', $base_email->parent_id)->first()
                : $base_email;

            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }

            $email = $email_configuration->email;
            $token = $this->get_email_token($email_configuration);

            $original_message = $this->make_fetch_message_by_id_gmail_api_request($email, $token, $request->message_id);
            if (!$original_message || $original_message->failed() || $original_message->status() === 401) {
                throw new \RuntimeException('Failed to fetch original message.');
            }
            $original_message = $original_message->json();

            $email_headers = $this->extract_email_headers($original_message);

            $replySubject = isset($email_headers['Subject']) ? $email_headers['Subject'] : 'No Subject';
            if ($request->has('to') && !empty($request->get('to'))) {
                $email_to = $request->to;
            } else {
                $email_to[] = $this->extract_from_email($email_headers['From']);
            }

            if ($this->extract_from_email($email_headers['To']) == $base_email->email) {
                $from_email_subject = $this->extract_email_subject($email_headers['To']);

            } else {
                $from_email_subject = $this->extract_email_subject($base_email->email);
            }
            if (!$email_to) {
                throw new \RuntimeException('Oop! To email not found.');
            }
            $threadId = $request->has('thread_id') ? $request->input('thread_id') : $original_message['threadId'];
            $transport = (new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
                ->setUsername($email)
                ->setPassword($token);

            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message($replySubject))
                ->setFrom([$base_email->email => $from_email_subject])
                ->setFrom([$base_email->email => $from_email_subject])
                ->setSubject($replySubject)
                ->setTo($email_to)
                ->setCc($request->cc)
                ->setBcc($request->bcc)
                ->setBody($request->input('reply_message'), 'text/html')
                ->setReplyTo($request->to);

            $this->send_attachments($request, $message);

            $headers = $message->getHeaders();
            $headers->addTextHeader('References', $email_headers['References'] ?? "");
            $headers->addTextHeader('In-Reply-To', $email_headers['In-Reply-To'] ?? "");
            $headers->addTextHeader('threadId', $threadId);
            $result = $mailer->send($message);
            return response()->json(['status' => 1, 'result' => $result, 'message' => 'Reply sent successfully']);
        } catch
        (\Exception $e) {
            Log::driver('email_system')->debug('Error sending reply: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'status' => 0]);
        }
    }

//    private function refresh_and_retry(EmailConfiguration $email)
//    {
//        try {
//            $token = $this->refresh_token($email);
//            $email_token = json_decode($email->access_token, true);
//
//            $refreshed_email = EmailConfiguration::find($email->id);
//            $refreshed_token = json_decode($refreshed_email->access_token, true)['access_token'];
//
//            /** Through this remaining fields won't disturb like if we only fetch access token it will update access token and won't change refresh token to empty*/
//            /** First it will check if access_token available then it will only update fields and if not then update whole access_token instance*/
//            if ($email_token && $token) {
//                if (array_key_exists('access_token', $email_token) && array_key_exists('access_token', $token)) {
//                    $email_token['access_token'] = $token['access_token'];
//                }
//                if (array_key_exists('expires_in', $email_token) && array_key_exists('expires_in', $token)) {
//                    $email_token['expires_in'] = $token['expires_in'];
//                }
//                if (array_key_exists('expires_at', $email_token) && array_key_exists('expires_at', $token)) {
//                    $email_token['expires_at'] = $token['expires_at'];
//                }
//                if (array_key_exists('refresh_token', $email_token) && array_key_exists('refresh_token', $token)) {
//                    $email_token['refresh_token'] = $token['refresh_token'];
//                }
//                $token = $email_token;
//                $email->update(['access_token' => $token]);
//            }
//            return [
//                'refreshed_email' =>$refreshed_email->email,
//                'refreshed_token' =>$refreshed_token,
//            ];
//            /**>==========<**/
//        } catch (Exception $e) {
//            Log::driver('email_system')->debug('Error handling response: ' . $e->getMessage());
//            throw new \RuntimeException('Token Expire');
//        }
//    }
    private function refresh_and_retry(EmailConfiguration $email)
    {
        try {
            $token = $this->refresh_token($email);
            $email_token = json_decode($email->access_token, true);
            if ($email_token && $token) {
                foreach (['access_token', 'expires_in', 'expires_at', 'refresh_token'] as $field) {
                    if (array_key_exists($field, $token)) {
                        $email_token[$field] = $token[$field];
                    }
                }
                $email->access_token = json_encode($email_token);
                $email->save();
            }
            return $token['access_token'];
        } catch (Exception $e) {
            Log::driver('email_system')->debug('Error handling response: ' . $e->getMessage());
            throw new \RuntimeException('Token Refresh Failed: ' . $e->getMessage());
        }
    }

    private function refresh_token(EmailConfiguration $email)
    {
        try {
            $email_token = json_decode($email->access_token, true);
            if (array_key_exists('refresh_token', $email_token)) {
                $response = Http::post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $email_token['refresh_token'],
                    'client_id' => $email->client_id,
                    'client_secret' => $email->client_secret,
                ]);
                if (!$response->successful()) {
                    $error = $response->json('error_description', 'Token Expire : 709');
                    Log::driver('email_system')->debug('Token refresh exception: ' . $error);
                    throw new Exception($error);
                }
                return $response->json();
            }
            throw new \RuntimeException('Refresh token not found.!');
        } catch (Exception $e) {
            Log::driver('email_system')->debug('Token refresh exception: ' . $e->getMessage());
            throw new Exception('Token Expire : 718');
        }
    }

    private function extract_email_headers($message): array
    {
        $email_headers = [];
        if (is_array($message) && isset($message['payload']) && is_array($message['payload']['headers']) && !empty($message['payload']['headers'])) {
            foreach ($message['payload']['headers'] as $header) {
                switch ($header['name']) {
                    case 'To':
                    case 'Cc':
                    case 'Bcc':
                    case 'From':
                    case 'Subject':
                    case 'Date':
                    case 'References':
                    case 'In-Reply-To':
                    case 'Message-ID':
                        $email_headers[$header['name']] = $header['value'];
                        break;
                }
            }
        }
        return $email_headers;
    }

    private function extract_from_email($fromHeader)
    {
        $matches = [];

        preg_match('/<([^>]+)>/', $fromHeader, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    private function extract_email_subject($fromHeader)
    {
        $matches = [];
        preg_match('/"?(.*?)"?\s*<(.+)>/', $fromHeader, $matches);
        if (empty($matches)) {
            preg_match('/<([^>]+)>/', $fromHeader, $matches);
        }
        if (isset($matches[1]) && !empty($matches[1])) {
            return $matches[1];
        } elseif (isset($matches[2]) && !empty($matches[2])) {
            $emailParts = explode('@', $matches[2]);
            return isset($emailParts[0]) ? $emailParts[0] : '';
        }
        return preg_replace('/[<>]/', '', $fromHeader);
    }

    private function get_formatted_from($fromHeader)
    {
        $extractedFrom = $this->extract_email_subject($fromHeader);

        if (!empty($extractedFrom)) {
            // If a name is extracted, capitalize the first letter
            return ucfirst($extractedFrom);
        } else {
            // If no name is extracted, return the original From header
            return ucfirst($fromHeader);
        }
    }

    private function format_subject($subject)
    {
        $subject = strip_tags($subject);
        return strlen($subject) > 25 ? html_entity_decode(substr($subject, 0, 24)) . ' ...' : (empty($subject) ? '(no subject)' : html_entity_decode($subject));
    }

    private function process_snippet($snippet)
    {
        $snippet = strip_tags(html_entity_decode($snippet));
        $snippet = mb_convert_encoding($snippet, 'UTF-8', 'HTML-ENTITIES');
        $snippet = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}]/u', '', $snippet);
        $snippet = trim(preg_replace('/[^\x00-\x7F]/u', '', $snippet));
        return substr($snippet, 0, 110) . (strlen($snippet) > 110 ? '...' : '');
    }

    public function append_messages($messagesArr, $email, $route_name, $token)
    {
        $html = '';
        if (!empty($messagesArr) && (is_array($messagesArr) || is_object($messagesArr))) {
            foreach ($messagesArr as $key => $message) {
                $subject_label = '';
                $unique_emails = [];
                $unique_emails[] = $email;
                if (isset($message['id'])) {
                    $is_unread = isset($message["labelIds"]) && in_array("UNREAD", $message["labelIds"], true);

                    $email_headers = $this->extract_email_headers($message);
                    $from = $this->extract_email_subject($email_headers['From'] ?? "");
                    $to = $this->extract_email_subject($email_headers['To'] ?? "");

                    if (Str::contains($route_name, 'Sent')) {
                        $subject_label = "To: " . $to;
                    }
                    if (Str::contains($email_headers['From'], $email) && Str::contains($email_headers['To'], $email)) {
                        $subject_label .= "me";
                    } else {
                        if (Str::contains($email_headers['From'], $email)) {
                            $from = "me";
                        }
                        if (Str::contains($email_headers['To'], $email)) {
                            $to = "me";
                        }
                        if (Str::contains($email_headers['From'], $email)) {
                            $subject_label .= $this->extract_email_subject($to);
                        } elseif (Str::contains($email_headers['To'], $email)) {
                            $subject_label .= $this->extract_email_subject($from);
                        }

                    }
                    $subject = isset($email_headers['Subject'])
                        ? ($this->format_subject($email_headers['Subject']))
                        : "(no subject)";

                    $thread_id = $message['threadId'] ?? null;
                    if ($thread_id) {
                        try {
                            $thread_messages = $this->make_fetch_threads_gmail_api_request($email, $token, $thread_id);
                            if (!$thread_messages || $thread_messages->failed() || $thread_messages->status() === 401) {
                                throw new \RuntimeException("Emails not fetched.");
                            }
                            $thread_messages = $thread_messages->json();

                            if (!empty($thread_messages) && isset($thread_messages['messages']) && (is_array($thread_messages['messages']) || is_object($thread_messages['messages']))) {
                                $thread_messages_arr = [];
                                foreach ($thread_messages['messages'] as $thread_key => $thread_message) {
                                    if (isset($thread_message['id'])) {
                                        $thread_messages_arr[$thread_key]['thread_message_headers'] = $thread_message_headers = $this->extract_email_headers($thread_message);
                                        $thread_messages_arr[$thread_key]['thread_message_from'] = isset($thread_message_headers['From']) ? $this->extract_email_subject($thread_message_headers['From']) : "Not Found";
                                        $thread_messages_arr[$thread_key]['thread_message_subject'] = isset($thread_message_headers['Subject']) ? $this->format_subject($thread_message_headers['Subject']) : "No Subject";
                                        $thread_messages_arr[$thread_key]['thread_message_snippet'] = $this->process_snippet($thread_message['snippet']);
                                        $thread_messages_arr[$thread_key]['thread_message_date'] = $thread_message_headers['Date'];
                                        $thread_messages_arr[$thread_key]['thread_message_body'] = $this->fetchEmailBody($thread_message['payload']);

                                        if (!in_array($thread_message_headers['From'], $unique_emails, true)) {
                                            $unique_emails[] = $thread_message_headers['From'];
                                        } elseif (!in_array($thread_message_headers['To'], $unique_emails, true)) {
                                            $unique_emails[] = $thread_message_headers['To'];
                                        }
                                    }
                                }

                                if (!Str::contains($route_name, 'Sent')) {
                                    foreach ($unique_emails as $unique_email) {
                                        if (!Str::contains(strtolower($unique_email), strtolower($email)) && !Str::contains($subject_label, $this->extract_email_subject($unique_email)) && !Str::contains($subject_label, $to)) {
                                            if (!empty($subject_label)) {
                                                $subject_label .= ", ";
                                            }
                                            $subject_label .= $this->extract_email_subject($unique_email);
                                        }
                                    }
                                }
                            }

                        } catch (\Exception $e) {
                            Log::driver('email_system')->debug('Error fetching thread messages : ' . $e->getMessage());
                            throw new \RuntimeException('thread messages not fetched');
                        }

                    }

                    if (!empty($unique_emails) && strlen($subject_label) > 15) {
                        $max_length = (count($unique_emails) > 2) ? 13 : 15;
                        $ellipsis = (count($unique_emails) > 2) ? '...' : '.';
                        $subject_label = substr($subject_label, 0, $max_length) . $ellipsis;
                    }

                    if ($thread_messages_arr && is_array($thread_messages_arr)) {
                        $last_thread_message = end($thread_messages_arr);
                        if ($last_thread_message && isset($last_thread_message['thread_message_snippet'])) {
                            $snippet = $this->process_snippet($last_thread_message['thread_message_snippet']);
                        }
                    } else {
                        $snippet = $this->process_snippet($message['snippet']);
                    }
                    $date = $email_headers['Date'];
                    //                    $date2 = date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(now())) ? date("h:i A", strtotime($date)) : (date('Y-m', strtotime($date)) == date('Y-m', strtotime(now())) ? date("M d", strtotime($date)) : date("m/d/y", strtotime($date)));
                    //                    if($key == 33){
                    //                    dd($date,$date2);}

                    $dateTime = new DateTime($date);
                    $dateTime->setTimezone(new DateTimeZone('Asia/Karachi'));
                    $date = $dateTime->format('D, d M Y H:i:s T');
                    $date = date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(now())) ? date("h:i A", strtotime($date)) : (date('Y-m', strtotime($date)) == date('Y-m', strtotime(now())) ? date("M d", strtotime($date)) : date("m/d/y", strtotime($date)));


                    $html .= '<tr class="mail-enter-link position-relative' . ($is_unread ? ' unread-message' : ' read-message') . '" id="' . $message['id'] . '"' . '" data-thread-id="' . $message['threadId'] . '" >';
                    $html .= '<td class="d-none">';
                    $html .= '<div class="icheck-navy">';
                    $html .= '<input type="checkbox" value=" ' . $message['id'] . ' " id="check-' . $message['id'] . '">';
                    $html .= '<label for="check-' . $message['id'] . '"></label>';
                    $html .= '</div>';
                    $html .= '</td>';
                    $html .= '<td class="check-icon">';
                    $html .= '<span class="material-symbols-outlined">check_box_outline_blank</span>';
                    $html .= '</td>';
                    $html .= '<td class="check-star">';
                    $html .= '<span class="material-symbols-outlined">star</span>';
                    $html .= '</td>';
                    $html .= '<td class="mailer-name">';
                    $html .= '<div class="bg-gradient-navy rounded">';
                    $html .= '<a href="' . route('user.email.system.read.message', ['email' => $email, 'type' => strtolower($route_name), 'message_id' => $message['id']]) . '" style="color: #202124;">';
                    $html .= '<span class="xbg-gradient-orange text-wrap text-left"> ' . $subject_label . ' </span>';
                    $html .= '</div>';
                    $html .= '</a>';
                    $html .= '</td>';
                    $html .= '<td class="mail-text p-0">';
                    $html .= '<a href="' . route('user.email.system.read.message', ['email' => $email, 'type' => strtolower($route_name), 'message_id' => $message['id']]) . '" style="color: #202124;">';

                    if (!empty($subject)) {
                        $html .= $subject;
                        if (!empty($snippet)) {
                            $html .= ' - ' . $snippet;
                        }
                    } elseif (!empty($snippet)) {
                        $html .= ' - ' . $snippet;
                    }
                    $html .= '</a>';
                    $html .= '</td>';
                    $html .= '<td class="mail-time">' . $date . '</td>';
                    $html .= '<td class="align-middle"><div class="text-nowrap"></div></td>';
                    $html .= '<td class="mail-hover-list">';
                    if ($is_unread == true) {
                        $html .= '<a href="javascript:void(0);" title="mark as read"><span class="material-symbols-outlined mark-as-read-unread-message" data-message_id="' . $message['id'] . '" data-label="' . $email . '">mark_email_read</span></a>';
                    } else {
                        $html .= '<a href="javascript:void(0);" title="mark as unread"><span class="material-symbols-outlined mark-as-unread-read-message" data-message_id="' . $message['id'] . '" data-label="' . $email . '">mark_email_unread</span></a>';
                    }
                    $html .= '</td>';
                }
            }
            $html .= '</tr>';
            return $html;
        }
    }

    public function mark_as_read_unread(Request $request)
    {

        try {
            $message_id = $request->input('message_id');
            $label_email = $request->input('label');
            $isUnread = $request->input('is_unread');

            $base_email = $this->get_email_configuration($label_email);

            $email_configuration = ($base_email->parent_id != 0)
                ? EmailConfiguration::where('id', $base_email->parent_id)->first()
                : $base_email;

            if (!$email_configuration) {
                throw new \RuntimeException('Oop! Email not found.');
            }

            $email = $email_configuration->email;

            $token = $this->get_email_token($email_configuration);

            $add_label = $isUnread == 1 ? [] : ['UNREAD'];
            $remove_label = $isUnread == 1 ? ['UNREAD'] : [];

//            $message_response = $this->make_fetch_message_by_id_gmail_api_request($email, $token, $message_id)->json();
            $response = $this->make_message_status_change_gmail_api_request($email, $token, $message_id, $add_label, $remove_label);
//            $message_response1 = $this->make_fetch_message_by_id_gmail_api_request($email, $token, $message_id)->json();

            return response()->json(['status' => 1, 'message' => 'Email status updated successfully']);
        } catch (Exception $e) {
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'status' => 0]);
        }
    }


    /**
     * //            $replyMessageContent = strip_tags($request->input('reply_message'));
     * //
     * //            $replyMessage = "Content-Type: text/plain; charset=\"UTF-8\"\n" .
     * //                "MIME-Version: 1.0\n" .
     * //                "Content-Transfer-Encoding: 7bit\n" .
     * //                "Subject: $replySubject\n" .
     * //                "From: {$base_email->email}\n" .
     * //                "To: $email_to\n\n" .
     * //                "In-Reply-To: <{$original_message['id']}>\n" .
     * //                "References: <{$original_message['id']}>\n\n" .
     * //                $replyMessageContent;
     * //
     * //
     * //            $encodedResponse = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($replyMessage));
     * //            $response = Http::withHeaders([
     * //                'Authorization' => 'Bearer ' . $token,
     * //                'Content-Type' => 'application/json',
     * //            ])->post('https://www.googleapis.com/gmail/v1/users/me/messages/send', [
     * //                'raw' => $encodedResponse,
     * //                'threadId' => $threadId,
     * //            ]);
     * //
     * //            if ($response->successful()) {
     * //                return response()->json(['status' => true, 'message' => 'Reply sent successfully']);
     * //            }
     * //            throw new \RuntimeException('Error sending reply.');
     */


    public function email_suggestions(Request $request)
    {


        try {

            $label_email = $request->input('email');
            $search = $request->input('search');

            if ($label_email && $search) {
                $base_email = $this->get_email_configuration($label_email);
                $search = '&query=' . rawurlencode(' ' . $request->search);


                $email_configuration = ($base_email->parent_id != 0)
                    ? EmailConfiguration::where('id', $base_email->parent_id)->first()
                    : $base_email;

                if (!$email_configuration) {
                    throw new \RuntimeException('Oop! Email not found.');
                }

                $token = $this->get_email_token($email_configuration);

                $response = $this->make_other_contact_api($token, $search);
                if (!$response || $response->failed() || $response->status() === 401) {
                    throw new \RuntimeException($response->reason());
                }
                $emails = [];
                if (isset($response->json()['results'])) {
                    foreach ($response->json()['results'] as $result) {
                        if (isset($result['person']['emailAddresses'][0]['value'])) {
                            $emails[] = $result['person']['emailAddresses'][0]['value'];
                        }
                    }
                }
            }
            return response()->json(['search' => $search, 'status' => 1, 'message' => 'Email fetched successfully', 'emails' => $emails]);
        } catch (Exception $e) {
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'status' => 0]);
        }
    }

    public function edit_signature(Request $request)
    {
        try {
            $rules = [
                'label' => 'required',
                'signature' => 'required',
            ];
            $messages = [
                'label.required' => 'Email not found , please try again later.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $base_email = $this->get_email_configuration($request->label);

            $signature = UserEmailSignature::where('email_configuration_id', $base_email->id)->where('user_id', auth()->user()->id)->where('status', 1)->first();
            if (!$signature) {
                $signature = new UserEmailSignature();
                $signature->user_id = auth()->user()->id;
                $signature->email_configuration_id = $base_email->id;
            }
            $signature->signature = $request->signature;
            $signature->save();
            return response()->json(['status' => 1, 'message' => 'Signature updated successfully', 'signature' => $signature->signature]);
        } catch (Exception $e) {
            Log::driver('email_system')->debug('API request exception: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating signature.', 'status' => 0]);
        }
    }
}
