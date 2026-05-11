<?php

namespace App\Http\Controllers;

use App\Imports\PenjualanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $import = new PenjualanImport();
            Excel::import($import, $request->file('file'));

            $pesan = "Import berhasil! {$import->imported} data dimasukkan, {$import->skipped} dilewati.";

            if (!empty($import->errors)) {
                session()->flash('import_errors', $import->errors);
            }

            return redirect()->route('dashboard')->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        \App\Models\Penjualan::truncate();
        return redirect()->route('dashboard')->with('success', 'Seluruh data berhasil dihapus. Sistem siap menerima dataset baru.');
    }
}
