<?php

namespace App\Http\Livewire\Components;

use App\Helpers\StringHelper;
use App\Models\Master\Achievement;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvModalAchievement extends Component
{
    public $action;
    public $modalId;
    public $loadingState = 0;

    public function render()
    {
        return view('livewire.components.lv-modal-achievement');
    }

    public function setAchievementId($id)
    {
        $this->emitUp($this->action, $id);
        $this->loadingState = 0;
        $this->dispatchBrowserEvent('component-modal:close', ['target' => "#{$this->modalId}"]);
    }

    public function dtAchievement(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = Achievement::query();

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('kode_prestasi', 'asc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'pick' => ["dataId" => $row->id, 'action' => 'loadingState = 1; $wire.setAchievementId(' . $row->id . ')', 'loadingTarget' => 'setAchievementId'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'kode_prestasi',
                'poin_prestasi',
                'deskripsi',
                'catatan',
                'action'
            ])
            ->toJson();
    }
}
