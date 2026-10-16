<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;

class LanguageService
{
    public function getAllPage()
    {
        return Language::paginate(10);
    }

    public function getById($id)
    {
        return Language::find($id);
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id(); // thêm trước khi create
        return Language::create($data);
    }

    public function update($id, array $data)
    {
        $language = Language::findOrFail($id);
        $language->update($data);
        return $language;
    
    }

    public function delete($id)
    {
        $language = Language::findOrFail($id);
        return $language->delete();
    }
}