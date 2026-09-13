"use strict";

(function () {

    /* =========================================================
       CONFIGURATION
       ========================================================= */

    const config = window.AI_HR_CONFIG || {};

    const MAX_MESSAGE_LENGTH =
        Number(config.maxMessageLength) || 2000;

    const CSRF_TOKEN =
        typeof config.csrfToken === "string"
            ? config.csrfToken
            : "";

    


    /* =========================================================
       STATE
       ========================================================= */

    let sessionId = null;
    let isSending = false;


    /* =========================================================
       DOM ELEMENTS
       ========================================================= */

    const input = $("#chatInput");
    const messages = $("#chatMessages");
    const sendButton = $("#sendMessage");
    const typingIndicator = $("#typingIndicator");
    const characterCounter = $("#characterCounter");


    /* =========================================================
       CREATE SESSION
       ========================================================= */

    function createSession() {

        try {

            if (
                typeof crypto !== "undefined" &&
                typeof crypto.randomUUID === "function"
            ) {

                sessionId = crypto.randomUUID();

            } else {

                sessionId =
                    Date.now().toString(36) +
                    "-" +
                    Math.random()
                        .toString(36)
                        .substring(2, 12);

            }

        } catch (error) {

            console.error(
                "Unable to create chat session."
            );

            sessionId = null;
        }
    }

    createSession();


    /* =========================================================
       VALIDATE MESSAGE
       ========================================================= */

    function validateMessage(message) {

        if (!message) {

            showChatError(
                "Please enter a message."
            );

            return false;
        }


        if (message.length > MAX_MESSAGE_LENGTH) {

            showChatError(
                `Message cannot exceed ${MAX_MESSAGE_LENGTH} characters.`
            );

            return false;
        }


        if (!sessionId) {

            showChatError(
                "Chat session could not be created. Please refresh the page."
            );

            return false;
        }


        if (!CSRF_TOKEN) {

            console.error(
                "CSRF token is missing."
            );

            showChatError(
                "Security validation failed. Please refresh the page."
            );

            return false;
        }


        return true;
    }


    /* =========================================================
       SEND MESSAGE
       ========================================================= */

    function sendMessage() {

        if (isSending) {
            return;
        }


        const message =
            String(input.val() || "").trim();


        if (!validateMessage(message)) {
            return;
        }


        /* Add user message */
        removeInitialWelcomeMessage();
        addUserMessage(message);


        /* Clear input */

        input.val("");

        updateCharacterCounter();
        autoResizeTextarea();


        /* Sending state */

        isSending = true;

        setSendingState(true);

        showTypingIndicator();


        /* =====================================================
           AJAX REQUEST
           ===================================================== */

        $.ajax({

            url: "../../ajax/employee_chat.php",

            method: "POST",

            dataType: "json",

            timeout: 60000,

            data: {

                session_id: sessionId,

                message: message,

                csrf_token: CSRF_TOKEN

            },


            /* =================================================
               SUCCESS
               ================================================= */

            success: function (response) {

                if (
                    response &&
                    response.success === true
                ) {

                    const reply =
                        typeof response.reply === "string" &&
                        response.reply.trim() !== ""

                            ? response.reply

                            : "No response received.";

                    addAIMessage(reply);

                } else {

                    const errorMessage =
                        response &&
                        typeof response.message === "string"

                            ? response.message

                            : "Something went wrong. Please try again.";

                    addAIMessage(errorMessage);
                }
            },


            /* =================================================
               ERROR
               ================================================= */

            error: function (xhr, status, error) {

                console.error(
                    "Chat request failed:",
                    status,
                    error
                );


                let errorMessage =
                    "Unable to connect to the AI service. Please try again.";


                /* Server JSON message */

                if (
                    xhr.responseJSON &&
                    typeof xhr.responseJSON.message === "string"
                ) {

                    errorMessage =
                        xhr.responseJSON.message;
                }


                /* Timeout */

                if (status === "timeout") {

                    errorMessage =
                        "The AI service took too long to respond. Please try again.";
                }


                /* Forbidden / CSRF */

                if (xhr.status === 403) {

                    errorMessage =
                        "Security validation failed. Please refresh the page and try again.";
                }


                /* Rate limit */

                if (xhr.status === 429) {
                    addAIMessage(
                        "Your AI chat limit is over for today. Please come tomorrow.",
                        true
                    );

                    return;
                }


                /* Unauthorized */

                if (xhr.status === 401) {

                    errorMessage =
                        "Your session has expired. Please log in again.";
                }


                addAIMessage(errorMessage);
            },


            /* =================================================
               COMPLETE
               ================================================= */

            complete: function () {

                isSending = false;

                setSendingState(false);

                hideTypingIndicator();

                input.focus();
            }

        });
    }


    /* =========================================================
       SET SENDING STATE
       ========================================================= */

    function setSendingState(sending) {

        if (sending) {

            sendButton
                .prop("disabled", true)
                .html(
                    '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>' +
                    '<span class="visually-hidden">Sending...</span>'
                );

        } else {

            sendButton
                .prop("disabled", false)
                .html(
                    '<i class="bi bi-arrow-up" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Send</span>'
                );
        }
    }


    /* =========================================================
       ADD USER MESSAGE
       ========================================================= */

    function addUserMessage(message) {

        const safeMessage =
            escapeHtml(message);


        const messageHtml = `

            <div class="chat-message user-message mb-3">

                <div class="chat-message-content">

                    <div class="chat-message-name">
                        You
                    </div>


                    <div class="chat-message-bubble">

                        <div class="chat-message-text">
                            ${safeMessage}
                        </div>

                    </div>


                    <div class="chat-message-time">
                        Just now
                    </div>

                </div>

            </div>

        `;


        messages.append(messageHtml);

        scrollChatToBottom();
    }


    /* =========================================================
       ADD AI MESSAGE
       ========================================================= */

    function addAIMessage(message, isLimitMessage = false) {

        const safeMessage =
            escapeHtml(String(message));


        const messageHtml = `

            <div class="chat-message ai-message mb-3">


                <!-- AI Avatar -->

                <div
                    class="chat-avatar"
                    aria-hidden="true"
                >
                    <i class="bi bi-robot"></i>
                </div>


                <!-- Content -->

                <div class="chat-message-content">

                    <div class="chat-message-name">
                        AI HR Assistant
                    </div>


                    <div class="chat-message-bubble">

                        <div class="chat-message-text ${isLimitMessage ? "chat-limit-message" : ""}">
                            ${safeMessage}
                        </div>

                    </div>


                    <div class="chat-message-time">
                        Just now
                    </div>

                </div>

            </div>

        `;


        messages.append(messageHtml);

        scrollChatToBottom();
    }

    /* =========================================================
    REMOVE INITIAL WELCOME MESSAGE
    ========================================================= */

    function removeInitialWelcomeMessage() {

        const welcomeMessage =
            $("#initialWelcomeMessage");

        if (!welcomeMessage.length) {
            return;
        }

        welcomeMessage.addClass("welcome-message-hide");

        setTimeout(function () {

            welcomeMessage.remove();

        }, 180);
    }


    /* =========================================================
       ERROR
       ========================================================= */

    function showChatError(message) {

        addAIMessage(message);
    }


    /* =========================================================
       ESCAPE HTML
       ========================================================= */

    function escapeHtml(value) {

        return $("<div>")
            .text(value)
            .html();
    }


    /* =========================================================
       SCROLL CHAT
       ========================================================= */

    function scrollChatToBottom() {

        const element =
            messages[0];

        if (!element) {
            return;
        }


        requestAnimationFrame(function () {

            element.scrollTop =
                element.scrollHeight;

        });
    }


    /* =========================================================
       TYPING INDICATOR
       ========================================================= */

    function showTypingIndicator() {

        typingIndicator
            .removeClass("d-none");

        scrollChatToBottom();
    }


    function hideTypingIndicator() {

        typingIndicator
            .addClass("d-none");
    }


    /* =========================================================
       CHARACTER COUNTER
       ========================================================= */

    function updateCharacterCounter() {

        const length =
            String(input.val() || "").length;


        characterCounter.text(
            `${length} / ${MAX_MESSAGE_LENGTH}`
        );


        if (
            length >=
            MAX_MESSAGE_LENGTH * 0.9
        ) {

            characterCounter
                .removeClass("text-muted")
                .addClass("text-danger");

        } else {

            characterCounter
                .removeClass("text-danger")
                .addClass("text-muted");
        }
    }


    /* =========================================================
       AUTO RESIZE TEXTAREA
       ========================================================= */

    function autoResizeTextarea() {

        const element =
            input[0];

        if (!element) {
            return;
        }


        element.style.height = "auto";


        const maxHeight = 110;


        element.style.height =
            Math.min(
                element.scrollHeight,
                maxHeight
            ) + "px";
    }


    /* =========================================================
       SUGGESTION BUTTONS
       ========================================================= */

    $(document).on(
        "click",
        ".suggestion-chip",
        function () {

            if (isSending) {
                return;
            }


            const message =
                $(this).data("message");


            if (!message) {
                return;
            }


            input.val(message);

            updateCharacterCounter();

            autoResizeTextarea();

            input.focus();

            sendMessage();
        }
    );


    /* =========================================================
       SEND BUTTON
       ========================================================= */

    sendButton.on(
        "click",
        function () {

            sendMessage();

        }
    );


    /* =========================================================
       ENTER KEY
       ========================================================= */

    input.on(
        "keydown",
        function (event) {

            /*
             * Enter = Send
             * Shift + Enter = New line
             */

            if (
                event.key === "Enter" &&
                !event.shiftKey
            ) {

                event.preventDefault();

                sendMessage();
            }
        }
    );


    /* =========================================================
       INPUT EVENT
       ========================================================= */

    input.on(
        "input",
        function () {

            updateCharacterCounter();

            autoResizeTextarea();
        }
    );


    /* =========================================================
       INITIALIZE
       ========================================================= */

    updateCharacterCounter();

    autoResizeTextarea();

    input.focus();

})();