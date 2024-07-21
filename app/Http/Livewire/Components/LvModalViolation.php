<?php

namespace App\Http\Livewire\Components;

use App\Helpers\StringHelper;
use App\Models\Master\Violation;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvModalViolation extends Component
{
    public $action;
    public $modalId;
    public $loadingState = 0;

    public function render()
    {
        return view('livewire.components.lv-modal-violation');
    }

    public function setViolationId($id)
    {
        $this->emitUp($this->action, $id);
        $this->loadingState = 0;
        $this->dispatchBrowserEvent('component-modal:close', ['target' => "#{$this->modalId}"]);
    }

    public function dtViolation(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = Violation::query();

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('kode_pelanggaran', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'pick' => ["dataId" => $row->id, 'action' => 'loadingState = 1; $wire.setViolationId(' . $row->id . ')', 'loadingTarget' => 'setViolationId'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'kode_pelanggaran',
                'jenis',
                'nama_pelanggaran',
                'bobot_poin',
                'kategori',
                'action'
            ])
            ->toJson();
    }
}
