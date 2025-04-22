<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ration Shop Slot Booking - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6">Ration Shop Slot Booking</h1>
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Login Type</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="login_type" value="user" class="mr-2" checked>
                        <span>User Login</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="login_type" value="dealer" class="mr-2">
                        <span>Dealer Login</span>
                    </label>
                </div>
            </div>
            <div>
                <label for="identifier" class="block text-sm font-medium text-gray-700">Ration Card Number / Username</label>
                <input type="text" name="identifier" id="identifier" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Login</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            <a href="#" onclick="showForgotPasswordModal()" class="text-blue-600 hover:underline">Forgot Password?</a>
        </p>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Forgot Password</h2>
            <p class="text-gray-700 mb-4">Please visit your nearest MeeSeva center or contact your dealer to update your password.</p>
            <button onclick="closeForgotPasswordModal()" class="bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Close</button>
        </div>
    </div>

    <script>
        function showForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
        }
    </script>
</body>
</html>