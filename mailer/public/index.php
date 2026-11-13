<?php
$config = require_once __DIR__ . '/../config/email_config.php';
require_once __DIR__ . '/../vendor/autoload.php';
$needsConfig = !$config['from_email'] || !$config['smtp_host'] || !$config['smtp_user'] || !$config['smtp_pass'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Bulk Mailer</title>
    <style>

        <div class="card">
            <h2>✉️ Send Bulk Email</h2>
            <form id="sendForm">
                <div class="form-group">
                    <label for="subject">Email Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter email subject" required>
                </div>
                <div class="form-group">
                    <label for="emailType">Email Format</label>
                    <select id="emailType" name="email_type">
                        <option value="text">Plain Text</option>
                        <option value="html">HTML</option>
                        <option value="template">Template (with variables)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="template">Email Content</label>
                    <textarea id="template" name="template" placeholder="Enter your email content here..." required></textarea>
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">
                        <?php if ($needsConfig): ?>Fill in SMTP details first.<?php else: ?>Use variables like {name} for templates.<?php endif; ?>
                    </small>
                </div>
                <div class="form-group">
                    <label for="recipients">Recipients (Email or Name,Email - one per line)</label>
                    <textarea id="recipients" name="recipients" 
                              placeholder="example@example.com&#10;John Doe &lt;john@example.com&gt;&#10;admin@example.com" required></textarea>

        <div class="card">
            <h2>📄 Template Example</h2>
            <p>Here's an example of a HTML template with variables:</p>
            <div class="template-preview">
                <html>
                <body>
                    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
                        <tr><td align="center" style="padding: 40px 0;">
                            <h1>Welcome, {name}!</h1>
                            <p style="color: #666;">Thank you for choosing <strong>{company_name}</strong>.</p>
                            <p style="color: #444;">{message}</p>
                            <p style="color: #999; font-size: 12px; margin-top: 30px;">Sent on {date}</p>
                        </td></tr>
                    </table>
                </body>
                </html>
            </div>
            <small style="color: #7f8c8d;">Use {variable_name} in your template. The mailer will replace them with actual values.</small>
        </div>
    </div>
    <script>
        document.getElementById('sendForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('sendBtn');
            const status = document.getElementById('status');
            const resetBtn = document.getElementById('resetBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Sending...';
            status.className = 'status';
            status.textContent = 'Sending emails...';
            const formData = new FormData(form);
            formData.append('email_type', document.getElementById('emailType').value);
            try {
                const response = await fetch('send.php', { method: 'POST', body: formData });
                const data = await response.json();
                status.className = data.success ? 'status success' : 'status error';
                status.textContent = data.message;
                if (data.errors && data.errors.length > 0) {
                    const errorList = document.createElement('ul');
                    errorList.className = 'error-list';
                    data.errors.forEach(err => {
                        const li = document.createElement('li');
                        li.textContent = err;
                        errorList.appendChild(li);
                    });
                    status.appendChild(errorList);
                }
                document.getElementById('successCount').textContent = data.successCount || 0;
                document.getElementById('failCount').textContent = data.failCount || 0;
                document.getElementById('totalCount').textContent = data.totalCount || 0;
                document.getElementById('progressPercent').textContent = data.progress ? data.progress + '%' : '0%';
                resetBtn.classList.remove('hidden');
            } catch (error) {
                status.className = 'status error';
                status.textContent = 'Error: ' + error.message;
            } finally {
                btn.disabled = false;
                btn.textContent = '📤 Send Emails';
            }
        });
        document.getElementById('resetBtn').addEventListener('click', function() {
            document.getElementById('sendForm').reset();
            document.getElementById('status').className = 'status hidden';
            document.getElementById('status').textContent = '';
            document.getElementById('stats').className = 'stats hidden';
            document.getElementById('resetBtn').classList.add('hidden');
        });
    </script>
</body>
</html>
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">One email per line. Use "Name &lt;email&gt;" for name + email.</small>
                </div>
                <button type="submit" class="btn btn-success" id="sendBtn">📤 Send Emails</button>
                <button type="button" class="btn btn-secondary hidden" id="resetBtn">Reset</button>
            </form>
            <div id="status" class="status hidden"></div>
            <?php if (!$needsConfig): ?>
            <div class="stats hidden" id="stats">
                <div class="stat-box"><div class="number" id="successCount">0</div><div class="label">Sent</div></div>
                <div class="stat-box"><div class="number" id="failCount">0</div><div class="label">Failed</div></div>
                <div class="stat-box"><div class="number" id="totalCount">0</div><div class="label">Total</div></div>
                <div class="stat-box"><div class="number" id="progressPercent">0%</div><div class="label">Progress</div></div>
            </div>
            <?php endif; ?>
        </div>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #333; line-height: 1.6; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
        header h1 { font-size: 2em; margin-bottom: 10px; }
        .card {
            background: white; border-radius: 8px; padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;
        }
        .card h2 { margin-bottom: 20px; color: #2c3e50; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;
            font-size: 16px; font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none; border-color: #3498db; box-shadow: 0 0 5px rgba(52,152,219,0.3);
        }
        textarea { min-height: 150px; resize: vertical; }
        .btn {
            padding: 12px 30px; background: #3498db; color: white; border: none;
            border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600;
        }
        .btn:hover { background: #2980b9; }
        .btn:disabled { background: #bdc3c7; cursor: not-allowed; }
        .btn-success { background: #27ae60; } .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; } .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; } .btn-secondary:hover { background: #7f8c8d; }
        .status { padding: 15px; border-radius: 4px; margin-top: 20px; display: <?php echo $needsConfig ? 'none' : 'block'; ?>; }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .error-list { color: #721c24; margin-top: 10px; } .error-list li { margin-bottom: 5px; }
        .config-warning {
            background: #fef9e7; border: 1px solid #f9e79f; border-radius: 4px;
            padding: 15px; margin-bottom: 20px;
        }
        .config-warning strong { color: #b7950b; }
        .hidden { display: none; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-top: 20px; }
        .stat-box { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-box .number { font-size: 2em; font-weight: bold; color: #2c3e50; }
        .stat-box .label { color: #7f8c8d; font-size: 0.9em; }
        .template-preview {
            background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;
            padding: 15px; margin-top: 10px; max-height: 200px; overflow-y: auto; font-size: 14px;
        }
        @media (max-width: 768px) { .card { padding: 20px; } .stats { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="container"><h1>📧 PHP Bulk Mailer</h1><p>Send bulk emails using SMTP</p></div>
    </header>
    <div class="container">
        <?php if ($needsConfig): ?>
        <div class="config-warning">
            <strong>⚠️ SMTP Config Required</strong> - Edit config/email_config.php
        </div>
        <?php endif; ?>