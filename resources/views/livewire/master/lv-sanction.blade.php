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
                    <h6 class="card-title mb-0">Daftar Sanksi </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button x-on:click="formType = 1" data-toggle="modal" data-target="#sanctionModal" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Tambah Data
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tSanction" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode Sanksi</th>
                                    <th scope="col">Jenis</th>
                                    <th scope="col">Deskripsi</th>
                                    <th scope="col">Catatan</th>
                                    <th scope="col">Poin Minimum</th>
                                    <th scope="col">Poin Batas</th>
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
    <div wire:ignore.self class="modal fade" id="sanctionModal" tabindex="-1" aria-labelledby="sanctionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sanctionModalLabel" x-text="(formType == 1)? 'Tambah Pelanggaran' : 'Edit Pelanggaran'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendSanction(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdSanctionDesc">Deskripsi</label>
                                    <input wire:model.defer="sanctionDesc" type="text" name="sanctionDesc" id="mdSanctionDesc" class="form-control {{($errors->has('sanctionDesc'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('sanctionDesc') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="sanctionId" name="sanctionId" id="mdSanctionId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdSanctionCode">Kode Sanksi</label>
                                    <input wire:model.defer="sanctionCode" type="text" name="sanctionCode" id="mdSanctionCode" class="form-control {{($errors->has('sanctionCode'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('sanctionCode') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdSanctionType">Jenis</label>
                                    <select wire:model.defer="sanctionType" name="sanctionType" id="mdSanctionType" class="form-control {{($errors->has('sanctionType'))? 'is-invalid' : ''}}" required>
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Ringan">Ringan</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Berat">Berat</option>
                                    </select>
                                    @error('sanctionType') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdSanctionPointMin">Poin Minimum</label>
                                    <input wire:model.defer="sanctionPointMin" type="number" name="sanctionPointMin" id="mdSanctionPointMin" class="form-control {{($errors->has('sanctionPointMin'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('sanctionPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdSanctionPointLimit">Poin Batas</label>
                                    <input wire:model.defer="sanctionPointLimit" type="number" name="sanctionPointLimit" id="mdSanctionPointLimit" class="form-control {{($errors->has('sanctionPointLimit'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('sanctionPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdSanctionNote">Catatan</label>
                                    <textarea wire:model.defer="sanctionNote" name="sanctionNote" id="mdSanctionNote" class="form-control {{($errors->has('sanctionNote'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('sanctionNote') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendSanction" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendSanction" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendSanction">Submit</span>
                            <span wire:loading wire:target="sendSanction" style="display: none">Submitting...</span>
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
    var tSanction;
    $(document).ready(function() {
        tSanction = $('#tSanction').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('master.law.sanction.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'kode_sanksi', name: 'kode_sanksi' },
            { data: 'jenis', name: 'jenis' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'catatan', name: 'catatan' },
            { data: 'poin_minimum', name: 'poin_minimum' },
            { data: 'poin_batasan', name: 'poin_batasan' },
            { data: 'action', name: 'action', className: 'text-center' },
            ],
            columnDefs: [
            { targets: 7, orderable: false },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
    })
    
    function editSanction(e) {
        let self = e.currentTarget
        let id = self.getAttribute('data-id')
        $('#sanctionModal').modal()
        @this.setSanction(id)
    }

    function deleteSanction(e) {
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
                    @this.deleteSanction(id)
                }, 300);
            }
        })
    }
    
    $("#sanctionModal").on("hidden.bs.modal", function () {
        @this.resetInput();
    });
</script>
@endsection