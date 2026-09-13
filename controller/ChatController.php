<?php

class ChatController extends Controller
{
    private PDO $db;

    private Employee $employeeModel;
    private User $userModel;
    private Leave $leaveModel;
    private Attendance $attendanceModel;
    private Department $departmentModel;
    private Designation $designationModel;


    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->employeeModel =
            new Employee($this->db);

        $this->userModel =
            new User($this->db);

        $this->leaveModel =
            new Leave($this->db);

        $this->attendanceModel =
            new Attendance($this->db);

        $this->departmentModel =
            new Department($this->db);

        $this->designationModel =
            new Designation($this->db);
    }


    /*
    |--------------------------------------------------------------------------
    | Store Chat
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Get Request Data
        |--------------------------------------------------------------------------
        */

        $data = [

            'message' => trim(
                $this->input('message', '')
            ),

            'session_id' => trim(
                $this->input('session_id', '')
            )

        ];


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validator = new Validator($data);

        $validator
            ->required('message')
            ->required('session_id');


        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' => 'Validation failed',

                'errors' => $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Message Length
        |--------------------------------------------------------------------------
        */

        if (mb_strlen($data['message']) > 2000) {

            $this->json([
                'success' => false,
                'message' => 'Message is too long.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Session Length
        |--------------------------------------------------------------------------
        */

        if (mb_strlen($data['session_id']) > 100) {

            $this->json([
                'success' => false,
                'message' => 'Invalid session.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Session Format
        |--------------------------------------------------------------------------
        */

        if (
            !preg_match(
                '/^[a-f0-9-]{36}$/i',
                $data['session_id']
            )
        ) {

            $this->json([
                'success' => false,
                'message' => 'Invalid session.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Logged-in Employee
        |--------------------------------------------------------------------------
        */

        $employeeId = (int) Auth::emp_id();


        if ($employeeId <= 0) {

            $this->json([

                'success' => false,

                'message' =>
                    'Employee information not found.'

            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Bind Chat Session To Logged-in Employee
        |--------------------------------------------------------------------------
        */

        if (!isset($_SESSION['ai_chat_sessions'])) {

            $_SESSION['ai_chat_sessions'] = [];
        }


        $sessionId = $data['session_id'];


        /*
        |--------------------------------------------------------------------------
        | Check Existing Session Binding
        |--------------------------------------------------------------------------
        */

        if (
            isset($_SESSION['ai_chat_sessions'][$sessionId])
        ) {

            $boundEmployeeId =
                (int) $_SESSION['ai_chat_sessions'][$sessionId];


            if ($boundEmployeeId !== $employeeId) {

                error_log(
                    "AI CHAT SESSION OWNERSHIP MISMATCH"
                );

                $this->json([
                    'success' => false,
                    'message' => 'Invalid chat session.'
                ], 403);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Bind New Chat Session
        |--------------------------------------------------------------------------
        */

        $_SESSION['ai_chat_sessions'][$sessionId] =
            $employeeId;


        if (
            !$this->employeeModel->consumeChatRequest(
                $employeeId
            )
        ) {

            error_log(
                "AI CHAT REQUEST LIMIT REACHED | " .
                "EMPLOYEE ID: " .
                $employeeId
            );

            $this->json([

                'success' => false,

                'message' =>
                    'You have reached your AI chat request limit.'

            ], 429);
        }


        /*
        |--------------------------------------------------------------------------
        | Python AI Service URL
        |--------------------------------------------------------------------------
        */

        $pythonUrl =env('PYTHON_URL');



        /*
        |--------------------------------------------------------------------------
        | AI Service Key
        |--------------------------------------------------------------------------
        */

        $aiServiceKey = env('AI_SERVICE_KEY');


        if (empty($aiServiceKey)) {

            error_log(
                "AI SERVICE KEY MISSING"
            );

            $this->json([

                'success' => false,

                'message' =>
                    'Unable to process your request. Please try again.'

            ], 500);
        }


        /*
        |--------------------------------------------------------------------------
        | Payload
        |--------------------------------------------------------------------------
        */

        $payload = [

            'session_id' =>
                $data['session_id'],

            'question' =>
                $data['message'],

            'employee_id' =>
                $employeeId

        ];


        /*
        |--------------------------------------------------------------------------
        | Convert Payload To JSON
        |--------------------------------------------------------------------------
        */

        $jsonPayload = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
        );


        if ($jsonPayload === false) {

            error_log(
                "AI JSON ERROR: " .
                json_last_error_msg()
            );

            $this->json([

                'success' => false,

                'message' =>
                    'Unable to process your request. Please try again.'

            ], 500);
        }


        /*
        |--------------------------------------------------------------------------
        | Start AI Request
        |--------------------------------------------------------------------------
        */

        error_log(
            "================================"
        );

        error_log(
            "AI REQUEST START: " .
            date('H:i:s')
        );

        error_log(
            "AI PYTHON URL: " .
            $pythonUrl
        );

        // error_log(
        //     "AI EMPLOYEE ID: " .
        //     $employeeId
        // );

        // error_log(
        //     "AI SESSION ID: " .
        //     $data['session_id']
        // );


        $startTime = microtime(true);


        /*
        |--------------------------------------------------------------------------
        | cURL
        |--------------------------------------------------------------------------
        */

        $ch = curl_init(
            $pythonUrl
        );


        curl_setopt_array($ch, [

            CURLOPT_POST => true,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_HTTPHEADER => [

                'Content-Type: application/json',

                'Accept: application/json',

                'X-AI-SERVICE-KEY: ' .
                    $aiServiceKey

            ],

            CURLOPT_POSTFIELDS =>
                $jsonPayload,

            CURLOPT_CONNECTTIMEOUT => 10,

            CURLOPT_TIMEOUT => 120,

            CURLOPT_NOPROXY =>
                env('DB_LOCAL').','.env('DB_HOST')

        ]);


        /*
        |--------------------------------------------------------------------------
        | Execute Request
        |--------------------------------------------------------------------------
        */

        $response = curl_exec($ch);


        /*
        |--------------------------------------------------------------------------
        | Get cURL Information BEFORE Closing
        |--------------------------------------------------------------------------
        */

        $elapsed = round(
            microtime(true) - $startTime,
            2
        );


        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


        $curlError = curl_error($ch);


        $curlErrorNumber =
            curl_errno($ch);


        /*
        |--------------------------------------------------------------------------
        | Close cURL
        |--------------------------------------------------------------------------
        */

        curl_close($ch);


        /*
        |--------------------------------------------------------------------------
        | Log AI Request
        |--------------------------------------------------------------------------
        */

        error_log(
            "AI REQUEST END: " .
            date('H:i:s')
        );

        error_log(
            "AI REQUEST TIME: " .
            $elapsed .
            " seconds"
        );

        error_log(
            "AI HTTP CODE: " .
            $httpCode
        );

        error_log(
            "AI CURL ERROR NUMBER: " .
            $curlErrorNumber
        );


        /*
        |--------------------------------------------------------------------------
        | Log cURL Error
        |--------------------------------------------------------------------------
        */

        if (!empty($curlError)) {

            error_log(
                "AI CURL ERROR: " .
                $curlError
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Log Response
        |--------------------------------------------------------------------------
        */

        if ($response !== false) {

            error_log(
                "AI RESPONSE LENGTH: " .
                strlen($response)
            );

            /*
             * Keep the raw response only for debugging.
             * Do NOT return it to the browser.
             */
            // error_log(
            //     "AI RESPONSE: " .
            //     $response
            // );
        }


        error_log(
            "================================"
        );


        /*
        |--------------------------------------------------------------------------
        | Python Connection Failed
        |--------------------------------------------------------------------------
        */

        if ($response === false) {

            /*
             * Detailed error stays in server logs.
             */

            error_log(
                "AI SERVICE CONNECTION FAILED: " .
                $curlError .
                " | CODE: " .
                $curlErrorNumber
            );


            /*
             * Generic error goes to browser.
             */

            $this->json([

                'success' => false,

                'message' =>
                    'Unable to connect to the AI service. Please try again.'

            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | Decode Python Response
        |--------------------------------------------------------------------------
        */

        $pythonResponse = json_decode(
            $response,
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Invalid JSON
        |--------------------------------------------------------------------------
        */

        if (!is_array($pythonResponse)) {

            error_log(
                "AI INVALID JSON: " .
                json_last_error_msg()
            );

            error_log(
                "AI RAW RESPONSE: " .
                $response
            );


            $this->json([

                'success' => false,

                'message' =>
                    'Invalid response from AI service.'

            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | Python Returned Error
        |--------------------------------------------------------------------------
        */

        if (
            $httpCode >= 400 ||
            !($pythonResponse['success'] ?? false)
        ) {

            /*
             * IMPORTANT:
             *
             * Do NOT return $pythonResponse
             * directly to the browser.
             */

            error_log(
                "AI SERVICE RETURNED ERROR: " .
                $response
            );


            $this->json([

                'success' => false,

                'message' =>
                    'Unable to process your request. Please try again.'

            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Answer
        |--------------------------------------------------------------------------
        */

        $answer =
            $pythonResponse['answer']
            ?? null;


        if (!is_string($answer)) {

            error_log(
                "AI RESPONSE MISSING ANSWER"
            );

            $this->json([

                'success' => false,

                'message' =>
                    'Invalid response from AI service.'

            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | Final AI Response
        |--------------------------------------------------------------------------
        */

        $this->json([

            'success' => true,

            'session_id' =>
                $pythonResponse['session_id']
                ?? $data['session_id'],

            'reply' =>
                $answer

        ]);
    }
}