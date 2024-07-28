<?php

namespace App\Http\Livewire\Components;

use App\Helpers\StringHelper;
use App\Models\Master\Student;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvModalStudent extends Component
{
    public $action;
    public $modalId;
    public $loadingState = 0;

    public function render()
    {
        return view('livewire.components.lv-modal-student');
    }

    public function setStudentId($id)
    {
        $this->emitUp($this->action, $id);
        $this->loadingState = 0;
        $this->dispatchBrowserEvent('component-modal:close', ['target' => "#{$this->modalId}"]);
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
                $query->orderBy('nama_siswa', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('formatted_ttl', function ($row) {
                return "{$row->tempat_lahir}, {$row->tanggal_lahir->format('d F Y')}";
            })
            ->addColumn('action', function ($row) {
                $actions = [
                    'pick' => ["dataId" => $row->id, 'action' => 'loadingState = 1; $wire.setStudentId(' . $row->id . ')', 'loadingTarget' => 'setStudentId'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nis',
                'nama_siswa',
                'formatted_ttl',
                'alamat',
                'action'
            ])
            ->toJson();
    }
}
