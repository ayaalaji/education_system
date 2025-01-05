<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AddAttachmentRequest;
use App\Http\Requests\Task\AssigneTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Resources\Task\TaskResource;

class TaskController extends Controller
{


    protected $taskService;
    /**
     * Summary of __construct
     * @param \App\Services\TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {

        $this->taskService = $taskService;
    }
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only('status');
        $perPage = $request->input('per_page', 15);
        $courseId = $request->course_id;
        $tasks = $this->taskService->listAllTask($filters,$perPage,$courseId);
        return $this->paginated(TaskResource::collection($tasks),'tasks retreive successfuly');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Task\TaskStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskStoreRequest $request)
    {
      $data = $request->validated();
      $task = $this->taskService->createTask($data);
      return $this->success(null,'task created success',201);
    }

    /**
     * Summary of show
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        $this->taskService->getTask($task);
        return $this->success(new TaskResource($task),'task retrive success');
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\Task\TaskUpdateRequest $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $data = $request->validated();
        $task = $this->taskService->updateTask($data,$task);
        return $this->success(null,'task update success');
    }

    /**
     * Summary of destroy
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
       $this->taskService->deleteTask($task);
       return $this->success(null,'task deleted success',204);
    }

 public function uploadTask(Task $task,AddAttachmentRequest $request)
 {

    $this->taskService->addAttachment($task,$request);
    return $this->success(null,'task uploaded');
 }
}
