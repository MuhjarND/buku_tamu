<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Login Tidak Valid</title>
    @include('partials.app-icons')
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .panel {
            width: min(420px, calc(100% - 32px));
            padding: 28px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        h1 {
            margin: 0 0 12px;
            font-size: 22px;
            line-height: 1.25;
        }

        p {
            margin: 0 0 20px;
            color: #4b5563;
            line-height: 1.5;
        }

        a {
            display: inline-block;
            color: #ffffff;
            background: #2563eb;
            padding: 10px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Link login tidak valid</h1>
        <p>{{ $message }}</p>
        <a href="{{ route('login') }}">Kembali ke login</a>
    </main>
</body>
</html>
