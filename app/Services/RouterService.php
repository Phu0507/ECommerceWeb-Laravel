<?php

namespace App\Services;

use App\Models\Router;


class RouterService
{
    public function createRouter(string $canonical, int $moduleId, string $controller)
    {
        // Kiểm tra canonical đã tồn tại chưa
        if (Router::where('canonical', $canonical)->exists()) {
            throw new \Exception("Canonical đã tồn tại trong router");
        }

        // Nếu chưa thì tạo router mới
        return Router::create([
            'canonical'  => $canonical,
            'module_id'  => $moduleId,
            'controller' => $controller,
        ]);
    }

    public function updateRouter(int $moduleId, string $canonical, string $controller)
    {
        // Check canonical trùng (trừ bản ghi hiện tại)
        if (Router::where('canonical', $canonical)->where('module_id', '!=', $moduleId)->exists()) {
            throw new \Exception("Canonical đã tồn tại trong router");
        }

        // Update hoặc tạo mới
        return Router::updateOrCreate(
            ['module_id' => $moduleId, 'controller' => $controller],
            ['canonical' => $canonical]
        );
    }
}