<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\EmailConfiguration;

class ApiGmailController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    // For inbox All Messages Start //
    public function all_inbox($request){
        $email = $request->query('email');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages?' . $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // For inbox All Messages End  //


    // For inbox One Messages Start //
    public function single_inbox_message($request){
        $email = $request->query('email');
        $messageId = $request->query('messageId');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages'.$messageId.'?'. $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // For inbox One Messages End //

    // For spam All Messages Start //
    public function all_spam($request){
        $email = $request->query('email');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages?' . $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // For spam All Messages End //




    // For spam One Messages Start //

    public function single_spam_message($request){
        $email = $request->query('email');
        $messageId = $request->query('messageId');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages'.$messageId.'?'. $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // For spam One Messages End //

    // For trash All Messages Start //
    public function all_trash($request){
        $email = $request->query('email');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages?' . $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // For trash All Messages End //




    // For trash One Messages Start //

    public function single_trash_message($request){
        $email = $request->query('email');
        $messageId = $request->query('messageId');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages'.$messageId.'?'. $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // For trash One Messages End //

    // For send All Messages Start //
    public function all_send($request){
        $email = $request->query('email');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages?' . $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // For send All Messages End //




    // For send One Messages Start //

    public function single_send_message($request){
        $email = $request->query('email');
        $messageId = $request->query('messageId');
        $emailConfiguration = EmailConfiguration::where('email', $email)->first();
        
        if (!$emailConfiguration) {
            return response()->json(['error' => 'Email not configured'], 500);
        }

        $accessTokenJson = json_decode($emailConfiguration['access_token'], true);
        if (empty($accessTokenJson['access_token'])) {
            return response()->json(['error' => 'Access token not configured'], 500);
        }

        $accessToken = $accessTokenJson['access_token'];

        // Build the query parameters dynamically
        $queryParams = [];
        if ($request->query('maxResults')) {
            $queryParams['maxResults'] = $request->query('maxResults');
        } else {
            $queryParams['maxResults'] = 50; // Default to 10 if not provided
        }

        if ($request->query('pageToken')) {
            $queryParams['pageToken'] = $request->query('pageToken');
        }


        if ($request->query('q')) {
            $queryParams['q'] = $request->query('q');
        }
        
        if ($request->query('labelIds')) {
            $queryParams['labelIds'] = $request->query('labelIds');
        }
        if ($request->query('label')) {
            $queryParams['label'] = $request->query('label');
        }
        // Build query string from the parameters array
        $queryString = http_build_query($queryParams);
        $messagesUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages'.$messageId.'?'. $queryString;

        try {
            // Fetch the list of messages
            $response = $this->client->get($messagesUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $messages = json_decode($response->getBody()->getContents(), true);

            $messageList = [];
            $totalCount = isset($messages['resultSizeEstimate']) ? $messages['resultSizeEstimate'] : 0;

            // Loop through each message and get details
            foreach ($messages['messages'] as $message) {
                $messageId = $message['id'];
                $messageDetailUrl = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/' . $messageId;

                // Fetch message details
                $detailResponse = $this->client->get($messageDetailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);



                $messageDetail = json_decode($detailResponse->getBody()->getContents(), true);
                
                // Check if the required keys are present
                $headers = isset($messageDetail['payload']['headers']) ? $messageDetail['payload']['headers'] : [];
                $headerMap = [];
                foreach ($headers as $header) {
                    $headerMap[$header['name']] = $header['value'];
                }
                // dd($messageDetail);
                // Add message details to the list
                $messageList[] = [
                    'threadId' => $messageDetail['threadId'] ?? 'N/A',
                    'labelIds' => $messageDetail['labelIds'] ?? [],
                    'subject' => $headerMap['Subject'] ?? 'N/A',
                    'to' => $headerMap['To'] ?? 'N/A',
                    'from' => $headerMap['From'] ?? 'N/A',
                    'snippet' => $messageDetail['snippet'] ?? 'N/A',
                    'date' => $headerMap['Date'] ?? 'N/A',
                ];
            }
            
            // Include the nextPageToken in the response if available
            $nextPageToken = $messages['nextPageToken'] ?? null;

            return response()->json([
                'messages' => $messageList,
                'response' => $messages,
                'totalCount' => $totalCount,
                'nextPageToken' => $nextPageToken
            ], 200);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client exceptions, e.g., 4xx errors
            return response()->json(['error' => 'Client error: ' . $e->getMessage()], 400);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle server exceptions, e.g., 5xx errors
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // For send One Messages End //



    public function listMessages(Request $request)
    {
        if($request->type == 'inbox_all'){
            $result = $this->all_inbox($request);
            return $result;
        }elseif($request->type == 'single_inbox_message'){
            $result = $this->single_inbox_message($request);
            return $result;
        }elseif($request->type == 'spam_all'){
            $result = $this->all_spam($request);
            return $result;
        }elseif($request->type == 'single_spam_message'){
            $result = $this->single_spam_message($request);
            return $result;
        }elseif($request->type == 'trash_all'){
            $result = $this->all_trash($request);
            return $result;
        }elseif($request->type == 'single_trash_message'){
            $result = $this->single_trash_message($request);
            return $result;
        }elseif($request->type == 'send_all'){
            $result = $this->all_send($request);
            return $result;
        }elseif($request->type == 'single_send_message'){
            $result = $this->single_send_message($request);
            return $result;
        }
       
    }
}