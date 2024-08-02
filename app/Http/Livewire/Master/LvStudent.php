<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;
use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\StudentUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class LvStudent extends Component
{
    public $studentId;
    public $nis;
    public $fullname;
    public $gender = "";
    public $birthPlace;
    public $birthDate;
    public $address;

    public $viewFormType = 1;

    protected $messages = [
        'nis.required' => 'NIS Siswa harus diisi.',
        'nis.integer' => 'NIS Siswa yang diisi tidak valid.',
        'fullname.required' => 'Nama Siswa harus diisi.',
        'gender.required' => 'Jenis Kelamin harus dipilih.',
        'birthPlace.required' => 'Tempat Lahir harus diisi.',
        'birthDate.required' => 'Tanggal Lahir harus diisi.',
        'address.required' => 'Alamat harus diisi.',
    ];

    public function render()
    {
        return view('livewire.master.lv-student')
            ->with(['pageTitle' => "Master Student"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'master_student']);
    }

    public function dtStudent(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = Student::query();

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('nama_siswa', 'asc')
                    ->orderBy('nis', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editStudent($event);'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteStudent'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nis',
                'nama_siswa',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'alamat',
                'action'
            ])
            ->toJson();
    }

    public function setStudent(int $id)
    {
        if (!$id) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "No ID supplied!",
                'icon' => 'error',
            ]);
        }

        $student = Student::where('id', $id)->firstOrFail();
        $this->viewFormType = 2;
        $this->nis = $student->nis;
        $this->fullname = $student->nama_siswa;
        $this->gender = $student->jenis_kelamin;
        $this->birthPlace = $student->tempat_lahir;
        $this->birthDate = $student->tanggal_lahir;
        $this->address = $student->alamat;
    }

    public function sendStudent(int $formType)
    {
        if ($formType == 1) {
            $this->addStudent();
        } else {
            $this->updateStudent();
        }
    }

    public function addStudent()
    {
        $this->validate([
            'nis' => 'required|integer',
            'fullname' => 'required',
            'gender' => 'required',
            'birthPlace' => 'required',
            'birthDate' => 'required',
            'address' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $create = Student::create([
                'nis' => $this->nis,
                'nama_siswa' => $this->fullname,
                'jenis_kelamin' => $this->gender,
                'tempat_lahir' => $this->birthPlace,
                'tanggal_lahir' => $this->birthDate,
                'alamat' => $this->address,
            ]);

            $studentUser = new StudentUser();
            $studentUser->username = $this->nis;
            $studentUser->name = $this->fullname;
            $studentUser->email_verified_at = Carbon::now();
            $studentUser->student_id = $create->id;
            $studentUser->password = Hash::make(Carbon::createFromFormat('Y-m-d', $this->birthDate)->format('dmY'));
            $studentUser->save();


            DB::commit();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#studentModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#studentModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateStudent()
    {
        $this->validate([
            'nis' => 'required|integer',
            'fullname' => 'required',
            'gender' => 'required',
            'birthPlace' => 'required',
            'birthDate' => 'required',
            'address' => 'required',
        ]);
        $student = Student::where('id', $this->studentId)->firstOrFail();

        try {
            $update = Student::where('id', $this->studentId)
                ->update([
                    'nis' => $this->nis,
                    'nama_siswa' => $this->fullname,
                    'jenis_kelamin' => $this->gender,
                    'tempat_lahir' => $this->birthPlace,
                    'tanggal_lahir' => $this->birthDate,
                    'alamat' => $this->address,
                ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#studentModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#studentModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function deleteStudent($id)
    {
        $student = Student::where('id', $id)->first();

        if (!$student)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data!",
                'icon' => 'error',
            ]);

        DB::beginTransaction();
        try {
            $delete = Student::where('id', $id)->delete();
            $deleteAccount = StudentUser::where('student_id', $id)->delete();

            DB::commit();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menghapus data!',
                'icon' => 'success',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#studentModal',
            ]);
        }
    }

    public function resetInput()
    {
        $this->reset([
            'nis',
            'fullname',
            'birthPlace',
            'birthDate',
            'address',
        ]);
        $this->viewFormType = 1;
        $this->gender = "";
    }

    // Tidak Jadi Dipakai, Untuk Custom Query
    // public function dtStudent2(Request $request)
    // {
    //     $search = StringHelper::escapeLike($request->input('search.value') ?? '');
    //     $pageSize = ($request->length) ? $request->length : 10;
    //     $start = ($request->start) ? $request->start : 0;

    //     $query = Student::orderBy('nama_siswa', 'asc')
    //         ->orderBy('nis', 'asc');

    //     $queryTotal = clone $query;
    //     $queryFiltered = 0;

    //     if ($search != '') {
    //         $query->where('nis', 'LIKE', '%' . $search . '%')
    //             ->orWhere('nama_siswa', 'LIKE', '%' . $search . '%')
    //             ->orWhere('jenis_kelamin', 'LIKE', '%' . $search . '%')
    //             ->orWhere('tempat_lahir', 'LIKE', '%' . $search . '%')
    //             ->orWhere('tanggal_lahir', 'LIKE', '%' . $search . '%')
    //             ->orWhere('alamat', 'LIKE', '%' . $search . '%');
    //         $queryFiltered = clone $query;
    //     }


    //     $items = $query->skip($start)->take($pageSize)->get();

    //     $totalItems = $queryTotal->count();

    //     $totalFilteredItems = ($queryFiltered) ? $queryFiltered->count() : $totalItems;
    //     return DataTables::make($items)
    //         ->with([
    //             "recordsTotal" => $totalItems,
    //             "recordsFiltered" => $totalFilteredItems,
    //         ])
    //         ->addIndexColumn()
    //         ->addColumn('action', function ($row) {
    //             $actions = [
    //                 'edit' => ["dataId" => $row->id],
    //                 'detail' => ["dataId" => $row->id],
    //                 'delete' => ["dataId" => $row->id],
    //             ];

    //             return view('components.standard-button-datatables', $actions);
    //         })
    //         ->rawColumns(['action'])
    //         ->toJson();
    // }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStudent"]);
    }
}
