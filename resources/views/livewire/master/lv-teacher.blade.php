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
                    <h6 class="card-title mb-0">Daftar Guru </h6>
                </div>
                <div class="card-body">
                    <div class="w-100 mb-4 text-right">
                        <button x-on:click="formType = 1" data-toggle="modal" data-target="#teacherModal" class="btn btn-outline-primary btn-sm shadow-none">
                            <span class="fas fa-plus-circle"></span> Tambah Data
                        </button>
                    </div>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tTeacher" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">NIP</th>
                                    <th scope="col">Nama Lengkap</th>
                                    <th scope="col">Jenis Kelamin</th>
                                    <th scope="col">Tempat Lahir</th>
                                    <th scope="col">Tanggal Lahir</th>
                                    <th scope="col">Agama</th>
                                    <th scope="col">Alamat</th>
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
    <div wire:ignore.self class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="aTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aTeacherModalLabel" x-text="(formType == 1)? 'Tambah Guru' : 'Edit Guru'"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form x-on:submit.prevent="$wire.sendTeacher(formType)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <template x-if="formType == 2">
                                    <div class="form-group d-none">
                                        <input type="hidden" wire:model.defer="teacherId" name="teacherId" id="mdTeacherId">
                                    </div>
                                </template>
                                <div class="form-group">
                                    <label for="mdNip">NIP Guru</label>
                                    <input wire:model.defer="nip" type="text" name="nip" id="mdNip" class="form-control {{($errors->has('nip'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('nip') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdName">Nama Lengkap</label>
                                    <input wire:model.defer="fullname" type="text" name="fullname" id="mdName" class="form-control {{($errors->has('fullname'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('fullname') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdGender">Jenis Kelamin</label>
                                    <select wire:model.defer="gender" name="gender" id="mdGender" class="form-control {{($errors->has('gender'))? 'is-invalid' : ''}}" required>
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('gender') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div wire:ignore class="form-group">
                                    <label for="mdReligion">Agama</label>
                                    <select data-wire-model="religion" name="religion" id="mdReligion" class="form-control select2" style="width: 100%;" required>
                                        <option value="" disabled selected>Select your option</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen ">Kristen </option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('religion') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdBirthPlace">Tempat Lahir</label>
                                    <input wire:model.defer="birthPlace" type="text" name="birthPlace" id="mdBirthPlace" class="form-control {{($errors->has('birthPlace'))? 'is-invalid' : ''}}" autocomplete="off" required>
                                    @error('birthPlace') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mdBirthDate">Tanggal Lahir</label>
                                    <input wire:model.defer="birthDate" type="date" name="birthDate" id="mdBirthDate" class="form-control {{($errors->has('birthDate'))? 'is-invalid' : ''}}" required>
                                    @error('birthDate') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mdAddress">Alamat</label>
                                    <textarea wire:model.defer="address" name="address" id="mdAddress" class="form-control {{($errors->has('address'))? 'is-invalid' : ''}}" rows="3" required></textarea>
                                    @error('address') <span class="text-sm text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:target="sendTeacher" wire:loading.attr="disabled" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" wire:target="sendTeacher" class="btn btn-sm btn-primary">
                            <span wire:loading.remove wire:target="sendTeacher">Submit</span>
                            <span wire:loading wire:target="sendTeacher" style="display: none">Submitting...</span>
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
    var tTeacher;
    $(document).ready(function() {
        tTeacher = $('#tTeacher').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('master.teacher.datatables') }}",
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'nip', name: 'nip' },
            { data: 'nama_guru', name: 'nama_guru' },
            { data: 'jenis_kelamin', name: 'jenis_kelamin' },
            { data: 'tempat_lahir', name: 'tempat_lahir' },
            { data: 'tanggal_lahir', name: 'tanggal_lahir' },
            { data: 'agama', name: 'agama' },
            { data: 'alamat', name: 'alamat' },
            { data: 'action', name: 'action', className: 'text-center' },
            ],
            columnDefs: [
            { targets: 8, orderable: false },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
        $('.select2').select2({placeholder: 'Select your option', minimumResultsForSearch: -1})
    })
    
    function editTeacher(e) {
        let self = e.currentTarget
        let id = self.getAttribute('data-id')
        $('#teacherModal').modal()
        @this.setTeacher(id)
    }
    
    function deleteTeacher(e) {
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
                    @this.deleteTeacher(id)
                }, 300);
            }
        })
    }
    
    $("#teacherModal").on("hidden.bs.modal", function () {
        @this.resetInput();
    });
</script>
@endsection