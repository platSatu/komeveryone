<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat | Kom Digital</title>
    <link rel="shortcut icon" href="{{ asset('home') }}/96.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            margin-top: 60px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }

        .certificate-info p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .header-text {
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 25px;
        }

        .footer-note {
            font-style: italic;
            font-size: 16px;
            text-align: center;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card border-primary">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="card-title">Verifikasi E-Sertifikat KOM EVERYONE</h4>
                    </div>

                    <div class="card-body certificate-info">
                        <div class="header-text">
                            DIVISI PENGAJARAN<br>
                            GBI Jl. JEND. GATOT SUBROTO, SENAYAN - JAKARTA<br>
                            <hr>
                            MENYATAKAN BAHWA:
                        </div>

                        <div class="table-responsive" style="max-width: 500px; margin: 0 auto;">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">No Sertifikat 100</th>
                                        <td>{{ $certificate_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Oleh</th>
                                        <td>Sub Divisi KOM</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Tanggal Pelaksanaan</th>
                                        <td>{{ $tanggalPelaksanaan }}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ $tanggalCetak }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="footer-note">
                            <strong> Adalah benar dan tercatat dalam sistem database kami.</strong>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 text-muted">
                    <small>&copy; {{ date('Y') }} Kom Digital GBIGATSU</small>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
