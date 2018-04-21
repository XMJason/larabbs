<?php

namespace App\Http\Controllers\Api;

use App\Transformers\PermissionTransformer;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index()
    {
        $permission = $this->user()->getAllPermissions();

        return $this->response->collection($permission, new PermissionTransformer());
    }
}
