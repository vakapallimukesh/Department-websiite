<?php
/**
 * AI Department Assistant Chatbot Component
 * SRKREC CSD & CSIT Departments
 * Colorful Multi-Hue Galaxy Planet SVG Animation (Gold, Cyan, Magenta, Emerald)
 */
?>
<!-- Chatbot Stylesheet -->
<link rel="stylesheet" href="assets/css/chatbot.css">

<!-- Floating Trigger Button & Animated Planet/Orbital Department AI Logo -->
<button id="chatbotTriggerBtn" class="chatbot-trigger-btn" aria-label="Open Department AI Chat" aria-expanded="false" title="Chat with Department AI">
    <!-- Ambient Multi-Color Radial Glow Layer -->
    <span class="ambient-gold-glow"></span>
    
    <!-- Animated Concentric Planet / Orbit Rings & Glowing Particles Layer (SVG) -->
    <div class="orbit-animation-container">
        <svg class="orbit-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Ring 1: Vibrant Amber Gold Gradient -->
                <linearGradient id="ringGradGold" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFD166" stop-opacity="0.95" />
                    <stop offset="50%" stop-color="#FFB703" stop-opacity="0.65" />
                    <stop offset="100%" stop-color="#FB8500" stop-opacity="0.90" />
                </linearGradient>

                <!-- Ring 2: Vibrant Electric Cyan / Blue Gradient -->
                <linearGradient id="ringGradCyan" x1="100%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#00F5FF" stop-opacity="0.95" />
                    <stop offset="50%" stop-color="#00B4D8" stop-opacity="0.55" />
                    <stop offset="100%" stop-color="#48CAE4" stop-opacity="0.90" />
                </linearGradient>

                <!-- Ring 3: Vibrant Neon Magenta / Purple Gradient -->
                <linearGradient id="ringGradPurple" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#F72585" stop-opacity="0.95" />
                    <stop offset="50%" stop-color="#B5179E" stop-opacity="0.55" />
                    <stop offset="100%" stop-color="#7209B7" stop-opacity="0.90" />
                </linearGradient>

                <!-- Ring 4: Vibrant Emerald Cyan / Lime Gradient -->
                <linearGradient id="ringGradEmerald" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#06D6A0" stop-opacity="0.95" />
                    <stop offset="50%" stop-color="#10B981" stop-opacity="0.60" />
                    <stop offset="100%" stop-color="#4CC9F0" stop-opacity="0.95" />
                </linearGradient>

                <!-- Glowing Particle Glow Filters -->
                <filter id="goldParticleGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <filter id="cyanParticleGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>

                <filter id="purpleParticleGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="3.5" result="blur" />
                    <feMerge>
                        <feMergeNode in="blur" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
            </defs>

            <!-- Orbit Ring 1 (Outer Ring, Vibrant Amber Gold) -->
            <g class="orbit-ring-group ring-group-1">
                <circle cx="100" cy="100" r="88" stroke="url(#ringGradGold)" stroke-width="1.6" stroke-dasharray="140 35 80 20" opacity="0.85" />
                <g class="particle-runner runner-1">
                    <circle cx="100" cy="12" r="4.2" fill="#FFD166" filter="url(#goldParticleGlow)" />
                    <circle cx="100" cy="12" r="2.0" fill="#FFFFFF" />
                </g>
            </g>

            <!-- Orbit Ring 2 (Middle-Outer Ring, Vibrant Electric Cyan) -->
            <g class="orbit-ring-group ring-group-2">
                <circle cx="100" cy="100" r="74" stroke="url(#ringGradCyan)" stroke-width="1.4" stroke-dasharray="110 40 60 30" opacity="0.80" />
                <g class="particle-runner runner-2">
                    <circle cx="174" cy="100" r="3.8" fill="#00F5FF" filter="url(#cyanParticleGlow)" />
                    <circle cx="174" cy="100" r="1.8" fill="#FFFFFF" />
                </g>
            </g>

            <!-- Orbit Ring 3 (Middle-Inner Ring, Vibrant Magenta / Purple) -->
            <g class="orbit-ring-group ring-group-3">
                <circle cx="100" cy="100" r="60" stroke="url(#ringGradPurple)" stroke-width="1.5" stroke-dasharray="90 25 40 15" opacity="0.85" />
                <g class="particle-runner runner-3">
                    <circle cx="100" cy="160" r="4.2" fill="#F72585" filter="url(#purpleParticleGlow)" />
                    <circle cx="100" cy="160" r="2.0" fill="#FFFFFF" />
                </g>
            </g>

            <!-- Orbit Ring 4 (Inner Ring, Emerald Cyan) -->
            <g class="orbit-ring-group ring-group-4">
                <circle cx="100" cy="100" r="48" stroke="url(#ringGradEmerald)" stroke-width="1.2" opacity="0.75" />
                <g class="particle-runner runner-4">
                    <circle cx="52" cy="100" r="3.2" fill="#06D6A0" filter="url(#cyanParticleGlow)" />
                </g>
            </g>
        </svg>
    </div>

    <!-- Central Floating Glass Orb with ONLY Text inside -->
    <div class="central-glass-orb">
        <div class="orb-content">
            <span class="brand-title">DEPARTMENT AI</span>
            <span class="brand-subtitle">CSD &amp; CSIT DEPARTMENT</span>
        </div>
    </div>

    <!-- Online Status Dot -->
    <span class="status-indicator-dot" title="Online"></span>

    <!-- Close Icon (Shown when Chat Window is Active) -->
    <i class="fas fa-xmark icon-close"></i>
</button>

<!-- Floating Chat Window Panel -->
<div id="chatbotWindow" class="chatbot-window" role="dialog" aria-label="Department AI Chat Window">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">
                <!-- Mini Animated Orbit Avatar -->
                <div class="mini-orbit-avatar">
                    <svg class="mini-orbit-svg" viewBox="0 0 100 100" fill="none">
                        <circle cx="50" cy="50" r="42" stroke="url(#ringGradCyan)" stroke-width="1.5" opacity="0.7" />
                        <circle cx="50" cy="50" r="32" stroke="url(#ringGradGold)" stroke-dasharray="25 10" stroke-width="1" opacity="0.8" />
                        <circle cx="50" cy="18" r="2.5" fill="#00F5FF" />
                    </svg>
                    <div class="mini-glass-core">AI</div>
                </div>
            </div>
            <div class="chatbot-title-group">
                <h5>DEPARTMENT AI</h5>
                <div class="chatbot-status">
                    <span class="status-dot"></span> How can I help you?
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

    <!-- Conversation Container (White Body & Black Text) -->
    <div id="chatbotBody" class="chatbot-body">
        <!-- Welcome Message & Quick Question Pills render here dynamically -->
    </div>

    <!-- Footer Input Bar -->
    <div class="chatbot-footer">
        <form id="chatbotForm" class="chatbot-input-form" onsubmit="return false;">
            <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Ask anything about our department..." aria-label="Ask anything about our department" autocomplete="off" autocorrect="off" spellcheck="false">
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
