<?php

namespace App\Http\Livewire\Master;

use App\Helpers\StringHelper;
use App\Models\Master\Violation;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvViolation extends Component
{
    public $violationId;
    public $violationCode;
    public $violationType = "";
    public $violationName;
    public $violationPoint;
    public $violationCategory;

    public $viewFormType = 1;

    private $validationRules = [
        'violationCode' => 'required',
        'violationType' => 'required',
        'violationName' => 'required',
        'violationPoint' => 'required|integer',
        'violationCategory' => 'required',
    ];

    public function render()
    {
        return view('livewire.master.lv-violation')
            ->with(['pageTitle' => "Master Violation"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'master_law']);
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
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editViolation($event);'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteViolation'],
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

    public function setViolation(int $id)
    {
        if (!$id) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "No ID supplied!",
                'icon' => 'error',
            ]);
        }

        $violation = Violation::where('id', $id)->firstOrFail();
        $this->viewFormType = 2;
        $this->violationCode = $violation->kode_pelanggaran;
        $this->violationType = $violation->jenis;
        $this->violationName = $violation->nama_pelanggaran;
        $this->violationPoint = $violation->bobot_poin;
        $this->violationCategory = $violation->kategori;
    }

    public function sendViolation(int $formType)
    {
        if ($formType == 1) {
            $this->addViolation();
        } else {
            $this->updateViolation();
        }
    }

    public function addViolation()
    {
        $this->validate($this->validationRules);

        try {
            $create = Violation::create([
                'kode_pelanggaran' => $this->violationCode,
                'jenis' => $this->violationType,
                'nama_pelanggaran' => $this->violationName,
                'bobot_poin' => $this->violationPoint,
                'kategori' => $this->violationCategory,
            ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#violationModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#violationModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateViolation()
    {
        $this->validate($this->validationRules);
        $violation = Violation::where('id', $this->violationId)->firstOrFail();

        try {
            $update = Violation::where('id', $this->violationId)
                ->update([
                    'kode_pelanggaran' => $this->violationCode,
                    'jenis' => $this->violationType,
                    'nama_pelanggaran' => $this->violationName,
                    'bobot_poin' => $this->violationPoint,
                    'kategori' => $this->violationCategory,
                ]);


            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui data!',
                'icon' => 'success',
                'close_modal' => true,
                'target' => '#violationModal',
            ]);
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui data! (Error: {$ex->getMessage()})",
                'icon' => 'error',
                'close_modal' => true,
                'target' => '#violationModal',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function deleteViolation($id)
    {
        $violation = Violation::where('id', $id)->first();

        if (!$violation)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus data!",
                'icon' => 'error',
            ]);

        $delete = Violation::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus data!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'violationCode',
            'violationName',
            'violationPoint',
            'violationCategory',
        ]);
        $this->viewFormType = 1;
        $this->violationType = "";
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tViolation"]);
    }
}
