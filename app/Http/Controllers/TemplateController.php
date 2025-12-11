<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Template::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'spt' => 'required|string',
            'file' => 'required|file|mimes:docx'
        ]);

        // Hapus template lama jika ada
        // $oldTemplate = Template::first();
        // if ($oldTemplate) {
        //     // if (Storage::exists($oldTemplate->file_path)) {
        //     //     Storage::delete($oldTemplate->file_path);
        //     // }
        //     if ($oldTemplate->file_path && Storage::disk('public')->exists($oldTemplate->file_path)) {
        //         Storage::disk('public')->delete($oldTemplate->file_path);
        //     }
        //     $oldTemplate->delete();
        // }

        // // Simpan file baru
        // $path = $request->file('file')->store('templates', 'public');

        $path = $request->file('file')->store('templates', 'public');

        $template = Template::create([
            'spt' => $request->spt,
            'file_path' => $path
        ]);

        return response()->json([
            'message' => 'Template berhasil diupload',
            'data' => $template
        ]);
    }
}
