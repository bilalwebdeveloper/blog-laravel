<?php
// app/Services/MenuServiceInterface.php

namespace App\Services;

interface MenuServiceInterface
{
    public function getHeaderMenu($userId = null);
    public function getFooterMenu($userId = null);
}
