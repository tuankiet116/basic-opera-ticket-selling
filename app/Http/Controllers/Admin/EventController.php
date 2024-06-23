<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Http\Requests\Admin\UpdateEventStatusRequest;
use App\Services\Admin\EventService;
use Exception;
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
        return $this->responseSuccess($data);
    }

    public function update(int $eventId, UpdateEventRequest $request)
    {
        $data = $request->validated();
        try {
            $event = $this->eventService->update($data, $eventId);
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
        if ($event) return $this->responseSuccess($event->toArray());
    }

    public function updateStatus(int $eventId, UpdateEventStatusRequest $request)
    {
        $data = $request->validated();
        $event = $this->eventService->updateStatus($data, $eventId);
        if (!$event) return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
        return $this->responseSuccess($event->toArray());
    }

    public function delete(int $eventId)
    {
        $result = $this->eventService->deleteEvent($eventId);
        if ($result) return $this->responseSuccess();
        return $this->responseError();
    }
}
