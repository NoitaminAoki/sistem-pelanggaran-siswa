<?php

namespace App\Http\Livewire\Components;

use App\Helpers\StringHelper;
use App\Models\Master\Sanction;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvModalSanction extends Component
{
    public $action;
    public $modalId;
    public $loadingState = 0;

    public function render()
    {
        return view('livewire.components.lv-modal-sanction');
    }

    public function setSanctionId($id)
    {
        $this->emitUp($this->action, $id);
        $this->loadingState = 0;
        $this->dispatchBrowserEvent('component-modal:close', ['target' => "#{$this->modalId}"]);
    }

    public function dtSanction(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = Sanction::query();

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('kode_sanksi', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'pick' => ["dataId" => $row->id, 'action' => 'loadingState = 1; $wire.setSanctionId(' . $row->id . ')', 'loadingTarget' => 'setSanctionId'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'kode_sanksi',
                'jenis',
                'deskripsi',
                'poin_minimum',
                'poin_batasan',
                'action'
            ])
            ->toJson();
    }
}
