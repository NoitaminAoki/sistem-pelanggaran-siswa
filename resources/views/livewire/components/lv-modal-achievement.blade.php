<div x-data="{loadingState: @entangle('loadingState').defer}">
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="{{$modalId}}" tabindex="-1" aria-labelledby="{{$modalId}}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{$modalId}}Label">Data Prestasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tChildDtAchievement" width="100%">
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
                <div x-show="loadingState == 1" x-cloak class="inside-modal-overlay loading">
                    <i class="fas fa-2x fa-sync fa-spin" style="animation-duration: 1s;"></i>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:target="sendAchievement" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>

@push('child-script')
<script>
    $(document).ready(function() {
        var tChildDtAchievement;
        tChildDtAchievement = $('#tChildDtAchievement').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('component.achievement.datatables') }}",
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
</script>
@endpush