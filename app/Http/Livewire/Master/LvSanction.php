<?php

namespace App\Http\Livewire\Master;

use App\Helpers\StringHelper;
use App\Models\Master\Sanction;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvSanction extends Component
{
    public $sanctionId;
    public $sanctionCode;
    public $sanctionType = "";
    public $sanctionDesc;
    public $sanctionPointMin;
    public $sanctionPointLimit;
    public $sanctionNote;

    public $viewFormType = 1;

    private $validationRules = [
        'sanctionCode' => 'required',
        'sanctionType' => 'required',
        'sanctionDesc' => 'required',
        'sanctionPointMin' => 'required|integer',
        'sanctionPointLimit' => 'required|integer',
        'sanctionNote' => 'required',
    ];

    public function render()
    {
        return view('livewire.master.lv-sanction')
            ->with(['pageTitle' => "Master Sanction"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'master_law']);
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
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editSanction($event);'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteSanction'],
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
                'catatan',
                'poin_minimum',
                'poin_batasan',
                'action'
            ])
            ->toJson();
    }

    public function setSanction(int $id)
    {
        if (!$id) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "No ID supplied!",
                'icon' => 'error',
            ]);
        }

        $sanction = Sanction::where('id', $id)->firstOrFail();
        $this->viewFormType = 2;
        $this->sanctionCode = $sanction->kode_pelanggaran;
        $this->sanctionType = $sanction->jenis;
        $this->sanctionDesc = $sanction->nama_pelanggaran;
        $this->sanctionPointMin = $sanction->bobot_poin;
        $this->sanctionNote = $sanction->kategori;
    }

    public function sendSanction(int $formType)
    {
        if ($formType == 1) {
            $this->addSanction();
        } else {
            $this->updateSanction();
        }
    }

    public function addSanction()
    {
        $this->validate($this->validationRules);

        try {
            $create = Sanction::create([
                'kode_sanksi' => $this->sanctionCode,
                'jenis' => $this->sanctionType,
                'deskripsi' => $this->sanctionDesc,
                'catatan' => $this->sanctionNote,
                'poin_minimum' => $this->sanctionPointMin,
                'poin_batasan' => $this->sanctionPointLimit,
            ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#sanctionModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#sanctionModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateSanction()
    {
        $this->validate($this->validationRules);
        $sanction = Sanction::where('id', $this->sanctionId)->firstOrFail();

        try {
            $update = Sanction::where('id', $this->sanctionId)
                ->update([
                    'kode_sanksi' => $this->sanctionCode,
                    'jenis' => $this->sanctionType,
                    'deskripsi' => $this->sanctionDesc,
                    'catatan' => $this->sanctionNote,
                    'poin_minimum' => $this->sanctionPointMin,
                    'poin_batasan' => $this->sanctionPointLimit,
                ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#sanctionModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#sanctionModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function deleteSanction($id)
    {
        $sanction = Sanction::where('id', $id)->first();

        if (!$sanction)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data!",
                'icon' => 'error',
            ]);

        $delete = Sanction::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus data!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'sanctionCode',
            'sanctionDesc',
            'sanctionPointMin',
            'sanctionPointLimit',
            'sanctionNote',
        ]);
        $this->viewFormType = 1;
        $this->sanctionType = "";
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tSanction"]);
    }
}
