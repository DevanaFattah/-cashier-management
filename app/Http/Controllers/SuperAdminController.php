<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    public function index () 
    {
        return view('settings.index');
    }

    public function update(Request $request, Shop $shop)
    {
        
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string'
                ]);
                
                // dd($validated, $shop);
            $shop->update($validated);

            $settings = $shop->setting;

            return redirect(route('settings.index', $shop->id))->with('success', 'Berhasil mengubah Nama dan Alamat');
        } catch (\Throwable $th) {
            // back()->with('error', 'Gagal mengubah Nama & Alamat');

            // return info($th->getMessage());
            throw $th;
        }
    }

    // public function index
    public function updateTheme (Request $request, Shop $shop) {
        try {
            $validated = $request->validate([
                'theme' => 'required|in:ember,ocean,forest,violet,rose',
            ]);

            // dd($theme);
            $setting = Setting::where('theme', $validated)->first();

            // dd($setting);
            $shop->update([
                'setting_id' => $setting->id
            ]);
            
            return redirect(route('settings.index', $shop->id))->with('success', 'Theme berhasil diubah');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function uploadLogo (Request $request, Shop $shop) {

            $request->validate([
                'logo' => 'required|file|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            // if (!$request->hasFile('logo')) {
            //     if ($shop->path_logo) {
            //         Storage::disk('public')->delete($shop->path_logo);
            //     }

            //     $path = $request->file('logo')->store('logos', 'public');

            //     $shop->update([
            //         'path_logo' => $path,
            //     ]);
            // };

                    // Cek apakah ada file logo yang diupload
            if ($request->hasFile('logo')) {
                // Hapus logo lama kalau ada (Opsional, biar server nggak penuh)
                if ($shop->logo && Storage::disk('public')->exists($shop->logo)) {
                    Storage::disk('public')->delete($shop->logo);
                }

                // Simpan file 
                $path = $request->file('logo')->store('logos', 'public');
            }

            try {
                $shop->update([
                    'path_logo' => $path
                ]);
                return back()->with('success', 'Berhasil mengubah data toko');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return back()->with('error', 'Gagal menyimpan data');
            }
        }
}