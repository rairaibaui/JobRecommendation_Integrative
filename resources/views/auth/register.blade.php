<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 0;
            box-sizing: border-box;
        }

        .form-container {
            width: 700px;
            background: #fff;
            border: 1px solid #9A8D8D;
            border-radius: 27px;
            padding: 40px 60px;
            box-sizing: border-box;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .header {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        h2 {
            font-size: 32px;
            font-weight: 400;
            margin: 0;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        label {
            font-size: 13px;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            height: 38px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.34);
            background: #D3D3D3;
            padding: 0 10px;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
            box-sizing: border-box;
        }

        .input-error {
            border: 2px solid red;
            background: #ffe6e6;
        }

        .error-text {
            color: red;
            font-size: 12px;
            margin-top: 3px;
            display: block;
        }

        .create-btn {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(180deg, #648EB5 0%, #334A5E 100%);
            color: white;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);
        }

        .create-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .bottom-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
        }

        .bottom-text a {
            display: inline-block;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .bottom-text a:hover {
            background-color: #f0f0f0;
        }

        .req-item {
            margin: 2px 0;
            font-size: 12px;
            color: #666;
        }

        .req-item.valid {
            color: #28a745;
        }

        .req-item.valid::before {
            content: "✅ ";
        }

        .req-item.invalid {
            color: #dc3545;
        }

        .req-item.invalid::before {
            content: "❌ ";
        }

        .terms {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin: 10px 0;
        }

        .terms input {
            width: 14px;
            height: 14px;
            margin: 0;
        }

        .terms a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }

        /* Custom upload UI */
        .upload-zone {
            border: 2px dashed #648EB5;
            background: #f9fbff;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
            user-select: none;
        }
        .upload-zone:hover {
            background: #f3f8ff;
        }
        .upload-zone.dragover {
            border-color: #334A5E;
            background: #eef4fb;
        }
        .upload-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e8f0f9;
            color: #334A5E;
            border-radius: 10px;
            flex: 0 0 38px;
        }
        .upload-text {
            font-size: 13px;
            line-height: 1.3;
            color: #333;
        }
        .upload-text small {
            color: #666;
        }
        .file-name {
            margin-left: auto;
            font-size: 12px;
            color: #334A5E;
            background: #eef4fb;
            padding: 6px 10px;
            border-radius: 8px;
            max-width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="header">
            <h2>CREATE ACCOUNT</h2>
            <!-- Account type switch in the upper right -->
            <div style="display:flex; gap:8px; background:#f5f5f5; border:1px solid #ddd; border-radius:10px; padding:4px;">
                <button type="button" id="btn-job" class="type-btn active" onclick="selectType('job_seeker')" style="border:none; padding:8px 12px; border-radius:8px; cursor:pointer; background:#fff;">Job Seeker</button>
                <button type="button" id="btn-emp" class="type-btn" onclick="selectType('employer')" style="border:none; padding:8px 12px; border-radius:8px; cursor:pointer; background:transparent;">Employer</button>
            </div>
        </div>

        <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_type" id="user_type" value="{{ old('user_type','job_seeker') }}">

            @if(session('error'))
                <div style="background:#ffe6e6; color:#a40000; border:1px solid #ffb3b3; padding:10px 12px; border-radius:8px; margin-bottom:12px;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background:#fff3cd; color:#856404; border:1px solid #ffeeba; padding:10px 12px; border-radius:8px; margin-bottom:12px;">
                    <strong>There were some problems with your input.</strong>
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                        class="@error('first_name') input-error @enderror" required>
                    @error('first_name') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                        class="@error('last_name') input-error @enderror" required>
                    @error('last_name') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email"><span id="email-label-text">Email Address</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="@error('email') input-error @enderror" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <!-- Employer-only fields -->
            <div id="employer-fields" style="display:none;">
                <div class="form-group">
                    <label for="company_name">Company/Business Name</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" class="@error('company_name') input-error @enderror">
                    @error('company_name') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}" class="@error('job_title') input-error @enderror">
                    @error('job_title') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="business_permit">Company/Business Verification Document (Business Permit)</label>
                    <div id="permit-drop" class="upload-zone" onclick="document.getElementById('business_permit').click()"
                         ondragover="handlePermitDrag(event,true)" ondragleave="handlePermitDrag(event,false)" ondrop="handlePermitDrop(event)">
                        <div class="upload-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 16V4" stroke="#334A5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 9l5-5 5 5" stroke="#334A5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 16v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3" stroke="#334A5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="upload-text">
                            <strong>Click to upload</strong> or drag & drop<br>
                            <small>PDF, JPG, PNG (max 5 MB)</small>
                        </div>
                        <div id="permit-file-name" class="file-name">No file chosen</div>
                    </div>
                    <input type="file" name="business_permit" id="business_permit" accept=".pdf,.jpg,.jpeg,.png" style="display:none" onchange="handlePermitChange(event)" class="@error('business_permit') input-error @enderror">
                    @error('business_permit') <span class="error-text">{{ $message }}</span> @enderror
                    <span id="permit-error" class="error-text" style="display:none"></span>
                </div>
            </div>

            <!-- Job-seeker-only fields -->
            <div id="jobseeker-fields">
                <div class="form-row">
                <div class="form-group">
                    <label for="birthday">Date of Birth</label>
                    <input type="date" name="birthday" id="birthday" value="{{ old('birthday') }}"
                        class="@error('birthday') input-error @enderror">
                    @error('birthday') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                        class="@error('phone_number') input-error @enderror" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    @error('phone_number') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                </div>

                <div class="form-group">
                    <label for="education_level">Education Level</label>
                    <input type="text" name="education_level" id="education_level" value="{{ old('education_level') }}"
                        class="@error('education_level') input-error @enderror">
                    @error('education_level') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="skills">Skills (Comma separated)</label>
                    <input type="text" name="skills" id="skills" value="{{ old('skills') }}"
                        class="@error('skills') input-error @enderror">
                    @error('skills') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="years_of_experience">Years of Experience</label>
                    <input type="text" name="years_of_experience" id="years_of_experience"
                        value="{{ old('years_of_experience', '0') }}" class="@error('years_of_experience') input-error @enderror"
                        type="number" min="0">
                    @error('years_of_experience') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="position: relative;">
                    <label for="location">Location (Brgy in Mandaluyong)</label>
                    <select name="location" id="location" class="@error('location') input-error @enderror" required>
                    <option value="">Select your Location</option>
                    <option value="Addition Hills" {{ old('location') == 'Addition Hills' ? 'selected' : '' }}>Addition Hills
                    </option>
                    <option value="Bagong Silang" {{ old('location') == 'Bagong Silang' ? 'selected' : '' }}>Bagong Silang
                    </option>
                    <option value="Barangka Drive" {{ old('location') == 'Barangka Drive' ? 'selected' : '' }}>Barangka Drive
                    </option>
                    <option value="Barangka Ibaba" {{ old('location') == 'Barangka Ibaba' ? 'selected' : '' }}>Barangka Ibaba
                    </option>
                    <option value="Barangka Ilaya" {{ old('location') == 'Barangka Ilaya' ? 'selected' : '' }}>Barangka Ilaya
                    </option>
                    <option value="Barangka Itaas" {{ old('location') == 'Barangka Itaas' ? 'selected' : '' }}>Barangka Itaas
                    </option>
                    <option value="Buayang Bato" {{ old('location') == 'Buayang Bato' ? 'selected' : '' }}>Buayang Bato
                    </option>
                    <option value="Burol" {{ old('location') == 'Burol' ? 'selected' : '' }}>Burol</option>
                    <option value="Daang Bakal" {{ old('location') == 'Daang Bakal' ? 'selected' : '' }}>Daang Bakal</option>
                    <option value="Hagdang Bato Itaas" {{ old('location') == 'Hagdang Bato Itaas' ? 'selected' : '' }}>Hagdang
                        Bato Itaas</option>
                    <option value="Hagdang Bato Libis" {{ old('location') == 'Hagdang Bato Libis' ? 'selected' : '' }}>Hagdang
                        Bato Libis</option>
                    <option value="Harapin ang Bukas" {{ old('location') == 'Harapin ang Bukas' ? 'selected' : '' }}>Harapin
                        ang Bukas</option>
                    <option value="Highway Hills" {{ old('location') == 'Highway Hills' ? 'selected' : '' }}>Highway Hills
                    </option>
                    <option value="Mabini–J. Rizal" {{ old('location') == 'Mabini–J. Rizal' ? 'selected' : '' }}>Mabini–J.
                        Rizal</option>
                    <option value="Malamig" {{ old('location') == 'Malamig' ? 'selected' : '' }}>Malamig</option>
                    <option value="Mauway" {{ old('location') == 'Mauway' ? 'selected' : '' }}>Mauway</option>
                    <option value="Namayan" {{ old('location') == 'Namayan' ? 'selected' : '' }}>Namayan</option>
                    <option value="New Zañiga" {{ old('location') == 'New Zañiga' ? 'selected' : '' }}>New Zañiga</option>
                    <option value="Old Zañiga" {{ old('location') == 'Old Zañiga' ? 'selected' : '' }}>Old Zañiga</option>
                    <option value="Pag-Asa" {{ old('location') == 'Pag-Asa' ? 'selected' : '' }}>Pag-Asa</option>
                    <option value="Plainview" {{ old('location') == 'Plainview' ? 'selected' : '' }}>Plainview</option>
                    <option value="Pleasant Hills" {{ old('location') == 'Pleasant Hills' ? 'selected' : '' }}>Pleasant Hills
                    </option>
                    <option value="Poblacion" {{ old('location') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                    <option value="San Jose" {{ old('location') == 'San Jose' ? 'selected' : '' }}>San Jose</option>
                    <option value="Vergara" {{ old('location') == 'Vergara' ? 'selected' : '' }}>Vergara</option>
                    <option value="Wack-Wack Greenhills" {{ old('location') == 'Wack-Wack Greenhills' ? 'selected' : '' }}>
                        Wack-Wack Greenhills</option>
                    </select>
                    @error('location') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="@error('password') input-error @enderror"
                    required>
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="@error('password_confirmation') input-error @enderror" required>
                @error('password_confirmation') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="terms">
                <input type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy
                        Policy</a></label>
            </div>

            <button class="create-btn" type="submit">Create Account</button>

            <div class="bottom-text">
                <p>Already have an Account?</p>
                <a href="{{ route('login') }}">Sign In to Existing Account</a>
            </div>
        </form>
    </div>
    <script>
        function selectType(type) {
            const userTypeInput = document.getElementById('user_type');
            const btnJob = document.getElementById('btn-job');
            const btnEmp = document.getElementById('btn-emp');
            const jsFields = document.getElementById('jobseeker-fields');
            const empFields = document.getElementById('employer-fields');
            // Inputs to toggle required/disabled
            const phoneInput = document.getElementById('phone_number');
            const locInput = document.getElementById('location');
            const companyInput = document.getElementById('company_name');
            const permitInput = document.getElementById('business_permit');

            userTypeInput.value = type;
            if (type === 'employer') {
                btnEmp.style.background = '#fff';
                btnJob.style.background = 'transparent';
                jsFields.style.display = 'none';
                empFields.style.display = 'block';
                const el = document.getElementById('email-label-text');
                if (el) el.textContent = 'Work Email';

                // Disable job-seeker required fields so browser doesn't block submit
                if (phoneInput) { phoneInput.required = false; phoneInput.disabled = true; }
                if (locInput) { locInput.required = false; locInput.disabled = true; }
                // Ensure employer fields are enabled (HTML required handled server-side)
                if (companyInput) { companyInput.disabled = false; }
                if (permitInput) { permitInput.disabled = false; }
            } else {
                btnJob.style.background = '#fff';
                btnEmp.style.background = 'transparent';
                jsFields.style.display = 'block';
                empFields.style.display = 'none';
                const el = document.getElementById('email-label-text');
                if (el) el.textContent = 'Email Address';

                // Re-enable and require job-seeker fields
                if (phoneInput) { phoneInput.disabled = false; phoneInput.required = true; }
                if (locInput) { locInput.disabled = false; locInput.required = true; }
                // Optionally disable employer-only inputs when not used
                if (companyInput) { companyInput.disabled = true; }
                if (permitInput) { permitInput.disabled = true; }
            }
        }

        // Initialize based on old value
        (function() {
            const initial = '{{ old('user_type','job_seeker') }}';
            selectType(initial);
        })();

        // Custom upload handlers
        function handlePermitChange(e) {
            const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
            const nameEl = document.getElementById('permit-file-name');
            const errEl = document.getElementById('permit-error');
            errEl.style.display = 'none';
            errEl.textContent = '';
            if (!file) {
                nameEl.textContent = 'No file chosen';
                return;
            }
            // Client-side checks (types and size)
            const allowed = ['application/pdf','image/jpeg','image/png'];
            if (!allowed.includes(file.type)) {
                errEl.textContent = 'Invalid file type. Please upload a PDF, JPG, or PNG.';
                errEl.style.display = 'block';
                e.target.value = '';
                nameEl.textContent = 'No file chosen';
                return;
            }
            const max = 5 * 1024 * 1024; // 5MB
            if (file.size > max) {
                errEl.textContent = 'File is too large. Maximum size is 5 MB.';
                errEl.style.display = 'block';
                e.target.value = '';
                nameEl.textContent = 'No file chosen';
                return;
            }
            nameEl.textContent = file.name;
        }

        function handlePermitDrag(ev, isOver) {
            ev.preventDefault();
            const drop = document.getElementById('permit-drop');
            if (isOver) drop.classList.add('dragover'); else drop.classList.remove('dragover');
        }

        function handlePermitDrop(ev) {
            ev.preventDefault();
            const drop = document.getElementById('permit-drop');
            drop.classList.remove('dragover');
            const files = ev.dataTransfer.files;
            if (files && files.length > 0) {
                const input = document.getElementById('business_permit');
                input.files = files;
                // Trigger change for validation + filename update
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        }
    </script>
</body>

</html>