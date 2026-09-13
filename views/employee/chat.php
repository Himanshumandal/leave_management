<?php

Auth::requireLogin();

$pageTitle = 'Employee Dashboard';


/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/

// if (empty($_SESSION['csrf_token'])) {

//     $_SESSION['csrf_token'] =
//         bin2hex(random_bytes(32));
// }

// $csrfToken =
//     $_SESSION['csrf_token'];


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidenav.php';

?>


<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="container-fluid py-3">

        <div class="row justify-content-center">

            <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">


                <!-- =====================================================
                     AI HR ASSISTANT
                     ===================================================== -->

                <div
                    class="card ai-assistant-widget border shadow-sm rounded-3 overflow-hidden"
                >


                    <!-- =================================================
                         HEADER
                         ================================================= -->

                    <div class="card-header bg-white border-bottom py-3 px-3">

                        <div class="d-flex align-items-center justify-content-between">


                            <!-- Assistant -->

                            <div class="d-flex align-items-center">

                                <div
                                    class="bg-primary-subtle text-primary rounded-3
                                           d-flex align-items-center justify-content-center
                                           flex-shrink-0 me-2"
                                    style="width:40px;height:40px;"
                                >

                                    <i
                                        class="bi bi-robot fs-5"
                                        aria-hidden="true"
                                    ></i>

                                </div>


                                <div>

                                    <h6 class="mb-0 fw-semibold">
                                        AI HR Assistant
                                    </h6>


                                    <div class="d-flex align-items-center gap-1 mt-1">

                                        <span
                                            class="bg-success rounded-circle"
                                            style="width:6px;height:6px;"
                                            aria-hidden="true"
                                        ></span>


                                        <small
                                            class="text-muted"
                                            style="font-size:11px;"
                                        >
                                            Online
                                        </small>


                                        <span
                                            class="text-muted"
                                            style="font-size:11px;"
                                        >
                                            ·
                                        </span>


                                        <small
                                            class="text-muted"
                                            style="font-size:11px;"
                                        >
                                            HR Assistant
                                        </small>

                                    </div>

                                </div>

                            </div>


                            <!-- Security -->

                            <span
                                class="badge bg-success-subtle text-success
                                       rounded-pill fw-normal px-2 py-1"
                                style="font-size:10px;"
                            >

                                <i
                                    class="bi bi-shield-check me-1"
                                    aria-hidden="true"
                                ></i>

                                Secure

                            </span>


                        </div>

                    </div>


                    <!-- =================================================
                         CHAT MESSAGES
                         ================================================= -->

                    <div
                        id="chatMessages"
                        class="chat-messages px-3 py-3"
                        role="log"
                        aria-live="polite"
                        aria-label="AI HR Assistant conversation"
                    >


                        <!-- =================================================
                             INITIAL AI MESSAGE
                             ================================================= -->

                       


                    


                                <!-- =================================================
                                INITIAL WELCOME MESSAGE
                                ================================================= -->

                            <div id="initialWelcomeMessage" class="chat-message ai-message mb-3">

                                <!-- Assistant Avatar -->

                                <div
                                    class="chat-avatar"
                                    aria-hidden="true"
                                >
                                    <i class="bi bi-robot"></i>
                                </div>


                                <!-- Message Content -->

                                <div class="chat-message-content">

                                    <div class="chat-message-name">
                                        AI HR Assistant
                                    </div>


                                    <div class="chat-message-bubble welcome-bubble">

                                        <!-- Greeting -->

                                        <div class="d-flex align-items-center gap-2 mb-2">

                                            <span class="welcome-icon">
                                                <i class="bi bi-stars"></i>
                                            </span>

                                            <span class="fw-semibold">
                                                Hello! How can I help you today?
                                            </span>

                                        </div>


                                        <div class="text-secondary mb-3">
                                            I can help you quickly find information about
                                            your leave, attendance, HR policies and other
                                            workplace-related topics.
                                        </div>


                                        <!-- Suggestions Heading -->

                                        <div class="suggestion-heading">
                                            <i class="bi bi-lightning-charge me-1"></i>
                                            Quick questions
                                        </div>


                                        <!-- Suggestions -->

                                        <div class="d-flex flex-wrap gap-2">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light border suggestion-chip text-start"
                                                data-message="What is my current leave balance?"
                                            >
                                                <i class="bi bi-calendar-check text-primary me-1"></i>
                                                Leave balance
                                            </button>


                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light border suggestion-chip text-start"
                                                data-message="What is the leave policy?"
                                            >
                                                <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                                Leave policy
                                            </button>


                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light border suggestion-chip text-start"
                                                data-message="Show me my attendance summary."
                                            >
                                                <i class="bi bi-clock-history text-primary me-1"></i>
                                                Attendance
                                            </button>


                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light border suggestion-chip text-start"
                                                data-message="What HR policies are available?"
                                            >
                                                <i class="bi bi-building text-primary me-1"></i>
                                                HR policies
                                            </button>

                                        </div>

                                    </div>


                                    <div class="chat-message-time">
                                        Just now
                                    </div>

                                </div>

                            </div>

                        

                    </div>


                    <!-- =================================================
                         TYPING INDICATOR
                         ================================================= -->

                    <div
                        id="typingIndicator"
                        class="d-none px-3 pb-2"
                        aria-live="polite"
                    >

                        <div class="d-flex align-items-center gap-2">


                            <div
                                class="chat-avatar chat-avatar-sm"
                                aria-hidden="true"
                            >

                                <i class="bi bi-robot"></i>

                            </div>


                            <div
                                class="bg-light border rounded-pill px-3 py-2"
                            >

                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>

                            </div>


                            <small
                                class="text-muted"
                                style="font-size:11px;"
                            >
                                Thinking...
                            </small>


                        </div>

                    </div>


                    <!-- =================================================
                         INPUT
                         ================================================= -->

                    <div class="card-footer bg-white border-top p-2">


                        <div class="input-group">


                            <textarea
                                id="chatInput"
                                class="form-control chat-input border-end-0"
                                rows="1"
                                maxlength="2000"
                                placeholder="Ask about leave, attendance, HR policies..."
                                autocomplete="off"
                                aria-label="Ask AI HR Assistant"
                            ></textarea>


                            <button
                                type="button"
                                id="sendMessage"
                                class="btn btn-primary px-3"
                                title="Send message"
                                aria-label="Send message"
                            >

                                <i
                                    class="bi bi-arrow-up"
                                    aria-hidden="true"
                                ></i>

                                <span class="visually-hidden">
                                    Send
                                </span>

                            </button>


                        </div>


                        <!-- Input information -->

                        <div
                            class="d-flex justify-content-between
                                   align-items-center mt-1 px-1"
                        >


                            <small
                                class="text-muted"
                                style="font-size:10px;"
                            >

                                <i
                                    class="bi bi-lock me-1"
                                    aria-hidden="true"
                                ></i>

                                Secure conversation

                            </small>


                            <small
                                id="characterCounter"
                                class="text-muted"
                                style="font-size:10px;"
                            >
                                0 / 2000
                            </small>


                        </div>

                    </div>


                </div>


                <!-- =====================================================
                     DISCLAIMER
                     ===================================================== -->

                <div class="text-center text-muted mt-2">

                    <small style="font-size:10px;">

                        <i
                            class="bi bi-info-circle me-1"
                            aria-hidden="true"
                        ></i>

                        AI responses may not always be accurate.
                        Contact HR for official decisions.

                    </small>

                </div>


            </div>

        </div>

    </div>

</main>


<!-- =============================================================
     AI HR CONFIG
     ============================================================= -->


<script>
    window.AI_HR_CONFIG = {
        csrfToken: <?= json_encode(Auth::csrfToken()) ?>,
        maxMessageLength: 2000
    };
</script>




<!-- =============================================================
     JQUERY
     ============================================================= -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>



<!-- =============================================================
     ASSISTANT JS
     ============================================================= -->

<script
    src="../../public/js/assistant.js"
></script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>