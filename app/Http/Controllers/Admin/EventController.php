<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    public function listAll(string $search): JsonResponse
    {
        $events = $this->eventService->listAll($search);
        return $this->responseSuccess($events);
    }
}
