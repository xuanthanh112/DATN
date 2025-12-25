<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ConstructRepositoryInterface  as ConstructRepository;
use App\Services\Interfaces\ConstructServiceInterface  as ConstructService;


class ConstructController extends Controller
{
    protected $constructRepository;
    protected $constructService;
    
    public function __construct(
        ConstructRepository $constructRepository,
        ConstructService $constructService,
    ){
        $this->constructService = $constructService;
        $this->constructRepository = $constructRepository;
    }
}
