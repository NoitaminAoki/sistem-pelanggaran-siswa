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
                    <h6 class="card-title mb-0">Daftar Sanksi ({{Auth::guard('studentUser')->user()->name}}) </h6>
                </div>
                <div class="card-body">
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tStdSanc" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Pembuat Laporan</th>
                                    <th scope="col">Sanksi</th>
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
    var tStdSanc;
    $(document).ready(function() {
        tStdSanc = $('#tStdSanc').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('student.record.sanction.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'nama_guru', name: 'nama_guru' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'jenis_sanksi', name: 'jenis_sanksi' },
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