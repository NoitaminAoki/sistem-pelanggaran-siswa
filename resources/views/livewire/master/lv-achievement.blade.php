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
                    <h6 class="card-title mb-0">Daftar Prestasi </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button x-on:click="formType = 1" data-toggle="modal" data-target="#achievementModal" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Tambah Data
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tAchievement" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kode Prestasi</th>
                                    <th scope="col">Poin</th>
                                    <th scope="col">Deskripsi</th>
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
    <div wire:ignore.self class="modal fade" id="achievementModal" tabindex="-1" aria-labelledby="achievementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="achievementModalLabel" x-text="(formType == 1)? 'Tambah Pelanggaran' : 'Edit Pelanggaran'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendAchievement(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdAchievementDesc">Deskripsi</label>
                                    <input wire:model.defer="achievementDesc" type="text" name="achievementDesc" id="mdAchievementDesc" class="form-control {{($errors->has('achievementDesc'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('achievementDesc') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="achievementId" name="achievementId" id="mdAchievementId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdAchievementCode">Kode Prestasi</label>
                                    <input wire:model.defer="achievementCode" type="text" name="achievementCode" id="mdAchievementCode" class="form-control {{($errors->has('achievementCode'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('achievementCode') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdAchievementPoint">Poin</label>
                                    <input wire:model.defer="achievementPoint" type="number" name="achievementPoint" id="mdAchievementPoint" class="form-control {{($errors->has('achievementPoint'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('achievementPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdAcheivementNote">Catatan</label>
                                    <textarea wire:model.defer="achievementNote" name="achievementNote" id="mdAcheivementNote" class="form-control {{($errors->has('achievementNote'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('achievementNote') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendAchievement" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendAchievement" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendAchievement">Submit</span>
                            <span wire:loading wire:target="sendAchievement" style="display: none">Submitting...</span>
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
    var tAchievement;
    $(document).ready(function() {
        tAchievement = $('#tAchievement').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('master.achievement.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'kode_prestasi', name: 'kode_prestasi' },
            { data: 'poin_prestasi', name: 'poin_prestasi' },
            { data: 'deskripsi', name: 'deskripsi' },
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
    
    function editAchievement(e) {
        let self = e.currentTarget
        let id = self.getAttribute('data-id')
        $('#achievementModal').modal()
        @this.setAchievement(id)
    }

    function deleteAchievement(e) {
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
                    @this.deleteAchievement(id)
                }, 300);
            }
        })
    }
    
    $("#achievementModal").on("hidden.bs.modal", function () {
        @this.resetInput();
    });
</script>
@endsection