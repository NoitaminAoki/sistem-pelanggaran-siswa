@section('page-title', $pageTitle)
@section('css')
<!-- datatables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="card-title mb-0">Daftar Pelanggaran ({{Auth::guard('studentUser')->user()->name}}) </h6>
                </div>
                <div class="card-body">
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tStdVio" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Pembuat Laporan</th>
                                    <th scope="col">Pelanggaran</th>
                                    <th scope="col">Jenis</th>
                                    <th scope="col">Catatan</th>
                                    <th scope="col">Tindakan</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@section('script')
<!-- date-range-picker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    var tStdVio;
    $(document).ready(function() {
        tStdVio = $('#tStdVio').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('student.record.violation.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'nama_guru', name: 'nama_guru' },
            { data: 'nama_pelanggaran', name: 'nama_pelanggaran' },
            { data: 'jenis_pelanggaran', name: 'jenis_pelanggaran' },
            { data: 'catatan', name: 'catatan' },
            { data: 'created_at', name: 'created_at' },
            ],
            columnDefs: [
            { targets: 5, orderable: false },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
    })
</script>
@stack('child-script')
@endsection