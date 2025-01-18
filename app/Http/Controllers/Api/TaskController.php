<?php

namespace App\Http\Controllers\Api;
use Exception;
use App\Models\Task;
use App\Models\Course;
use App\Services\FcmService;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Exports\TaskNotesExport;
use App\Events\TaskSubmittedEvent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Task\TaskResource;
use App\Exports\UsersWithOverdueTasksExport;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Requests\Task\AssigneTaskRequest;
use App\Http\Requests\Task\AddAttachmentRequest;


class TaskController extends Controller
{
    protected $fcmService;
    protected $taskService;
    /**
     * Summary of __construct
     * @param \App\Services\TaskService $taskService
     */
    public function __construct(TaskService $taskService, FcmService $fcmService)
    {
        $this->middleware('security');
        $this->taskService = $taskService;
        $this->fcmService =$fcmService;
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
      return $this->success($task,'task created success',201);
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

        $this->taskService->addAttachment($task, $request);

        $to = "dummy_device_token_for_testing"; // firebase token not real
        $title = "Task Uploaded Successfully";
        $body = "The task has been uploaded with attachments.";

        $response = $this->fcmService->sendNotification($to, $title, $body);

        return $this->success([
            'message' => 'Task uploaded and notification sent',
            'fcm_response' => $response
        ]);
    }

/**
 * Summary of forceDeleteForTask
 * @param int $id
 * @return mixed|\Illuminate\Http\JsonResponse
 */
public function forceDeleteForTask(int $id)
{
    $this->taskService->forceDeleteTask($id);
    return $this->success(null,'task deleted success');
}

/**
 * Summary of restoreTask
 * @param int $id
 * @return mixed|\Illuminate\Http\JsonResponse
 */
public function restoreTask(int $id)
{
   $task = $this->taskService->restoreTask($id);
    return $this->success($task,'task restore success');
}

   
}
