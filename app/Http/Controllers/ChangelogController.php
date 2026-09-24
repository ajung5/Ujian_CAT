<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChangelogController extends Controller {
    public function index(): View {
        $path = base_path('CHANGELOG.md');

        abort_unless(File::exists($path), 404, 'File changelog tidak ditemukan.');

        $markdown = File::get($path);

        $content = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return view('guru.changelog', [
            'user' => Auth::user(),
            'content' => $content,
            'version' => config('app.version'),
        ]);
    }
}
