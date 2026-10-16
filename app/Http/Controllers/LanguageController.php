<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Language;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.language');

        $languages = $this->languageService->getAllPage();
        return view('aadmin.language.index', compact('config', 'languages'));
    }

    public function create()
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        // Logic to show the form for creating a new user
        return view('aadmin.language.create', compact('config'));
    }

    public function store( StoreLanguageRequest $request)
    {
        
        $data = $request->validated();

        // Xử lý ảnh upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $random = \Illuminate\Support\Str::random(5);
            $date = now()->format('dmY');
            $originalName = preg_replace('/\s+/', '-', $file->getClientOriginalName());
            $fileName = "{$random}-{$date}-{$originalName}";

            // Đường dẫn thư mục lưu
            $uploadPath = public_path('upload/languages');

            // Nếu chưa có thư mục thì tạo
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Lưu file
            $file->move($uploadPath, $fileName);

            // Gán tên file vào dữ liệu
            $data['image'] = 'upload/languages/' . $fileName;

        }


        if ($data) {
            $this->languageService->create($data);
            return redirect()->route('language.index')->with('success', 'Language created successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create language.']);
    }

    public function edit($id)
    {
        $config = [
            'js' => ['css2/library/location.js'],
        ];
        $config['seo']=config('apps.language');
        $language = $this->languageService->getById($id);

        if (!$language) {
            return redirect()->route('aadmin.language.index')->withErrors(['error' => 'Language not found.']);
        }
        return view('aadmin.language.update', compact('id', 'config', 'language'));
    }

    public function update(UpdateLanguageRequest $request, $id)
    {
        $data = $request->validated();

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
        $file = $request->file('image');

        // Đặt tên file mới
        $random = \Illuminate\Support\Str::random(5);
        $date = now()->format('dmY');
        $originalName = preg_replace('/\s+/', '-', $file->getClientOriginalName());
        $fileName = "{$random}-{$date}-{$originalName}";

        // Di chuyển file
        $file->move(public_path('upload/languages'), $fileName);

        // Gán đường dẫn ảnh mới vào $data
        $data['image'] = 'upload/languages/' . $fileName;

        // Xoá ảnh cũ nếu có
        $oldLanguage = Language::findOrFail($id);
        if ($oldLanguage->image && file_exists(public_path($oldLanguage->image))) {
            @unlink(public_path($oldLanguage->image));
        }
        }

        $this->languageService->update($id, $data);

        return redirect()->route('language.index')->with('success', 'Language updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a user
        $this->languageService->delete($id);
        return redirect()->route('language.index')->with('success', 'Language deleted successfully.');
    }

    private function config()
    {
        return [
            'js'=>['css2/js/plugins/switchery/switchery.js'],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }
}