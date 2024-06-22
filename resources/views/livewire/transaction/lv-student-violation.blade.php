@section('page-title', $pageTitle)
@section('top-css')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('css')
<!-- datatables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

<div x-data="{ pageType: @entangle('viewPageType').defer, formType: @entangle('viewFormType').defer }">
    <div class="row">
        <div x-show="pageType == 2" x-cloak class="col-md-12">
            <div class="card border card-purple">
                <div class="card-header">
                    <h6 class="card-title mb-0">Buat Laporan Pelanggaran </h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        
                    </div>
                </div>
            </div>
        </div>
        <div x-show="pageType == 1" class="col-md-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="card-title mb-0">Daftar Pelanggaran Siswa </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button x-on:click="pageType = 2" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Buat Laporan
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tStudentViolation" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Pembuat Laporan</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Pelanggaran</th>
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
    
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="studentViolationModal" tabindex="-1" aria-labelledby="studentViolationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentViolationModalLabel" x-text="(formType == 1)? 'Tambah Pelanggaran' : 'Edit Pelanggaran'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendStudentViolation(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdStudentViolationDesc">Deskripsi</label>
                                    <input wire:model.defer="studentViolationDesc" type="text" name="studentViolationDesc" id="mdStudentViolationDesc" class="form-control {{($errors->has('studentViolationDesc'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('studentViolationDesc') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="studentViolationId" name="studentViolationId" id="mdStudentViolationId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdStudentViolationCode">Kode Prestasi</label>
                                    <input wire:model.defer="studentViolationCode" type="text" name="studentViolationCode" id="mdStudentViolationCode" class="form-control {{($errors->has('studentViolationCode'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('studentViolationCode') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdStudentViolationPoint">Poin</label>
                                    <input wire:model.defer="studentViolationPoint" type="number" name="studentViolationPoint" id="mdStudentViolationPoint" class="form-control {{($errors->has('studentViolationPoint'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('studentViolationPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdAcheivementNote">Catatan</label>
                                    <textarea wire:model.defer="studentViolationNote" name="studentViolationNote" id="mdAcheivementNote" class="form-control {{($errors->has('studentViolationNote'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('studentViolationNote') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendStudentViolation" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendStudentViolation" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendStudentViolation">Submit</span>
                            <span wire:loading wire:target="sendStudentViolation" style="display: none">Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>

@section('script')
<!-- date-range-picker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    var tStudentViolation;
    $(document).ready(function() {
        tStudentViolation = $('#tStudentViolation').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('record.student.violation.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'nama_guru', name: 'nama_guru' },
            { data: 'nama_siswa', name: 'nama_siswa' },
            { data: 'nama_pelanggaran', name: 'nama_pelanggaran' },
            { data: 'catatan', name: 'catatan' },
            { data: 'action', name: 'action', className: 'text-center' },
            ],
            columnDefs: [
            { targets: 5, orderable: false },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
    })
</script>
@endsection