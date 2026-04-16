<?php
/**
 * ADET - Application Development & Emerging Technologies
 * Part 2: Client Application (ADET.PHP)
 * 
 * This script serves as a modern, premium client that consumes a RESTful Service (service.php).
 * Features:
 * - Stunning Dark Mode UI
 * - Real-time JSON Consumption
 * - Comprehensive Error Handling
 * - Detailed Response Visualization
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADET | REST Service Client</title>
    
    <!-- Premium Typography & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --bg: #050810;
            --card-bg: rgba(15, 20, 35, 0.8);
            --primary: #8b5cf6;
            --primary-glow: rgba(139, 92, 246, 0.4);
            --secondary: #06b6d4;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.08);
            --success: #10b981;
            --error: #f43f5e;
            --warning: #f59e0b;
            --surface: rgba(255, 255, 255, 0.03);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(139, 92, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(6, 182, 212, 0.1) 0%, transparent 40%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @media (max-width: 850px) {
            .container { grid-template-columns: 1fr; }
        }

        /* --- Left Side: Header & Form --- */
        .info-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .badge {
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.2rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            display: inline-block;
            margin-bottom: 1.5rem;
            width: fit-content;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h1 span {
            display: block;
            color: var(--primary);
            -webkit-text-fill-color: initial;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 400px;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            color: var(--text-muted);
            transition: color 0.3s;
        }

        input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 14px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
            outline: none;
        }

        input:focus {
            border-color: var(--primary);
            background: rgba(139, 92, 246, 0.05);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        input:focus + i { color: var(--primary); }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1.2rem;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px var(--primary-glow);
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px var(--primary-glow);
        }

        .btn:active { transform: translateY(0) scale(1); }

        /* --- Right Side: Client Monitor --- */
        .monitor-card {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 32px;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .monitor-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        .monitor-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .monitor-title h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .monitor-title p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .status-pill {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--text-muted);
        }

        .status-online .status-dot {
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
            animation: pulse 2s infinite;
        }

        .results-area {
            flex-grow: 1;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            max-height: 400px;
            overflow-y: auto;
        }

        .placeholder {
            margin: auto;
            text-align: center;
            color: var(--text-muted);
            opacity: 0.5;
        }

        .placeholder i { margin-bottom: 1rem; }

        /* --- JSON Preview --- */
        .json-viewer {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            color: #a5b4fc;
            line-height: 1.6;
            white-space: pre-wrap;
            animation: slideIn 0.4s ease-out;
        }

        .response-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid var(--border);
            animation: slideIn 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .response-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .response-item:last-child { border: none; }

        .label { color: var(--text-muted); font-size: 0.85rem; }
        .value { font-weight: 600; font-size: 0.95rem; }

        .status-msg {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .success-bg { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .error-bg { background: rgba(244, 63, 94, 0.1); color: var(--error); border: 1px solid rgba(244, 63, 94, 0.2); }
        .warning-bg { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }

        /* --- Animations --- */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* --- Scrollbar Customization --- */
        .results-area::-webkit-scrollbar { width: 6px; }
        .results-area::-webkit-scrollbar-track { background: transparent; }
        .results-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
        .results-area::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

    <div class="container">
        <!-- Input Panel -->
        <section class="info-panel">
            <div class="badge">Part 2: REST Client</div>
            <h1>ADET <span>API Consumption</span></h1>
            <p class="subtitle">Enter your database credentials to initiate a bridge with the centralized <strong>service.php</strong> endpoint.</p>

            <form id="clientForm" onsubmit="handleRequest(event)">
                <div class="input-group">
                    <label for="username">Remote Username</label>
                    <div class="input-wrapper">
                        <i data-lucide="user" size="18"></i>
                        <input type="text" id="username" placeholder="e.g. system_admin" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Encrypted Secret</label>
                    <div class="input-wrapper">
                        <i data-lucide="lock" size="18"></i>
                        <input type="password" id="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn" id="submitBtn">
                    <span id="btnIcon"><i data-lucide="zap" size="20"></i></span>
                    <span id="btnText">Initiate Consumption</span>
                </button>
            </form>
        </section>

        <!-- Monitor Panel -->
        <section class="monitor-card">
            <div class="monitor-header">
                <div class="monitor-title">
                    <h2>Response Monitor</h2>
                    <p id="endpoint-label">Target: ./service.php</p>
                </div>
                <div class="status-pill status-online" id="status-badge">
                    <div class="status-dot"></div>
                    <span id="status-text">Client Ready</span>
                </div>
            </div>

            <div class="results-area" id="results">
                <div class="placeholder" id="placeholder">
                    <i data-lucide="terminal" size="48"></i>
                    <p>Awaiting handshake...<br>No payload detected in stream.</p>
                </div>
            </div>

            <div id="footer-info" style="font-size: 0.75rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                <span>V1.0.4-STABLE</span>
                <span>LATENCY: ---</span>
            </div>
        </section>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        async function handleRequest(event) {
            event.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const results = document.getElementById('results');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const statusBadge = document.getElementById('status-badge');
            const statusText = document.getElementById('status-text');

            // Reset UI to Loading State
            results.innerHTML = `
                <div class="placeholder">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 1rem">Negotiating secure bridge...</p>
                </div>
            `;
            
            submitBtn.disabled = true;
            btnText.innerText = 'Transmitting...';
            btnIcon.innerHTML = '<div class="loading-spinner"></div>';
            
            statusBadge.className = 'status-pill';
            statusText.innerText = 'Transmitting';

            const startTime = performance.now();

            try {
                // Construct URL with GET parameters as expected by service.php
                const url = `service.php?user=${encodeURIComponent(username)}&pass=${encodeURIComponent(password)}`;
                
                const response = await fetch(url);
                const latency = Math.round(performance.now() - startTime);
                
                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                
                const data = await response.json();
                
                // Visualization logic
                setTimeout(() => {
                    renderResponse(data, latency);
                    
                    // Update Status Badge based on API status
                    if (data.status === 'success') {
                        statusBadge.className = 'status-pill status-online';
                        statusText.innerText = 'Active Session';
                    } else if (data.status === 'unauthorized') {
                        statusBadge.className = 'status-pill';
                        statusText.innerText = 'Login Failed';
                        statusBadge.style.color = 'var(--error)';
                    } else {
                        statusBadge.className = 'status-pill';
                        statusText.innerText = 'Client Warning';
                    }
                    
                    document.querySelector('#footer-info span:last-child').innerText = `LATENCY: ${latency}ms`;
                    
                    // Reset Button
                    submitBtn.disabled = false;
                    btnText.innerText = 'Re-Initiate';
                    btnIcon.innerHTML = '<i data-lucide="refresh-cw" size="20"></i>';
                    lucide.createIcons();
                }, 800); // Polished delay

            } catch (error) {
                renderError(error.message);
                submitBtn.disabled = false;
                btnText.innerText = 'Retry Connection';
                btnIcon.innerHTML = '<i data-lucide="alert-triangle" size="20"></i>';
                statusBadge.className = 'status-pill';
                statusText.innerText = 'Connection Error';
                lucide.createIcons();
            }
        }

        function renderResponse(data, latency) {
            const results = document.getElementById('results');
            results.innerHTML = '';

            // Status Banner
            const banner = document.createElement('div');
            banner.className = `status-msg ${data.status === 'success' ? 'success-bg' : data.status === 'unauthorized' ? 'error-bg' : 'warning-bg'}`;
            
            let icon = 'info';
            if (data.status === 'success') icon = 'check-circle';
            if (data.status === 'unauthorized') icon = 'shield-x';
            if (data.status === 'incomplete') icon = 'help-circle';

            banner.innerHTML = `<i data-lucide="${icon}" size="20"></i> <span>${data.message}</span>`;
            results.appendChild(banner);

            // Detailed Response Group
            if (data.user_data) {
                const card = document.createElement('div');
                card.className = 'response-card';
                card.innerHTML = `
                    <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase; font-weight: 700;">Extracted Payload</div>
                    <div class="response-item">
                        <span class="label">Username</span>
                        <span class="value">${data.user_data.username}</span>
                    </div>
                    <div class="response-item">
                        <span class="label">System Role</span>
                        <span class="value" style="color: var(--secondary)">${data.user_data.role}</span>
                    </div>
                    <div class="response-item">
                        <span class="label">Protocol</span>
                        <span class="value">JSON / REST</span>
                    </div>
                `;
                results.appendChild(card);
            }

            // Raw JSON Preview
            const jsonTitle = document.createElement('p');
            jsonTitle.style = 'font-size: 0.75rem; color: var(--text-muted); margin: 1.5rem 0 0.5rem 0; font-weight: 600;';
            jsonTitle.innerText = 'RAW RAW STREAM';
            results.appendChild(jsonTitle);

            const jsonPre = document.createElement('div');
            jsonPre.className = 'json-viewer';
            jsonPre.textContent = JSON.stringify(data, null, 4);
            results.appendChild(jsonPre);

            lucide.createIcons();
        }

        function renderError(msg) {
            const results = document.getElementById('results');
            results.innerHTML = `
                <div class="status-msg error-bg">
                    <i data-lucide="wifi-off" size="20"></i>
                    <span>Network Failure: ${msg}</span>
                </div>
                <div class="placeholder">
                    <p style="font-size: 0.85rem">Ensure local server is running and <strong>service.php</strong> is accessible.</p>
                </div>
            `;
            lucide.createIcons();
        }
    </script>
</body>
</html>
