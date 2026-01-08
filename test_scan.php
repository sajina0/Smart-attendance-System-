<?php 
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
  header("Location: landing.php");
  exit;
}
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['fullname'] ?? 'Unknown Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scan QR - Smart Attendance</title>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
    }
    .header {
      text-align: center;
      color: white;
      margin-bottom: 30px;
    }
    .header h2 {
      font-size: 28px;
      margin-bottom: 10px;
    }
    .header p {
      font-size: 16px;
      opacity: 0.9;
    }
    .scanner-container {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      margin-bottom: 20px;
    }
    #reader {
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 15px;
    }
    #reader video {
      border-radius: 10px;
    }
    .status-message {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      text-align: center;
      font-weight: 500;
    }
    .status-idle {
      background: #e3f2fd;
      color: #1565c0;
      border: 2px solid #90caf9;
    }
    .status-scanning {
      background: #fff3e0;
      color: #e65100;
      border: 2px solid #ffb74d;
    }
    .status-success {
      background: #e8f5e9;
      color: #2e7d32;
      border: 2px solid #81c784;
    }
    .status-error {
      background: #ffebee;
      color: #c62828;
      border: 2px solid #e57373;
    }
    .controls {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }
    .btn {
      flex: 1;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    .btn-primary {
      background: #667eea;
      color: white;
    }
    .btn-primary:hover {
      background: #5568d3;
      transform: translateY(-2px);
    }
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    .btn-secondary:hover {
      background: #5a6268;
      transform: translateY(-2px);
    }
    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }
    .camera-select {
      margin-bottom: 15px;
    }
    .camera-select label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-weight: 600;
    }
    .camera-select select {
      width: 100%;
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      background: white;
    }
    .info-box {
      background: #f8f9fa;
      border-left: 4px solid #667eea;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .info-box p {
      margin: 5px 0;
      color: #495057;
    }
    .info-box strong {
      color: #333;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2> Scan QR Code</h2>
      <p>Position the QR code within the camera frame</p>
    </div>

    <div class="scanner-container">
      <div class="info-box">
        <p><strong>Student:</strong> <?php echo htmlspecialchars($student_name); ?></p>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
      </div>

      <div id="status" class="status-message status-idle">
        Initializing camera...
      </div>

      <div class="camera-select" id="cameraSelectContainer" style="display: none;">
        <label for="cameraSelect">Select Camera:</label>
        <select id="cameraSelect">
          <option value="">Loading cameras...</option>
        </select>
      </div>

      <div id="reader"></div>

      <div class="controls">
        <button id="startBtn" class="btn btn-primary" onclick="startScanner()">Start Scanner</button>
        <button id="stopBtn" class="btn btn-secondary" onclick="stopScanner()" disabled>Stop Scanner</button>
      </div>
    </div>

    <a href="student_dashboard.php" class="btn btn-secondary" style="display: block; margin-top: 20px;">⬅ Back to Dashboard</a>
  </div>

  <script>
    const studentId = "<?php echo addslashes($student_id); ?>";
    const studentName = "<?php echo addslashes($student_name); ?>";
    let html5QrCode = null;
    let isScanning = false;
    let cameras = [];

    // Update status message
    function updateStatus(message, type) {
      const statusEl = document.getElementById('status');
      statusEl.textContent = message;
      statusEl.className = 'status-message status-' + type;
    }

    // Get available cameras
    function getCameras() {
      Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
          cameras = devices;
          const select = document.getElementById('cameraSelect');
          select.innerHTML = '';
          
          devices.forEach((device, index) => {
            const option = document.createElement('option');
            option.value = device.id;
            option.text = device.label || `Camera ${index + 1}`;
            select.appendChild(option);
          });

          // Show camera selector if multiple cameras
          if (devices.length > 1) {
            document.getElementById('cameraSelectContainer').style.display = 'block';
          }

          updateStatus('Click "Start Scanner" to begin', 'idle');
        } else {
          updateStatus('No cameras found on this device', 'error');
        }
      }).catch(err => {
        console.error('Error getting cameras:', err);
        updateStatus('Unable to access camera. Please grant permission.', 'error');
      });
    }

    // Start the QR scanner
    function startScanner() {
      const cameraId = document.getElementById('cameraSelect').value || 
                       (cameras.length > 0 ? cameras[0].id : undefined);
      
      if (!cameraId && cameras.length === 0) {
        updateStatus('No camera available', 'error');
        return;
      }

      if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
      }

      const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
      };

      html5QrCode.start(
        cameraId || { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanError
      ).then(() => {
        isScanning = true;
        document.getElementById('startBtn').disabled = true;
        document.getElementById('stopBtn').disabled = false;
        updateStatus('Scanner active - Align QR code in frame', 'scanning');
      }).catch(err => {
        console.error('Error starting scanner:', err);
        updateStatus('Failed to start camera: ' + err, 'error');
      });
    }

    // Stop the QR scanner
    function stopScanner() {
      if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
          isScanning = false;
          document.getElementById('startBtn').disabled = false;
          document.getElementById('stopBtn').disabled = true;
          updateStatus('Scanner stopped', 'idle');
        }).catch(err => {
          console.error('Error stopping scanner:', err);
        });
      }
    }

    // Handle successful QR scan
    function onScanSuccess(decodedText, decodedResult) {
      // Stop scanner immediately
      stopScanner();
      
      updateStatus('✅ QR Code detected! Processing attendance...', 'success');

      // Send data to server
      fetch("mark_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `qr_data=${encodeURIComponent(decodedText)}&student_id=${encodeURIComponent(studentId)}&student_name=${encodeURIComponent(studentName)}`
      })
      .then(response => response.text())
      .then(data => {
        // Display response
        document.body.innerHTML = data;
      })
      .catch(error => {
        console.error('Error:', error);
        updateStatus('❌ Error submitting attendance: ' + error, 'error');
        // Re-enable scanner after error
        setTimeout(() => {
          document.getElementById('startBtn').disabled = false;
        }, 2000);
      });
    }

    // Handle scan errors (mostly can be ignored)
    function onScanError(errorMessage) {
      // Most errors are just "no QR code found" which is normal
      // Only log actual problems
      if (errorMessage.includes('NotAllowedError') || errorMessage.includes('NotFoundError')) {
        console.error('Camera error:', errorMessage);
        updateStatus('Camera access denied or not found', 'error');
        stopScanner();
      }
    }

    // Change camera when selector changes
    document.getElementById('cameraSelect')?.addEventListener('change', function() {
      if (isScanning) {
        stopScanner();
        setTimeout(startScanner, 500);
      }
    });

    // Request camera permission on page load
    async function requestCameraPermission() {
      // Check if page is served securely
      const isSecureContext = window.isSecureContext;
      const protocol = window.location.protocol;
      const hostname = window.location.hostname;
      
      // Show helpful setup instructions if not secure
      if (!isSecureContext && protocol !== 'https:' && !hostname.match(/^(localhost|127\.0\.0\.1)$/)) {
        const setupGuide = `
          <div style="text-align: left; padding: 10px;">
            <strong> For Mobile Testing:</strong><br><br>
            <strong>Option 1 - Use ngrok (Easiest):</strong><br>
            1. Download ngrok: <a href="https://ngrok.com/download" target="_blank">ngrok.com/download</a><br>
            2. Run: <code>ngrok http 80</code> (or your port)<br>
            3. Use the HTTPS URL provided<br><br>
            
            <strong>Option 2 - Local Network:</strong><br>
            1. Find your PC IP: <code>ipconfig</code> (Windows) or <code>ifconfig</code> (Mac/Linux)<br>
            2. Enable HTTPS on XAMPP/WAMP<br>
            3. Access: <code>https://YOUR_IP/project</code><br><br>
            
            <strong>Option 3 - Host Online:</strong><br>
            Upload to free hosting with HTTPS:<br>
            • InfinityFree, 000webhost, or Netlify<br><br>
            
            <strong>Current URL:</strong> ${protocol}//${window.location.host}
          </div>
        `;
        updateStatus(' Camera requires HTTPS for mobile access', 'error');
        document.querySelector('.scanner-container').innerHTML += setupGuide;
        document.getElementById('startBtn').disabled = true;
        return;
      }

      // Check if browser supports camera access
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateStatus('❌ Camera API not available. Browser may not support this feature.', 'error');
        document.getElementById('startBtn').disabled = true;
        return;
      }

      try {
        updateStatus('Requesting camera permission...', 'scanning');
        // Request camera access with mobile-optimized settings
        const stream = await navigator.mediaDevices.getUserMedia({ 
          video: { 
            facingMode: "environment",
            width: { ideal: 1280 },
            height: { ideal: 720 }
          } 
        });
        
        // Stop the stream immediately (we just needed permission)
        stream.getTracks().forEach(track => track.stop());
        
        // Now get the list of cameras
        getCameras();
      } catch (err) {
        console.error('Camera permission error:', err);
        if (err.name === 'NotAllowedError') {
          updateStatus('❌ Camera access denied. Please allow camera permission when prompted.', 'error');
        } else if (err.name === 'NotFoundError') {
          updateStatus('❌ No camera found on this device.', 'error');
        } else if (err.name === 'NotReadableError') {
          updateStatus('❌ Camera is being used by another app. Please close other camera apps.', 'error');
        } else {
          updateStatus('❌ Camera error: ' + err.message, 'error');
        }
      }
    }

    // Initialize on page load
    window.addEventListener('load', function() {
      requestCameraPermission();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
      if (isScanning) {
        stopScanner();
      }
    });
  </script>
</body>
</html>