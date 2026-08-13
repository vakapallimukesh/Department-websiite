/**
 * AI Department Assistant — Conversational UI Controller & Interaction Handler
 * SRKREC CSD & CSIT Departments
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'srkrec_dept_chat_history_v2';
    let chatHistory = [];
    let isProcessing = false;

    // DOM Elements
    let triggerBtn, windowEl, closeBtn, clearBtn, bodyEl, inputEl, sendBtn, typingEl;

    function initChatbotUI() {
        triggerBtn = document.getElementById('chatbotTriggerBtn');
        windowEl = document.getElementById('chatbotWindow');
        closeBtn = document.getElementById('chatbotCloseBtn');
        clearBtn = document.getElementById('chatbotClearBtn');
        bodyEl = document.getElementById('chatbotBody');
        inputEl = document.getElementById('chatbotInput');
        sendBtn = document.getElementById('chatbotSendBtn');
        typingEl = document.getElementById('chatbotTypingIndicator');

        if (!triggerBtn || !windowEl) return;

        const formEl = document.getElementById('chatbotForm');
        let isSendBtnDisabled = true;

        // Event Listeners
        triggerBtn.addEventListener('click', toggleChatWindow);
        closeBtn.addEventListener('click', closeChatWindow);
        clearBtn.addEventListener('click', clearChatHistory);

        sendBtn.addEventListener('click', handleSendMessage);

        if (formEl) {
            formEl.addEventListener('submit', function (e) {
                e.preventDefault();
                handleSendMessage();
            });
        }

        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSendMessage();
            }
        });

        // Fast & Isolated Input Handler (Only mutates DOM when disabled state actually flips)
        inputEl.addEventListener('input', function () {
            const hasText = inputEl.value.trim().length > 0;
            const shouldDisable = !hasText;
            if (isSendBtnDisabled !== shouldDisable) {
                isSendBtnDisabled = shouldDisable;
                sendBtn.disabled = shouldDisable;
            }
        });

        // Keyboard accessibility: Escape key closes chat window
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && windowEl && windowEl.classList.contains('active')) {
                closeChatWindow();
            }
        });

        // Load saved chat session or render initial state
        loadChatHistory();
    }

    function toggleChatWindow() {
        const isActive = windowEl.classList.contains('active');
        if (isActive) {
            closeChatWindow();
        } else {
            openChatWindow();
        }
    }

    function openChatWindow() {
        windowEl.classList.add('active');
        triggerBtn.classList.add('active');
        triggerBtn.setAttribute('aria-expanded', 'true');
        if (!bodyEl.children || bodyEl.children.length === 0) {
            renderWelcomeMessage();
        }
        setTimeout(() => inputEl.focus(), 300);
        scrollToBottom();
    }

    function closeChatWindow() {
        windowEl.classList.remove('active');
        triggerBtn.classList.remove('active');
        triggerBtn.setAttribute('aria-expanded', 'false');
    }

    function getTimeString() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function renderWelcomeMessage() {
        bodyEl.innerHTML = '';

        const welcomeHTML = `
            <div class="chat-message bot">
                <div class="msg-bubble">
                    <div class="chatbot-welcome-card">
                        <div class="mini-orbit-avatar welcome-avatar">
                            <svg class="mini-orbit-svg" viewBox="0 0 100 100" fill="none">
                                <circle cx="50" cy="50" r="42" stroke="url(#ringGrad1)" stroke-width="1.5" opacity="0.5" />
                                <circle cx="50" cy="50" r="32" stroke="url(#ringGrad2)" stroke-dasharray="25 10" stroke-width="1" opacity="0.6" />
                                <circle cx="50" cy="18" r="2.5" fill="#F3D49A" />
                            </svg>
                            <div class="mini-glass-core">AI</div>
                        </div>
                        <div class="chatbot-welcome-header-text">
                            <span class="chatbot-brand-tag">CSD &amp; CSIT DEPARTMENT</span>
                            <h6>DEPARTMENT AI</h6>
                        </div>
                    </div>
                    <p>👋 <strong>Welcome! I am your Department AI Assistant.</strong></p>
                    <p>Ask me anything about CSD &amp; CSIT courses, faculty, admissions, placements, events, facilities, or startups. How can I help you today?</p>
                    <div class="quick-questions-container">
                        <button class="quick-btn" data-query="Who are the faculty members?">👨‍🏫 Who are the faculty members?</button>
                        <button class="quick-btn" data-query="What courses does the department offer?">📚 What courses are offered?</button>
                        <button class="quick-btn" data-query="What are the placement opportunities?">💼 Placement opportunities</button>
                        <button class="quick-btn" data-query="Tell me about the department.">🎓 About the Department</button>
                        <button class="quick-btn" data-query="How to apply for admission?">📝 Admissions guide</button>
                        <button class="quick-btn" data-query="Department facilities and labs">🏫 Facilities and labs</button>
                        <button class="quick-btn" data-query="Department events and workshops">📅 Events &amp; Workshops</button>
                        <button class="quick-btn" data-query="Tell me about startups and incubation">🚀 Startups &amp; Incubation</button>
                    </div>
                </div>
                <div class="msg-timestamp">${getTimeString()}</div>
            </div>
            <div class="chat-message bot typing-indicator-msg" id="chatbotTypingIndicator">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                    <span class="typing-text">Department Assistant is typing...</span>
                </div>
            </div>
        `;

        bodyEl.innerHTML = welcomeHTML;
        typingEl = document.getElementById('chatbotTypingIndicator');
        bindQuickButtons();
        scrollToBottom();
    }

    function bindQuickButtons() {
        const btns = bodyEl.querySelectorAll('.quick-btn, .followup-chip');
        btns.forEach(btn => {
            btn.addEventListener('click', function () {
                const query = this.getAttribute('data-query');
                if (query && !isProcessing) {
                    processUserMessage(query);
                }
            });
        });
    }

    function handleSendMessage() {
        const text = inputEl.value.trim();
        if (!text || isProcessing) return;

        inputEl.value = '';
        sendBtn.disabled = true;
        processUserMessage(text);
    }

    async function processUserMessage(userInput) {
        isProcessing = true;
        console.log('[CHATBOT] User message:', userInput);

        // 1. Append & Render User Message
        appendMessage('user', userInput);
        saveState('user', userInput);

        // 2. Display Typing Indicator
        showTyping(true);
        scrollToBottom();

        try {
            console.log('[CHATBOT] Calling AI service...');
            const botData = await ChatbotService.getBotResponse(userInput);
            console.log('[CHATBOT] AI response received:', botData);
            
            showTyping(false);

            // 4. Append & Render Bot Response
            console.log('[CHATBOT] Rendering response...');
            appendBotMessage(botData);
            saveState('bot', botData);
        } catch (err) {
            console.error('[CHATBOT ERROR]', err);
            showTyping(false);
            const fallbackData = {
                answer: "I'm sorry, I couldn't find that information on the department website. You can contact the department for the latest details.",
                ctaLinks: [{ text: 'Contact Department →', url: 'footer.php' }],
                suggestions: ['What courses are offered?', 'Who is the HOD?']
            };
            appendBotMessage(fallbackData);
        } finally {
            isProcessing = false;
            sendBtn.disabled = false;
            inputEl.focus();
            scrollToBottom();
        }
    }

    function appendMessage(sender, text) {
        const time = getTimeString();
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${sender}`;

        msgDiv.innerHTML = `
            <div class="msg-bubble">${escapeHTML(text)}</div>
            <div class="msg-timestamp">${time}</div>
        `;

        if (typingEl && typingEl.parentNode === bodyEl) {
            bodyEl.insertBefore(msgDiv, typingEl);
        } else {
            bodyEl.appendChild(msgDiv);
        }
        scrollToBottom();
    }

    function appendBotMessage(data) {
        const time = getTimeString();
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message bot';

        let ctaHTML = '';
        if (data.ctaLinks && data.ctaLinks.length > 0) {
            ctaHTML = '<div class="chat-cta-container">';
            data.ctaLinks.forEach(link => {
                const isExternal = link.url.startsWith('http');
                const target = isExternal ? 'target="_blank" rel="noopener noreferrer"' : '';
                ctaHTML += `<a href="${link.url}" class="chat-cta-btn" ${target}>${escapeHTML(link.text)}</a>`;
            });
            ctaHTML += '</div>';
        }

        let suggestionsHTML = '';
        if (data.suggestions && data.suggestions.length > 0) {
            suggestionsHTML = '<div class="followup-suggestions">';
            suggestionsHTML += '<div class="followup-title">Suggested Questions:</div>';
            data.suggestions.forEach(sug => {
                suggestionsHTML += `<button class="followup-chip" data-query="${escapeHTML(sug)}">${escapeHTML(sug)}</button>`;
            });
            suggestionsHTML += '</div>';
        }

        msgDiv.innerHTML = `
            <div class="msg-bubble">
                ${data.answer}
                ${ctaHTML}
                ${suggestionsHTML}
            </div>
            <div class="msg-timestamp">${time}</div>
        `;

        if (typingEl && typingEl.parentNode === bodyEl) {
            bodyEl.insertBefore(msgDiv, typingEl);
        } else {
            bodyEl.appendChild(msgDiv);
        }

        bindQuickButtons();
        scrollToBottom();
    }

    function showTyping(show) {
        if (!typingEl) return;
        if (show) {
            typingEl.classList.add('active');
            bodyEl.appendChild(typingEl);
        } else {
            typingEl.classList.remove('active');
        }
    }

    function scrollToBottom() {
        setTimeout(() => {
            bodyEl.scrollTop = bodyEl.scrollHeight;
        }, 50);
    }

    function escapeHTML(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function saveState(sender, content) {
        chatHistory.push({ sender: sender, content: content, time: getTimeString() });
        if (chatHistory.length > 30) {
            chatHistory.shift();
        }
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));
        } catch (e) {
            console.warn('Unable to save chat history to localStorage:', e);
        }
    }

    function loadChatHistory() {
        renderWelcomeMessage();

        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    chatHistory = parsed;
                    parsed.forEach(item => {
                        if (item.sender === 'user') {
                            appendMessage('user', item.content);
                        } else if (item.sender === 'bot') {
                            if (typeof item.content === 'object') {
                                appendBotMessage(item.content);
                            } else {
                                appendBotMessage({ answer: item.content });
                            }
                        }
                    });
                }
            }
        } catch (e) {
            console.warn('Unable to load chat history:', e);
        }
    }

    function clearChatHistory() {
        chatHistory = [];
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch (e) {}
        if (typeof ChatbotService !== 'undefined' && ChatbotService.resetContext) {
            ChatbotService.resetContext();
        }
        renderWelcomeMessage();
    }

    // Initialize UI on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatbotUI);
    } else {
        initChatbotUI();
    }
})();
