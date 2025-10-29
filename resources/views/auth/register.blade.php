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
    </style>
</head>

<body>
    <div class="form-container">
        <div class="header">
            <h2>CREATE ACCOUNT?</h2>
        </div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

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
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="@error('email') input-error @enderror" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="@error('date_of_birth') input-error @enderror" required>
                    @error('date_of_birth') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                        class="@error('phone_number') input-error @enderror" required>
                    @error('phone_number') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="education_level">Education Level</label>
                <input type="text" name="education_level" id="education_level" value="{{ old('education_level') }}"
                    class="@error('education_level') input-error @enderror" required>
                @error('education_level') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="skills">Skills (Comma separated)</label>
                <input type="text" name="skills" id="skills" value="{{ old('skills') }}"
                    class="@error('skills') input-error @enderror" required>
                @error('skills') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="years_of_experience">Years of Experience</label>
                <input type="text" name="years_of_experience" id="years_of_experience"
                    value="{{ old('years_of_experience') }}" class="@error('years_of_experience') input-error @enderror"
                    required>
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

            <div class="form-group">
                <label for="user_type">Account Type</label>
                <select name="user_type" id="user_type" class="@error('user_type') input-error @enderror" required>
                    <option value="job_seeker" {{ old('user_type') == 'job_seeker' ? 'selected' : '' }}>Job Seeker</option>
                    <option value="employer" {{ old('user_type') == 'employer' ? 'selected' : '' }}>Employer</option>
                </select>
                @error('user_type') <span class="error-text">{{ $message }}</span> @enderror
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
</body>

</html>