<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Document</title>
    {{-- <link rel="stylesheet" href="{{public_path('/css/bootstrap-5/bootstrap.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{asset('/css/bootstrap-5/bootstrap.min.css') }}"> --}}
    <style>
        body {
            margin: 0;
        }
        
        .text-title {
            font-size: 24px;
            font-weight: 700;
            color: #2F3692;
        }
        .text-sub-title {
            font-size: 12px;
            font-weight: 700;
        }
        
        .text-desc {
            font-size: 12px;
            font-weight: 500;
        }
        
        .text-caption {
            font-size: 14px;
            font-weight: 300;
        }

        .text-caption-start {
            font-size: 18px;
        }
        .bg-yellow {
            background-color: #FFFF00 !important;
        }
        
        .image-logo {
            position: absolute;
            top: 2rem;
            left: 20px;
            width: 90px;
            height: 90px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            caption-side: bottom;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: .2rem;
        }
        
        .cs-table-border tbody, .cs-table-border td, .cs-table-border tfoot, .cs-table-border th, .cs-table-border thead, .cs-table-border tr {
            border: solid 1px #000000;
        }
        
        .text-center {
            text-align: center !important;
        }
        .align-middle {
            vertical-align: middle !important;
        }

        .mt-4 {
            margin-bottom: 2rem;
        }
        .mb-2 {
            margin-bottom: 1rem;
        }

        .border-0 {
            border-width: 0 !important;
        }
    </style>
</head>
<body>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-center border-0">
                    <div style="position: relative">
                        {{-- <img class="image-logo" src="{{ public_path('images/logo-smkn-2-cibinong.png') }}" alt="logo"> --}}
                        <img class="image-logo" src="{{ Storage::path('images/logo-smkn-2-cibinong.png') }}" alt="logo">
                    </div>
                    <h5 class="text-title mt-4 mb-2">SMK NEGERI 2 CIBINONG</h5>
                    <p class="text-sub-title">
                        Jl. SKB No.1, Karadenan, Kec. Cibinong<br>
                        Kabupaten Bogor, Jawa Barat 16913<br>
                        Telp: (0251) 8582276
                    </p>
                    <p class="text-caption mb-0 py-0">
                    <b class="text-caption-start">Laporan Sanksi Siswa</b><br>
                        Tanggal: {{$startDate}} - {{$endDate}}
                    </p>
                </th>
            </tr>
        </thead>
    </table>
    <table class="table table-bordered cs-table-border text-desc" width="100%">
        <thead> 
            <tr>
                <th scope="col" class="text-center align-middle bg-yellow" style="width: 30px;">No</th>
                <th scope="col" class="text-center align-middle bg-yellow">Pelapor</th>
                <th scope="col" class="text-center align-middle bg-yellow">NIS Siswa</th>
                <th scope="col" class="text-center align-middle bg-yellow">Nama Siswa</th>
                <th scope="col" class="text-center align-middle bg-yellow">Sanksi</th>
                <th scope="col" class="text-center align-middle bg-yellow">Jenis Sanksi</th>
                <th scope="col" class="text-center align-middle bg-yellow">Total Poin<br>(saat dikenakan sanksi)</th>
                <th scope="col" class="text-center align-middle bg-yellow" style="width: 150px;">Tanggal Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
            <tr>
                <td class="text-center">{{$index + 1}}</td>
                <td> {{$item->nama_guru ?? 'Administrator'}} </td>
                <td> {{$item->nis}} </td>
                <td> {{$item->nama_siswa}} </td>
                <td> {{$item->nama_sanksi}} </td>
                <td> {{$item->jenis_sanksi}} </td>
                <td class="text-center"> {{$item->poin_awal}} </td>
                <td class="text-center"> {{$item->created_at->setTimezone('Asia/Jakarta')->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('d F Y H:i \W\I\B')}} </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="border-0">
            <tr class="border-0">
                <td colspan="6" class="border-0"></td>
                <td colspan="2" class="border-0">
                    <p class="mt-4 w-100 text-center">
                        Bogor, {{Carbon\Carbon::now()->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("l d F Y")}}<br>
                        Mengetahui,<br>
                        Kepala Sekolah<br>
                        <br><br><br><br>
                        Solihin Al Amin M.Pd
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>