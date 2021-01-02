<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Reset Your Password</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap">

  <!-- Styles -->
  <style>
    *,*::before,*::after {
      box-sizing: border-box;
    }
    html, body {
      font-family: 'Inter UI var', 'Inter UI', sans-serif;
      font-size: 16px;
      height: 100vh;
      margin: 0;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    .container {
      width: 100%;
      max-width: 90vw;
      margin: 0 auto;
    }

    h1 {
      color: #4b5563;
    }

    input {
      -webkit-appearance: none;
      appearance: none;
      transition: border-color 150ms ease;
      border: 2px solid #e5e7eb;
      border-radius: 0.25rem;
      color: #374151;
      height: 3rem;
      margin-top: 16px;
      outline: none;
      padding: 0 0.75rem;

    }

    input:focus {
      border-color: #f56565;
    }

    button {
      background-color: #e02424;
      border-radius: 0.25rem;
      border: none;
      color: #fff;
      cursor: pointer;
      font-weight: 700;
      letter-spacing: 0.1em;
      margin-top: 16px;
      padding: 0.75rem 0;
      text-transform: uppercase;
    }

    button:focus {
      outline: 0;
      box-shadow: 0 0 0 3px rgba(248, 180, 180, 0.45);
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Reset Your Password</h1>
    <form action="/reset-password" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ request('email') }}">
      <input type="password" name="password" placeholder="Enter a new password">
      <input type="password" name="password_confirmation" placeholder="Confirm password">
      <button type="submit">Reset Password</button>
    </form>
  </div>
</body>
</html>
