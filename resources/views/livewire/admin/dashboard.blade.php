@section('page-title', $pageTitle)
@section('css')

@endsection
<div class="row">
    <div class="col-lg-12">
    <div class="card card-primary card-outline">
            <div class="card-body">
                <h5 class="card-title">Selamat Datang, Administrator!</h5>
                
                <p class="card-text">
                Terima kasih telah menggunakan aplikasi Pencatatan Pelanggaran Siswa.
                </p>
                <a href="{{ route('report.violation') }}" class="card-link">Lihat Laporan Pelanggaran</a>
                <a href="{{ route('report.rank.best') }}" class="card-link">Lihat Peringkat Siswa</a>
            </div>
        </div>
    </div>
</div>
@section('script')
<script>
</script>
@endsection