<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    // Cek Akses Role (OPSIONAL - lebih baik pakai middleware/policy)
    public function __construct()
    {
        $this->middleware('auth');
        // ... (Cek role di sini jika tidak menggunakan middleware)
    }

    // Tampilkan daftar meja
    public function index()
    {
        $tables = Table::all();
        return view('admin.tables.index', compact('tables'));
    }

    // Buat data meja baru dan QR Code
    public function store(Request $request)
    {
        $request->validate(['table_number' => 'required|string|unique:tables,table_number']);
        
        // Logika Generate QR Code dan simpan path (sudah dibahas sebelumnya)
        $table = Table::create(['table_number' => $request->table_number, 'status' => 'available']);
        $url = route('customer.menu', $table->id);
        $filename = 'qr_' . $table->table_number . '_' . $table->id . '.svg';
        Storage::disk('public')->put('qrcodes/' . $filename, QrCode::size(300)->generate($url));
        $table->qr_code_path = 'qrcodes/' . $filename;
        $table->save();

        return redirect()->route('admin.tables.index')->with('success', 'Meja & QR Code berhasil dibuat.');
    }
    
    // Tampilkan form edit
    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    // Update data meja
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'status' => 'required|in:available,occupied'
        ]);

        $table->update($request->only('table_number', 'status'));
        
        // Opsional: Regenerate QR jika nomor meja diubah
        
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    // Hapus meja
    public function destroy(Table $table)
    {
        // Hapus file QR dari storage
        if ($table->qr_code_path) {
            Storage::disk('public')->delete($table->qr_code_path);
        }
        
        $table->delete();
        
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}