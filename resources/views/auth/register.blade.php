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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h2 {
            font-size: 32px;
            font-weight: 400;
            margin: 0;
        }

        .radio-group {
            display: flex;
            gap: 30px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-option input[type="radio"] {
            width: 14px;
            height: 14px;
            accent-color: #334A5E;
        }

        label {
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            color: #1B1B1B;
            margin-bottom: 6px;
            display: block;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        input {
            width: 100%;
            height: 38px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.34);
            background: #D3D3D3;
            padding: 0 10px;
            box-sizing: border-box;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
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
        }

        .terms a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
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
            transition: 0.3s;
        }

        .create-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .bottom-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #000;
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
            transition: 0.3s;
        }

        .bottom-text a:hover {
            background-color: #f0f0f0;
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
                    <input type="text" name="first_name" id="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" required>
                </div>
            </div>

            <div class="form-group">
                <label for="education_level">Education Level</label>
                <input type="text" name="education_level" id="education_level" required>
            </div>

            <div class="form-group">
                <label for="skills">Skills (Comma separated)</label>
                <input type="text" name="skills" id="skills" required>
            </div>

            <div class="form-group">
                <label for="years_of_experience">Years of Experience</label>
                <input type="text" name="years_of_experience" id="years_of_experience" required>
            </div>

            <div class="form-group" style="position: relative;">
                <label for="location">Location (Brgy in Mandaluyong)</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <select name="location" id="location" required
                        style="
                            width: 100%;
                            height: 38px;
                            border-radius: 8px;
                            border: 1px solid rgba(0, 0, 0, 0.34);
                            background: #D3D3D3;
                            padding: 0 35px 0 10px;
                            box-sizing: border-box;
                            font-size: 13px;
                            font-family: 'Roboto', sans-serif;
                            color: #000;
                            appearance: none;
                            cursor: pointer;
                        ">
                        <option value="">Select your Location</option>
                        <option value="Addition Hills">Addition Hills</option>
                        <option value="Bagong Silang">Bagong Silang</option>
                        <option value="Barangka Drive">Barangka Drive</option>
                        <option value="Barangka Ibaba">Barangka Ibaba</option>
                        <option value="Barangka Ilaya">Barangka Ilaya</option>
                        <option value="Barangka Itaas">Barangka Itaas</option>
                        <option value="Buayang Bato">Buayang Bato</option>
                        <option value="Burol">Burol</option>
                        <option value="Daang Bakal">Daang Bakal</option>
                        <option value="Hagdang Bato Itaas">Hagdang Bato Itaas</option>
                        <option value="Hagdang Bato Libis">Hagdang Bato Libis</option>
                        <option value="Harapin ang Bukas">Harapin ang Bukas</option>
                        <option value="Highway Hills">Highway Hills</option>
                        <option value="Mabini–J. Rizal">Mabini–J. Rizal</option>
                        <option value="Malamig">Malamig</option>
                        <option value="Mauway">Mauway</option>
                        <option value="Namayan">Namayan</option>
                        <option value="New Zañiga">New Zañiga</option>
                        <option value="Old Zañiga">Old Zañiga</option>
                        <option value="Pag-Asa">Pag-Asa</option>
                        <option value="Plainview">Plainview</option>
                        <option value="Pleasant Hills">Pleasant Hills</option>
                        <option value="Poblacion">Poblacion</option>
                        <option value="San Jose">San Jose</option>
                        <option value="Vergara">Vergara</option>
                        <option value="Wack-Wack Greenhills">Wack-Wack Greenhills</option>
                    </select>
                    <!-- ▼ Location Icon -->
                    <span style="
                        position: absolute;
                        right: 10px;
                        pointer-events: none;
                        color: #555;
                        font-size: 16px;
                    ">▼</span>
                </div>
            </div>

            <div class="form-group">
                <label for="user_type">Account Type</label>
                <select name="user_type" id="user_type" required>
                    <option value="job_seeker" selected>Job Seeker</option>
                    <option value="employer">Employer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <div class="terms">
                <input type="checkbox" name="terms" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
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
