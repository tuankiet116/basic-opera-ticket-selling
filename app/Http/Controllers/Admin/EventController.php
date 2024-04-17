<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Services\Admin\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    public function listAll(): JsonResponse
    {
        $search = request()->get("search") ?? "";
        $events = $this->eventService->listAll($search);
        return $this->responseSuccess($events->toArray());
    }

    public function create(CreateEventRequest $request)
    {
        $data = $request->validated();
        $event = $this->eventService->create($data);
        if ($event) return $this->responseSuccess($event->toArray());
        return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
    }

    public function edit($eventId)
    {
        $data = $this->eventService->edit($eventId);
        if (!$data) return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
        return $this->responseSuccess($data->toArray());
    }

    public function update(int $eventId, UpdateEventRequest $request)
    {
        $data = $request->validated();
        $event = $this->eventService->update($data, $eventId);
        if (!$event) return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
        if ($event) return $this->responseSuccess($event->toArray());
        return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
    }
}
