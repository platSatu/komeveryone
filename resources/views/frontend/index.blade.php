<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBI GATSU | KOMEVERYONE</title>
    
    <link rel="shortcut icon" href="{{ asset('frontend') }}/96.png">
    <!-- [UPDATE] Memanggil Google Fonts (Space Grotesk & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('frontend/style.css') }}" rel="stylesheet">
</head>
<body>

   @include('frontend.partials.nav')
   @include('frontend.partials.section_1')
   @include('frontend.partials.section_2')
   @include('frontend.partials.section_3')
   @include('frontend.partials.section_4')
   @include('frontend.partials.section_5')
   @include('frontend.partials.section_6')
   @include('frontend.partials.section_7')
   @include('frontend.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>