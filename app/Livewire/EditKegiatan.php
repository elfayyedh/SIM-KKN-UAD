<?php

namespace App\Livewire;

use App\Models\Kegiatan;
use Livewire\Component;

class EditKegiatan extends Component
{
    public $kegiatanId;
    public $nama;
    public $frekuensi;
    public $jkem;
    public $total_jkem;

    protected $listeners = ['getKegiatan' => 'showModal'];

    public function showModal($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $this->kegiatanId = $kegiatan->id;
        $this->nama = $kegiatan->nama;
        $this->frekuensi = $kegiatan->frekuensi;
        $this->jkem = $kegiatan->jkem;
        $this->total_jkem = $kegiatan->total_jkem;

        $this->dispatchBrowserEvent('openModal');
    }

    public function updateKegiatan()
    {
        $this->validate([
            'nama' => 'required|string',
            'frekuensi' => 'required|integer',
            'jkem' => 'required|string',
            'total_jkem' => 'required|integer',
        ]);

        $kegiatan = Kegiatan::findOrFail($this->kegiatanId);
        $kegiatan->nama = $this->nama;
        $kegiatan->frekuensi = $this->frekuensi;
        $kegiatan->jkem = $this->jkem;
        $kegiatan->total_jkem = $this->total_jkem;
        $kegiatan->save();

        $this->dispatchBrowserEvent('closeModal');
        $this->emit('kegiatanUpdated');
    }

    public function render()
    {
        return view('livewire.edit-kegiatan');
    }
}