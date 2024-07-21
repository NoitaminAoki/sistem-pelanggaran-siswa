<div x-data="{loadingState: @entangle('loadingState').defer}">
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="{{$modalId}}" tabindex="-1" aria-labelledby="{{$modalId}}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{$modalId}}Label">Data Pelanggaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tChildDtViolation" width="100%">
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
                <div x-show="loadingState == 1" x-cloak class="inside-modal-overlay loading">
                    <i class="fas fa-2x fa-sync fa-spin" style="animation-duration: 1s;"></i>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:target="sendViolation" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>

@push('child-script')
<script>
    $(document).ready(function() {
        var tChildDtViolation;
        tChildDtViolation = $('#tChildDtViolation').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('component.violation.datatables') }}",
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
</script>
@endpush