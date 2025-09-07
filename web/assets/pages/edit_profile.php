<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        <?php
        global $user;
        ?>.container {
            max-width: 100%;
            margin: auto;
            border: 1px solid #10b981;
            border-radius: 12px;
            padding: 0px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
            overflow: hidden;
            margin-top: 90px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            background: linear-gradient(135deg, #10b981, #059669);
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f0fdf4;
            border-radius: 12px;
            margin: 20px;
            border: 1px solid #dcfce7;
        }
        .profile-section .image-preview {
            width: 150px;
            height: 150px;
            border: 3px dashed #10b981;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);
        }
        .profile-section .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-section button {
            margin-top: 15px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }
        .profile-section button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
        }
        .form-section {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            padding: 20px 30px;
        }
        .form-section .input-group {
            flex: 1 1 calc(50% - 25px);
            display: flex;
            flex-direction: column;
        }
        .form-section .input-group label {
            font-size: 16px;
            color: #047857;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-section .input-group input {
            padding: 12px 15px;
            border: 1px solid #d1fae5;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f0fdf4;
            transition: all 0.3s ease;
        }
        .form-section .input-group input:focus {
            border-color: #10b981;
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .form-section .input-group input:disabled {
            background-color: #f9fafb;
            color: #6b7280;
        }
        .gender {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-top: 15px;
            padding: 15px;
            background-color: #f0fdf4;
            border-radius: 12px;
            border: 1px solid #dcfce7;
        }
        .gender label {
            font-size: 16px;
            color: #047857;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .gender-options {
            display: flex;
            gap: 20px;
        }
        .gender-options label {
            display: flex;
            align-items: center;
            font-size: 15px;
            color: #374151;
            font-weight: normal;
            cursor: pointer;
        }
        .gender-options input[type="radio"] {
            margin-right: 8px;
            accent-color: #10b981;
        }
        .form-actions {
            margin-top: 30px;
            text-align: center;
            padding: 0 30px 30px;
        }
        .form-actions button {
            padding: 14px 30px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }
        .form-actions button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
        }
        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 5px;
        }
        .success-message {
            color: #10b981;
            text-align: center;
            font-weight: 600;
            padding: 10px;
            margin: 15px 20px 0;
            background-color: #f0fdf4;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }
    </style>

    <div class="container">
        <div class="header">
            <i class="fas fa-user-md mr-2"></i> Edit HealthLink Profile
        </div>
        <?php
        if (isset($_GET['success'])) {
        ?>
            <div class="success-message">
                <i class="fas fa-check-circle mr-2"></i>Profile Updated Successfully!
            </div>
        <?php
        }
        ?>
        <form method="post" action="assets/php/actions.php?updateprofile" enctype="multipart/form-data">
            <div class="profile-section">
                <div class="image-preview">
                    <img src="assets/img/profile/<?= $user['profile_pic'] ?>" name="profile_pic" alt="Profile Preview" id="profilePreview">
                </div>
                <!-- Add an external button to trigger file input -->
                <button type="button" onclick="triggerFileInput()">
                    <i class="fas fa-camera mr-2"></i>Change Profile Photo
                </button>
                <input type="file" id="profileInput" name="profile_pic" accept="image/*" style="display: none;">
                <?= showError('profile_pic') ?>
            </div>

            <div class="form-section">
                <div class="input-group">
                    <label for="firstName">
                        <i class="fas fa-user mr-2"></i>First Name
                    </label>
                    <input name="first_name" value="<?= $user['first_name']; ?>" type="text" id="firstName" placeholder="Enter your first name">
                    <?= showError('first_name') ?>
                </div>
                <div class="input-group">
                    <label for="lastName">
                        <i class="fas fa-user mr-2"></i>Last Name
                    </label>
                    <input name="last_name" value="<?= $user['last_name']; ?>" type="text" id="lastName" placeholder="Enter your last name">
                    <?= showError('last_name') ?>
                </div>
                <div class="gender">
                    <label>
                        <i class="fas fa-venus-mars mr-2"></i>Choose Your Gender:
                    </label>
                    <div class="gender-options">
                        <label><input type="radio" <?= $user['gender'] == 1 ? 'checked' : '' ?> disabled name="gender" id="male"> <i class="fas fa-mars mr-1"></i> Male</label>
                        <label><input type="radio" <?= $user['gender'] == 2 ? 'checked' : '' ?> disabled name="gender" id="female"> <i class="fas fa-venus mr-1"></i> Female</label>
                        <label><input type="radio" <?= $user['gender'] == 0 ? 'checked' : '' ?> disabled name="gender" id="other"> <i class="fas fa-genderless mr-1"></i> Other</label>
                    </div>
                </div>
                <div class="input-group">
                    <label for="email">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input name="email" value="<?= $user['email']; ?>" type="email" id="email" placeholder="Enter your email" disabled>
                </div>
                <div class="input-group">
                    <label for="username">
                        <i class="fas fa-at mr-2"></i>Username
                    </label>
                    <input value="<?= $user['username']; ?>" name="username" type="text" id="username" placeholder="Enter your username">
                    <?= showError('username') ?>
                </div>
                <div class="input-group">
                    <label for="password">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input name="password" type="password" id="password" placeholder="Enter your password">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit">
                    <i class="fas fa-save mr-2"></i>Update Profile
                </button>
            </div>
        </form>
    </div>
    <script>
        const profileInput = document.getElementById('profileInput');
        const profilePreview = document.getElementById('profilePreview');
        function triggerFileInput() {
            profileInput.click();
        }
        profileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    profilePreview.src = reader.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</div>