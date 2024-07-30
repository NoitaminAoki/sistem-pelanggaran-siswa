@section('page-title', $pageTitle)
@section('top-css')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('css')
<!-- datatables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<style>
    .btn-menu-width {
        width: 150px;
    }
    
    .form-title-header {
        display: flex;
        align-items: center;
        margin-bottom: .85rem;
    }
    .form-title-header > .header-icon {
        width: 30px;
        height: 30px;
        background-color: #ECECFF;
        color: #6969FF;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: .5rem;
        font-size: 16px;
    }
    .form-title-header > .header-title h5 {
        font-family: "Nunito Sans", sans-serif;
        color: #00003D;
        font-weight: 800;
        margin: 0;
        font-size: 16px;
    }
    
    .form-box-content {
        width: 100%;
        border: solid 2px #E8E8F0;
        border-radius: 10px;
        padding: 1rem 0.8rem;
    }
    
    .form-btn-light {
        font-family: "Nunito Sans", sans-serif;
        font-weight: 700;
        background-color: #ffffff;
        border: solid 2px #E8E8F0;
        color: #03033F;
        border-radius: 6px;
    }
    .form-btn-light:hover {
        color: #1f2d3d;
        background-color: #e2e6ea;
        border-color: #dae0e5;
    }
    .form-btn-light:focus {
        color: #1f2d3d;
        background-color: #e2e6ea;
        border-color: #dae0e5;
        box-shadow: 0 0 0 0 rgba(215, 218, 222, .5);
    }
    .form-btn-light:not(:disabled):not(.disabled).active, .form-btn-light:not(:disabled):not(.disabled):active, .show>.form-btn-light.dropdown-toggle {
        color: #1f2d3d;
        background-color: #dae0e5;
        border-color: #d3d9df;
    }
    
    .form-btn-light:not(:disabled):not(.disabled):active:focus, .show>.form-btn-light.dropdown-toggle:focus {
        box-shadow: 0 0 0 0 rgba(215, 218, 222, .5);
    }
    .form-btn {
        font-size: 15px;
        line-height: 1.55;
    }
    .form-btn i:first-child {
        margin-right: .3rem;
    }
    
    .mb-4-p5 {
        margin-bottom: 2rem;
    }
    
    .form-group-text, .form-description {
        font-family: "Nunito Sans", sans-serif;
        color: #00003D;
    }
    .form-description dt, .form-description dd {
        padding-top: .8rem;
        padding-bottom: .8rem;
    }
    .form-description dd {
        color: #A3A3B5;
    }
</style>
@endsection

<div x-data="{ filters: @entangle('filters') }">
    <div class="row">
        <div class="col-md-12">
            <div class="card border">
                <div class="card-body">
                    <h5>Filter Laporan</h5>
                    <hr>
                    <div class="row mb-5">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-12 mb-4-p5">
                                    <div class="form-title-header">
                                        <div class="header-icon">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div class="header-title">
                                            <h5>Jangka Waktu</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Date range -->
                                            <div class="form-group form-group-text">
                                                <label for="mdStartDate">Tanggal Awal:</label>
                                                
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input x-model="filters.startDate" type="text" class="form-control float-right datepicker" id="mdStartDate">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                            <!-- /.form group -->
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Date range -->
                                            <div class="form-group form-group-text">
                                                <label for="mdEndDate">Tanggal Akhir:</label>
                                                
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input x-model="filters.endDate" type="text" class="form-control float-right datepicker" id="mdEndDate">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                            <!-- /.form group -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-4-p5">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" wire:click="resetFilter" class="btn btn-light px-5 mr-2">Reset</button>
                                        <button type="button" wire:click="dtRpAchievementFilter" class="btn btn-primary px-5">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-12 mb-4-p5">
                                    <div class="form-title-header">
                                        <div class="header-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="header-title">
                                            <h5>Unduh Dokumen Laporan</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="form-box-content">
                                        <button type="button" @click="downloadExcel" wire:loading.attr="disabled" class="btn px-4 bg-teal"><i class="fas fa-file-excel mr-1"></i> Unduh Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5>Data Laporan</h5>
                    <hr>
                    <div wire:ignore class="table-responsive">
                        <table class="table table-striped table-bordered" id="tStdReport" width="100%">
                            <thead> 
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">NIS</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Total Prestasi</th>
                                    <th scope="col">Total Poin</th>
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
<!-- moment -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- date-range-picker -->
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
    $('.datepicker').on('change', function(ev) {
        ev.currentTarget.dispatchEvent(new Event('input'));
    });
</script>
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
    var tStdReport;
    var reportFilters = @json($filters);
    
    $(document).ready(function() {
        tStdReport = $('#tStdReport').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('report.achievement.summary.datatables') }}",
                data: function (d) {
                    d.filters = reportFilters;
                },
                type: "POST"
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id' },
            { data: 'nis', name: 'nis' },
            { data: 'nama_siswa', name: 'nama_siswa' },
            { data: 'total_prestasi', name: 'total_prestasi' },
            { data: 'total_poin', name: 'total_poin' },
            ],
            columnDefs: [
            { targets: [3, 4], className: 'text-center' },
            ],
        }).on('draw', function() {
            console.info("Datatables: drawed");
        });
        
        //Date range picker
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD MMMM YYYY'
            }
        })
    })
    
    function downloadExcel() {
        swalLoader.fire()
        @this.downloadExcel();
    }
</script>
@stack('child-script')
@endsection