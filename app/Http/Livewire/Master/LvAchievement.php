<?php

namespace App\Http\Livewire\Master;

use App\Helpers\StringHelper;
use App\Models\Master\Achievement;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvAchievement extends Component
{
    public $achievementId;
    public $achievementCode;
    public $achievementDesc;
    public $achievementPoint;
    public $achievementNote;

    public $viewFormType = 1;

    private $validationRules = [
        'achievementCode' => 'required',
        'achievementDesc' => 'required',
        'achievementPoint' => 'required|integer',
        'achievementNote' => 'required',
    ];

    public function render()
    {
        return view('livewire.master.lv-achievement')
            ->with(['pageTitle' => "Master Achievement"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'master_achievement']);
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
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editAchievement($event);'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteAchievement'],
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

    public function setAchievement(int $id)
    {
        if (!$id) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "No ID supplied!",
                'icon' => 'error',
            ]);
        }

        $achievement = Achievement::where('id', $id)->firstOrFail();
        $this->viewFormType = 2;
        $this->achievementCode = $achievement->kode_prestasi;
        $this->achievementDesc = $achievement->deskripsi;
        $this->achievementPoint = $achievement->poin_prestasi;
        $this->achievementNote = $achievement->catatan;
    }

    public function sendAchievement(int $formType)
    {
        if ($formType == 1) {
            $this->addAchievement();
        } else {
            $this->updateAchievement();
        }
    }

    public function addAchievement()
    {
        $this->validate($this->validationRules);

        try {
            $create = Achievement::create([
                'kode_prestasi' => $this->achievementCode,
                'deskripsi' => $this->achievementDesc,
                'poin_prestasi' => $this->achievementPoint,
                'catatan' => $this->achievementNote,
            ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#achievementModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#achievementModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateAchievement()
    {
        $this->validate($this->validationRules);
        $achievement = Achievement::where('id', $this->achievementId)->firstOrFail();

        try {
            $update = Achievement::where('id', $this->achievementId)
                ->update([
                    'kode_prestasi' => $this->achievementCode,
                    'deskripsi' => $this->achievementDesc,
                    'poin_prestasi' => $this->achievementPoint,
                    'catatan' => $this->achievementNote,
                ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#achievementModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#achievementModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function deleteAchievement($id)
    {
        $achievement = Achievement::where('id', $id)->first();

        if (!$achievement)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data!",
                'icon' => 'error',
            ]);

        $delete = Achievement::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus data!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'achievementCode',
            'achievementDesc',
            'achievementPoint',
            'achievementNote',
        ]);
        $this->viewFormType = 1;
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tAchievement"]);
    }
}
