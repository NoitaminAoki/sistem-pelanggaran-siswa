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
            <div class="row">
                
                <div class="col-md-12">
                    <div class="card border card-purple">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Buat Laporan Pelanggaran </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <h3 class="card-title">Data Siswa</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" title="Mark as read">
                                                    <i class="far fa-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body pb-0">
                                            <div class="form-group">
                                                <label>Nama Siswa</label>
                                                <div wire:ignore>
                                                    <select data-action="setInputStudent" data-wire-model="stdVioNis" class="form-control select2" id="selectStudent" style="width: 100%;" required>
                                                        <option value="" disabled selected>Pilih siswa</option>
                                                    </select>
                                                </div>
                                                @error('stdVioNis') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="card-footer card-comments">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Jenis Kelamin
                                                        </span>
                                                        <span class="text-muted">{{ $selected['student']['gender'] ?? '-' }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Tanggal Lahir
                                                        </span>
                                                        <span class="text-muted">{{ $selected['student']['formattedDate'] ?? '-' }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-12">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Alamat
                                                        </span>
                                                        <span class="text-muted">{{ $selected['student']['address'] ?? '-' }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <h3 class="card-title">Data Pelanggaran</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" title="Mark as read">
                                                    <i class="far fa-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body pb-0">
                                            <div class="form-group">
                                                <label>Pelanggaran</label>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 flex-grow-1">
                                                        <input type="hidden" id="mdViolationCode" class="form-control" wire:model.defer="selected.violation.code">
                                                        <input type="text" id="mdViolationName" class="form-control" placeholder="Pilih pelanggaran" value="{{$selected['violation']['name']}}" readonly>
                                                    </div>
                                                    <button class="btn bg-gradient-navy" data-toggle="modal" data-target="#modalViolation"><i class="fas fa-search"></i></button>
                                                    
                                                </div>
                                                @error('stdVioNis') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="card-footer card-comments">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Kode
                                                        </span>
                                                        <span class="text-muted">{{$selected['violation']['code'] ?? '-'}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Jenis
                                                        </span>
                                                        <span class="text-muted">{{$selected['violation']['type'] ?? '-'}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Bobot Poin
                                                        </span>
                                                        <span class="text-muted">{{$selected['violation']['point'] ?? '-'}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            Kategori
                                                        </span>
                                                        <span class="text-muted">{{$selected['violation']['category'] ?? '-'}}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="mdStdVioNote">Catatan</label>
                                        <textarea wire:model.defer="stdVioNote" name="stdVioNote" id="mdStdVioNote" class="form-control {{($errors->has('stdVioNote'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('stdVioNote') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="w-100">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light btn-sm" type="button" wire:loading.attr="disabled" @click="pageType = 1">Cancel</button>
                                    <button class="btn btn-primary btn-sm" type="button" wire:loading.attr="disabled">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div x-show="pageType == 1" x-cloak class="col-md-12">
            <div class="card border">
                <div class="card-header">
                    <h6 class="card-title mb-0">Daftar Pelanggaran Siswa </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button @click="pageType = 2" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Buat Laporan
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tStdVio" width="100%">
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
    <livewire:components.lv-modal-violation action="setInputViolation" modal-id="modalViolation">
    <!-- End Modal -->

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="stdVioModal" tabindex="-1" aria-labelledby="stdVioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stdVioModalLabel" x-text="(formType == 1)? 'Tambah Pelanggaran' : 'Edit Pelanggaran'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendStdVio(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdStdVioDesc">Deskripsi</label>
                                    <input wire:model.defer="stdVioDesc" type="text" name="stdVioDesc" id="mdStdVioDesc" class="form-control {{($errors->has('stdVioDesc'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('stdVioDesc') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="stdVioId" name="stdVioId" id="mdStdVioId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdStdVioCode">Kode Prestasi</label>
                                    <input wire:model.defer="stdVioCode" type="text" name="stdVioCode" id="mdStdVioCode" class="form-control {{($errors->has('stdVioCode'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('stdVioCode') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdStdVioPoint">Poin</label>
                                    <input wire:model.defer="stdVioPoint" type="number" name="stdVioPoint" id="mdStdVioPoint" class="form-control {{($errors->has('stdVioPoint'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('stdVioPoint') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdAcheivementNote">Catatan</label>
                                    <textarea wire:model.defer="stdVioNote" name="stdVioNote" id="mdAcheivementNote" class="form-control {{($errors->has('stdVioNote'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('stdVioNote') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendStdVio" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendStdVio" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendStdVio">Submit</span>
                            <span wire:loading wire:target="sendStdVio" style="display: none">Submitting...</span>
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
    $(".select2").on("change", function (e) {
        let action = $(this).attr("data-action");
        let model = $(this).attr("data-wire-model");
        let id = $(this).select2("val");
        if (id) {
            if (action) {
                @this.emit(action, id);
            } else if (model) {
                @this.set(model, id);
            }
        }
    });
</script>
<script>
    var tStdVio;
    var selectStudent;
    $(document).ready(function() {
        tStdVio = $('#tStdVio').DataTable({
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
        
        $("#selectStudent").select2({
            placeholders: 'Select your option',
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('record.student.violation.select2') }}",
                dataType: 'json',
                type: "GET",
                delay: 600,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.nama_siswa} (${item.nis})`,
                                id: item.nis
                            }
                        })
                    };
                }
            }
        });
    })
</script>
@stack('child-script')
@endsection