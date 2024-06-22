@section('page-title', $pageTitle)
@section('top-css')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('css')
<!-- datatables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

<div x-data="{ formType: @entangle('viewFormType').defer }">
    <div class="row">
        <div class="col-md-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="card-title mb-0">Daftar Pelanggaran </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button x-on:click="formType = 1" data-toggle="modal" data-target="#violationModal" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Tambah Data
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tViolation" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode Pelanggaran</th>
                                    <th scope="col">Jenis</th>
                                    <th scope="col">Pelanggaran</th>
                                    <th scope="col">Bobot Poin</th>
                                    <th scope="col">Kategori</th>
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
    <div wire:ignore.self class="modal fade" id="violationModal" tabindex="-1" aria-labelledby="violationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="violationModalLabel" x-text="(formType == 1)? 'Tambah Pelanggaran' : 'Edit Pelanggaran'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendViolation(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdViolationName">Pelanggaran</label>
                                    <input wire:model.defer="violationName" type="text" name="violationName" id="mdViolationName" class="form-control {{($errors->has('violationName'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('violationName') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="violationId" name="violationId" id="mdViolationId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdViolationCode">Kode Pelanggaran</label>
                                    <input wire:model.defer="violationCode" type="text" name="violationCode" id="mdViolationCode" class="form-control {{($errors->has('violationCode'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('violationCode') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdViolationPoint">Bobot Poin</label>
                                    <input wire:model.defer="violationPoint" type="number" name="violationPoint" id="mdViolationPoint" class="form-control {{($errors->has('violationPoint'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('violationPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdViolationType">Jenis</label>
                                    <select wire:model.defer="violationType" name="violationType" id="mdViolationType" class="form-control {{($errors->has('violationType'))? 'is-invalid' : ''}}" required>
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Ringan">Ringan</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Berat">Berat</option>
                                    </select>
                                    @error('violationType') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdViolationCategory">Kategori</label>
                                    <select wire:model.defer="violationCategory" name="violationCategory" id="mdViolationCategory" class="form-control {{($errors->has('violationCategory'))? 'is-invalid' : ''}}" required>
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Akademik">Akademik</option>
                                        <option value="Kedisiplinan">Kedisiplinan</option>
                                    </select>
                                    @error('violationCategory') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendViolation" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendViolation" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendViolation">Submit</span>
                            <span wire:loading wire:target="sendViolation" style="display: none">Submitting...</span>
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
    var tViolation;
    $(document).ready(function() {
        tViolation = $('#tViolation').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('master.law.violation.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'kode_pelanggaran', name: 'kode_pelanggaran' },
            { data: 'jenis', name: 'jenis' },
            { data: 'nama_pelanggaran', name: 'nama_pelanggaran' },
            { data: 'bobot_poin', name: 'bobot_poin' },
            { data: 'kategori', name: 'kategori' },
            { data: 'action', name: 'action', className: 'text-center' },
            ],
            columnDefs: [
            { targets: 6, orderable: false },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
    })
    
    function editViolation(e) {
        let self = e.currentTarget
        let id = self.getAttribute('data-id')
        $('#violationModal').modal()
        @this.setViolation(id)
    }

    function deleteViolation(e) {
        let self = e.currentTarget
        let id = self.getAttribute('data-id')

        swalBsButtons.fire({
            title: 'Anda yakin ingin menghapus data?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                swalLoader.fire()
                setTimeout(() => {
                    @this.deleteViolation(id)
                }, 300);
            }
        })
    }
    
    $("#violationModal").on("hidden.bs.modal", function () {
        @this.resetInput();
    });
</script>
@endsection