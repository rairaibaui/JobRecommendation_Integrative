{{-- Custom Confirmation Modal --}}
<div id="customConfirmModal" class="custom-modal-overlay" style="display:none;">
  <div class="custom-modal-box">
    <div class="custom-modal-icon custom-modal-icon-confirm">
      <i class="fas fa-question-circle"></i>
    </div>
    <div class="custom-modal-content">
      <h3 class="custom-modal-title" id="confirmModalTitle">Confirm Action</h3>
      <p class="custom-modal-message" id="confirmModalMessage">Are you sure you want to proceed?</p>
      <div class="custom-modal-actions" style="justify-content: flex-end;">
        <button type="button" class="custom-modal-btn custom-modal-cancel" onclick="closeCustomConfirm(false)">
          <i class="fas fa-times"></i> Cancel
        </button>
        <button type="button" class="custom-modal-btn custom-modal-confirm" onclick="closeCustomConfirm(true)" id="confirmModalBtn">
          <i class="fas fa-check"></i> Confirm
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Custom Alert Modal --}}
<div id="customAlertModal" class="custom-modal-overlay" style="display:none;">
  <div class="custom-modal-box">
    <div class="custom-modal-icon custom-modal-icon-alert">
      <i class="fas fa-info-circle"></i>
    </div>
    <div class="custom-modal-content">
      <h3 class="custom-modal-title" id="alertModalTitle">Notice</h3>
      <p class="custom-modal-message" id="alertModalMessage">Please note this message.</p>
      <div class="custom-modal-actions" style="justify-content: flex-end;">
        <button type="button" class="custom-modal-btn custom-modal-primary" onclick="closeCustomAlert()" style="min-width: 120px;">
          <i class="fas fa-check"></i> OK
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Custom Prompt Modal --}}
<div id="customPromptModal" class="custom-modal-overlay" style="display:none;">
  <div class="custom-modal-box">
    <div class="custom-modal-icon custom-modal-icon-prompt">
      <i class="fas fa-keyboard"></i>
    </div>
    <h3 class="custom-modal-title" id="promptModalTitle">Input Required</h3>
    <p class="custom-modal-message" id="promptModalMessage">Please enter your response:</p>
    <input type="text" id="promptModalInput" class="custom-modal-input" placeholder="Type here...">
    <div class="custom-modal-actions">
      <button type="button" class="custom-modal-btn custom-modal-cancel" onclick="closeCustomPrompt(null)">
        <i class="fas fa-times"></i> Cancel
      </button>
      <button type="button" class="custom-modal-btn custom-modal-confirm" onclick="closeCustomPrompt(document.getElementById('promptModalInput').value)">
        <i class="fas fa-check"></i> Submit
      </button>
    </div>
  </div>
</div>

<style>
  .custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: customFadeIn 0.2s ease;
  }

  @keyframes customFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes customSlideUp {
    from {
      opacity: 0;
      transform: translateY(30px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .custom-modal-box {
    background: white;
    border-radius: 12px;
    padding: 20px;
    max-width: 640px;
    width: 96%;
    box-shadow: 0 18px 48px rgba(13, 60, 120, 0.12);
    display: flex;
    gap: 18px;
    align-items: flex-start;
    animation: customSlideUp 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .custom-modal-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(13, 60, 120, 0.08);
    flex-shrink: 0;
  }

  .custom-modal-icon-confirm {
    background: linear-gradient(135deg, #648EB5, #334A5E);
  }

  .custom-modal-icon-alert {
    background: linear-gradient(135deg, #ffc107, #ff9800);
  }

  .custom-modal-icon-prompt {
    background: linear-gradient(135deg, #17a2b8, #138496);
  }

  .custom-modal-icon i {
    font-size: 32px;
    color: white;
  }

  .custom-modal-content { flex: 1; }

  .custom-modal-title {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #1f2d3d;
    margin: 0 0 8px;
  }

  .custom-modal-message {
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 14px;
    color: #475569;
    line-height: 1.5;
    margin: 0 0 18px;
  }

  .custom-modal-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 15px;
    margin-bottom: 24px;
    transition: all 0.2s ease;
  }

  .custom-modal-input:focus {
    outline: none;
    border-color: #648EB5;
    box-shadow: 0 0 0 4px rgba(100, 142, 181, 0.15);
  }

  .custom-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
  }

  .custom-modal-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 10px;
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 96px;
  }

  .custom-modal-cancel {
    background: #f1f3f5;
    color: #495057;
    border: 1px solid #dee2e6;
  }

  .custom-modal-cancel:hover {
    background: #e9ecef;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .custom-modal-confirm,
  .custom-modal-primary {
    background: linear-gradient(135deg, #5B9BD5, #0b63d6);
    color: white;
    box-shadow: 0 6px 18px rgba(11,99,214,0.18);
  }

  .custom-modal-confirm:hover,
  .custom-modal-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(100, 142, 181, 0.4);
  }

  .custom-modal-btn i {
    font-size: 14px;
  }
</style>

<script>
  // Custom Confirm Dialog
  let confirmCallback = null;

  function customConfirm(message, title = 'Confirm Action', confirmText = 'Confirm') {
    return new Promise((resolve) => {
      confirmCallback = resolve;
      document.getElementById('confirmModalTitle').textContent = title;
      document.getElementById('confirmModalMessage').textContent = message;
      document.getElementById('confirmModalBtn').innerHTML = '<i class="fas fa-check"></i> ' + confirmText;
      document.getElementById('customConfirmModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    });
  }

  function closeCustomConfirm(result) {
    document.getElementById('customConfirmModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    if (confirmCallback) {
      confirmCallback(result);
      confirmCallback = null;
    }
  }

  // Custom Alert Dialog
  let alertCallback = null;

  function customAlert(message, title = 'Notice') {
    return new Promise((resolve) => {
      alertCallback = resolve;
      document.getElementById('alertModalTitle').textContent = title;
      document.getElementById('alertModalMessage').textContent = message;
      document.getElementById('customAlertModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    });
  }

  function closeCustomAlert() {
    document.getElementById('customAlertModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    if (alertCallback) {
      alertCallback();
      alertCallback = null;
    }
  }

  // Custom Prompt Dialog
  let promptCallback = null;

  function customPrompt(message, title = 'Input Required', placeholder = 'Type here...') {
    return new Promise((resolve) => {
      promptCallback = resolve;
      document.getElementById('promptModalTitle').textContent = title;
      document.getElementById('promptModalMessage').textContent = message;
      const input = document.getElementById('promptModalInput');
      input.placeholder = placeholder;
      input.value = '';
      document.getElementById('customPromptModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
      setTimeout(() => input.focus(), 100);
    });
  }

  function closeCustomPrompt(value) {
    document.getElementById('customPromptModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    if (promptCallback) {
      promptCallback(value);
      promptCallback = null;
    }
  }

  // Close modals on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeCustomConfirm(false);
      closeCustomAlert();
      closeCustomPrompt(null);
    }
  });

  // Close modals on Enter in prompt
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.getElementById('customPromptModal').style.display === 'flex') {
      closeCustomPrompt(document.getElementById('promptModalInput').value);
    }
  });

  // Close on overlay click
  document.addEventListener('click', function(e) {
    if (e.target.id === 'customConfirmModal') closeCustomConfirm(false);
    if (e.target.id === 'customAlertModal') closeCustomAlert();
    if (e.target.id === 'customPromptModal') closeCustomPrompt(null);
  });
</script>
