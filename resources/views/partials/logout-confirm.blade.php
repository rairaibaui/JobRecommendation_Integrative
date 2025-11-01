{{-- Styled Logout Confirmation Modal --}}
<div id="logoutConfirmModal" class="logout-modal-overlay" style="display:none;">
  <div class="logout-modal-box" id="logoutModalContent">
    <div class="logout-modal-icon" id="logoutModalIcon">
      <i class="fas fa-sign-out-alt"></i>
    </div>
    <h3 class="logout-modal-title" id="logoutModalTitle">Confirm Logout</h3>
    <p class="logout-modal-message" id="logoutModalMessage">Are you sure you want to log out? You'll need to sign in again to access your account.</p>
    <div class="logout-modal-actions" id="logoutModalActions">
      <button type="button" class="logout-modal-btn logout-modal-cancel" onclick="closeLogoutModal()">
        <i class="fas fa-times"></i> Cancel
      </button>
      <button type="button" class="logout-modal-btn logout-modal-confirm" onclick="confirmLogout()">
        <i class="fas fa-check"></i> Yes, Log Out
      </button>
    </div>
    
    {{-- Loading State (hidden by default) --}}
    <div class="logout-loading-state" id="logoutLoadingState" style="display:none;">
      <div class="logout-spinner"></div>
      <p class="logout-loading-text">Logging you out...</p>
      <p class="logout-loading-subtext">Please wait a moment</p>
    </div>
  </div>
</div>

<style>
  .logout-modal-overlay {
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
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .logout-modal-box {
    background: white;
    border-radius: 16px;
    padding: 32px;
    max-width: 440px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .logout-modal-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #648EB5, #334A5E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(100, 142, 181, 0.3);
  }

  .logout-modal-icon i {
    font-size: 32px;
    color: white;
  }

  .logout-modal-title {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: #334A5E;
    margin: 0 0 12px;
  }

  .logout-modal-message {
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 15px;
    color: #666;
    line-height: 1.6;
    margin: 0 0 28px;
  }

  .logout-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .logout-modal-btn {
    flex: 1;
    max-width: 160px;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .logout-modal-cancel {
    background: #f1f3f5;
    color: #495057;
    border: 1px solid #dee2e6;
  }

  .logout-modal-cancel:hover {
    background: #e9ecef;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .logout-modal-confirm {
    background: linear-gradient(135deg, #648EB5, #334A5E);
    color: white;
    box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
  }

  .logout-modal-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(100, 142, 181, 0.4);
  }

  .logout-modal-btn i {
    font-size: 14px;
  }

  /* Loading State Styles */
  .logout-loading-state {
    text-align: center;
    padding: 20px 0;
  }

  .logout-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    border: 4px solid #f3f4f6;
    border-top: 4px solid #648EB5;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .logout-loading-text {
    font-family: 'Poppins', 'Inter', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #334A5E;
    margin: 0 0 8px;
  }

  .logout-loading-subtext {
    font-family: 'Roboto', 'Inter', sans-serif;
    font-size: 14px;
    color: #666;
    margin: 0;
    opacity: 0.8;
  }

  /* Fade transitions */
  .fade-out {
    animation: fadeOut 0.3s ease forwards;
  }

  .fade-in {
    animation: fadeIn 0.3s ease forwards;
  }

  @keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.95); }
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
</style>

<script>
  let logoutFormPending = null;

  function showLogoutModal(form) {
    logoutFormPending = form;
    const modal = document.getElementById('logoutConfirmModal');
    if (modal) {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    return false;
  }

  function closeLogoutModal() {
    const modal = document.getElementById('logoutConfirmModal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    logoutFormPending = null;
  }

  function confirmLogout() {
    if (logoutFormPending) {
      // Show loading state
      const icon = document.getElementById('logoutModalIcon');
      const title = document.getElementById('logoutModalTitle');
      const message = document.getElementById('logoutModalMessage');
      const actions = document.getElementById('logoutModalActions');
      const loadingState = document.getElementById('logoutLoadingState');
      
      // Fade out confirmation content
      if (icon) icon.classList.add('fade-out');
      if (title) title.classList.add('fade-out');
      if (message) message.classList.add('fade-out');
      if (actions) actions.classList.add('fade-out');
      
      // After fade out, hide confirmation and show loading
      setTimeout(() => {
        if (icon) icon.style.display = 'none';
        if (title) title.style.display = 'none';
        if (message) message.style.display = 'none';
        if (actions) actions.style.display = 'none';
        
        // Show loading state with fade in
        if (loadingState) {
          loadingState.style.display = 'block';
          loadingState.classList.add('fade-in');
        }
        
        // Submit form after showing loading animation (1 second delay for smooth UX)
        setTimeout(() => {
            // Create a new form element to avoid any event handler issues
            const newForm = document.createElement('form');
            newForm.method = 'POST';
            newForm.action = logoutFormPending.action;
          
            // Copy CSRF token
            const csrfInput = logoutFormPending.querySelector('input[name="_token"]');
            if (csrfInput) {
              const newCsrf = document.createElement('input');
              newCsrf.type = 'hidden';
              newCsrf.name = '_token';
              newCsrf.value = csrfInput.value;
              newForm.appendChild(newCsrf);
            }
          
            // Append and submit
            document.body.appendChild(newForm);
            newForm.submit();
        }, 1000);
      }, 300);
    }
  }

  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLogoutModal();
    }
  });

  // Close modal on overlay click
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('logoutConfirmModal');
    if (modal && e.target === modal) {
      closeLogoutModal();
    }
  });
</script>
