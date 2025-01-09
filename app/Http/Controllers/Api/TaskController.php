<?php

namespace App\Http\Controllers\Api;
use App\Models\Task;
use App\Services\FcmService;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Events\TaskSubmittedEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Task\TaskResource;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Requests\Task\AssigneTaskRequest;
use App\Http\Requests\Task\AddAttachmentRequest;
use Illuminate\Support\Facades\Log;

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
        // إضافة المرفق
        $this->taskService->addAttachment($task, $request);

        // إذا تم رفع المرفق بنجاح، نرسل إشعار عبر FCM
        $to = "dummy_device_token_for_testing"; // توكن وهمي للجهاز
        $title = "Task Uploaded Successfully";
        $body = "The task has been uploaded with attachments.";

        // استدعاء خدمة FCM لإرسال الإشعار
        $response = $this->fcmService->sendNotification($to, $title, $body);

        // إرجاع الاستجابة التي تحتوي على نتيجة إرسال الإشعار
        return $this->success([
            'message' => 'Task uploaded and notification sent',
            'fcm_response' => $response // عرض الاستجابة من FCM
        ]);
    }
}
