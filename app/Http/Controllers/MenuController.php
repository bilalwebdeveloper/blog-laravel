<?php
// app/Http/Controllers/MenuController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MenuServiceInterface;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function getHeaderMenu($user_id = null)
    {
        $headerMenu = $this->menuService->getHeaderMenu($user_id);
        return response()->json($headerMenu, 200);
    }

    public function getFooterMenu($user_id = null)
    {
        $footerMenu = $this->menuService->getFooterMenu($user_id);
        return response()->json($footerMenu, 200);
    }
}
