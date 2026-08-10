<?php
/**
 * AI Department Assistant Chatbot Component
 * SRKREC CSD & CSIT Departments
 */
?>
<!-- Chatbot Stylesheet -->
<link rel="stylesheet" href="assets/css/chatbot.css">

<!-- Floating Trigger Button -->
<button id="chatbotTriggerBtn" class="chatbot-trigger-btn" aria-label="Open Department Assistant Chat" aria-expanded="false" title="Chat with Department Assistant">
    <span class="chatbot-badge"></span>
    <i class="fas fa-robot icon-chat"></i>
    <i class="fas fa-times icon-close"></i>
</button>

<!-- Floating Chat Window -->
<div id="chatbotWindow" class="chatbot-window" role="dialog" aria-label="Department Assistant Chat Window">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chatbot-title-group">
                <h5>Department Assistant</h5>
                <div class="chatbot-status">
                    <span class="status-dot"></span> Online • Here to help
                </div>
            </div>
        </div>
        <div class="chatbot-header-actions">
            <button id="chatbotClearBtn" class="chatbot-action-btn" title="Clear Chat History" aria-label="Clear Chat History">
                <i class="fas fa-rotate-right"></i>
            </button>
            <button id="chatbotCloseBtn" class="chatbot-action-btn" title="Close Chat Window" aria-label="Close Chat Window">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Conversation Container -->
    <div id="chatbotBody" class="chatbot-body">
        <!-- Initial Welcome Message & Quick Question Pills will be dynamically rendered -->
    </div>

    <!-- Footer Input Bar -->
    <div class="chatbot-footer">
        <form id="chatbotForm" class="chatbot-input-form" onsubmit="return false;">
            <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Ask about courses, faculty, admissions..." aria-label="Ask a question to Department Assistant" autocomplete="off">
            <button type="button" id="chatbotSendBtn" class="chatbot-send-btn" disabled aria-label="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        <div class="chatbot-disclaimer">⚡ Powered by SRKREC CSD-CSIT AI</div>
    </div>
</div>

<!-- Chatbot JavaScript Services -->
<script src="assets/js/chatbotService.js"></script>
<script src="assets/js/chatbotUI.js"></script>
