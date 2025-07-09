<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; }
        body { 
            background-image: url('{{ $background_url }}');
            background-size: cover;
            font-family: 'Times New Roman', serif;
        }
        .content-wrapper {
            position: absolute;
            top: 40%; /* Sesuaikan posisi vertikal */
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 80%;
        }
        /* Styling lainnya untuk h1, p, dll. */
    </style>
</head>
<body>
    <div class="content-wrapper">
        {!! $content !!}
    </div>
</body>
</html>