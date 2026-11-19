<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        // 1. Query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        $search = $request->input('search');
        $penerbit = $request->input('penerbit');
        $status = $request->input('status');
        $sort = $request->input('sort');

        $query = Buku::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // 2. Filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        if (!empty($penerbit)) {
            $query->where('penerbit', $penerbit);
        }

        if ($status === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($status === 'habis') {
            $query->where('stok', '<=', 0);
        }

        // 3. Pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        switch ($sort) {
            case 'judul':
                $query->orderBy('judul', 'asc');
                break;
            case 'tahun':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'stok':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // 4. Paginasi 8 data dengan withQueryString()
        $buku = $query->paginate(8)->withQueryString();

        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        $listPenerbit = Buku::whereNotNull('penerbit')
            ->where('penerbit', '!=', '')
            ->distinct()
            ->orderBy('penerbit', 'asc')
            ->pluck('penerbit');

        // 6. Return view 'home' dengan data buku dan list penerbit
        return view('home', compact('buku', 'listPenerbit', 'search', 'penerbit', 'status', 'sort'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}