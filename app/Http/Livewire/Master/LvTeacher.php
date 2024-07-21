<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;
use App\Helpers\StringHelper;
use App\Models\Admin;
use App\Models\Master\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class LvTeacher extends Component
{
    public $teacherId;
    public $nip;
    public $fullname;
    public $gender = "";
    public $birthPlace;
    public $birthDate;
    public $religion;
    public $address;

    public $viewFormType = 1;

    public function render()
    {
        return view('livewire.master.lv-teacher')
            ->with(['pageTitle' => "Master Teacher"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'master_teacher']);
    }

    public function dtTeacher(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = Teacher::query();

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('nama_guru', 'asc')
                    ->orderBy('nip', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editTeacher($event);'],
                    'detail' => ["dataId" => $row->id, 'action' => 'showTeacher'],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteTeacher'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nip',
                'nama_guru',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'alamat',
                'action'
            ])
            ->toJson();
    }

    public function setTeacher(int $id)
    {
        if (!$id) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "No ID supplied!",
                'icon' => 'error',
            ]);
        }

        $teacher = Teacher::where('id', $id)->firstOrFail();
        $this->viewFormType = 2;
        $this->nip = $teacher->nip;
        $this->fullname = $teacher->nama_guru;
        $this->gender = $teacher->jenis_kelamin;
        $this->birthPlace = $teacher->tempat_lahir;
        $this->birthDate = $teacher->tanggal_lahir;
        $this->address = $teacher->alamat;
        $this->religion = $teacher->agama;
        $this->dispatchBrowserEvent('select2:set-value', ['target' => "#mdReligion", 'value' => $this->religion]);
    }

    public function sendTeacher(int $formType)
    {
        if ($formType == 1) {
            $this->addTeacher();
        } else {
            $this->updateTeacher();
        }
    }

    public function addTeacher()
    {
        $this->validate([
            'nip' => 'required|integer',
            'fullname' => 'required',
            'gender' => 'required',
            'birthPlace' => 'required',
            'birthDate' => 'required',
            'address' => 'required',
            'religion' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $created_teacher = Teacher::create([
                'nip' => $this->nip,
                'nama_guru' => $this->fullname,
                'jenis_kelamin' => $this->gender,
                'tempat_lahir' => $this->birthPlace,
                'tanggal_lahir' => $this->birthDate,
                'alamat' => $this->address,
                'agama' => $this->religion,
            ]);

            $admin = new Admin();
            $admin->username = $this->nip;
            $admin->name = $this->fullname;
            $admin->email_verified_at = Carbon::now();
            $admin->is_teacher = 1;
            $admin->teacher_id = $created_teacher->id;
            $admin->password = Hash::make(Carbon::createFromFormat('Y-m-d', $this->birthDate)->format('dmY'));
            $admin->save();

            DB::commit();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#teacherModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#teacherModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateTeacher()
    {
        $this->validate([
            'nip' => 'required|integer',
            'fullname' => 'required',
            'gender' => 'required',
            'birthPlace' => 'required',
            'birthDate' => 'required',
            'address' => 'required',
            'religion' => 'required',
        ]);
        $teacher = Teacher::where('id', $this->teacherId)->firstOrFail();

        try {
            $update = Teacher::where('id', $this->teacherId)
                ->update([
                    'nip' => $this->nip,
                    'nama_guru' => $this->fullname,
                    'jenis_kelamin' => $this->gender,
                    'tempat_lahir' => $this->birthPlace,
                    'tanggal_lahir' => $this->birthDate,
                    'alamat' => $this->address,
                    'agama' => $this->religion,
                ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#teacherModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#teacherModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function changePassword()
    {
    }

    public function deleteTeacher($id)
    {
        $teacher = Teacher::where('id', $id)->first();

        if (!$teacher)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data!",
                'icon' => 'error',
            ]);

        DB::beginTransaction();
        try {
            $delete = Teacher::where('id', $id)->delete();
            $deleteAccount = Admin::where('teacher_id', $id)->delete();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menghapus data!',
                'icon' => 'success',
            ]);
            DB::commit();
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#teacherModal',
            ]);
        }
    }

    public function resetInput()
    {
        $this->reset([
            'nip',
            'fullname',
            'birthPlace',
            'birthDate',
            'address',
        ]);
        $this->viewFormType = 1;
        $this->gender = "";
        $this->religion = "";
        $this->dispatchBrowserEvent('select2:reset-value', ['target' => ".select2"]);
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tTeacher"]);
    }
}
