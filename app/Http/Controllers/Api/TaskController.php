<?php

namespace App\Http\Controllers\Api;
use App\Models\Task;
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
/**
     * Generate and save an Excel file for the task with students' notes and grades.
     *
     * @param int $taskId The ID of the task
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateExcel($taskId)
    {
        // Retrieve the task by its ID
        $task = Task::findOrFail($taskId);

        // Get the desktop path
        $desktopPath = env('DESKTOP_PATH', 'C:/Users/AL.Shaddad Home/Desktop');

        // Ensure the path exists
        if (!is_dir($desktopPath)) {
            mkdir($desktopPath, 0777, true); // Create the directory if it does not 
        }

        // Define the file name and full path
        $fileName = 'task_notes_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = $desktopPath . '/' . $fileName;

        // Export the data to an Excel file at the specified path
        Excel::store(new TaskNotesExport($task), $fileName, 'local');

        // Move the file from temporary storage to the desktop
        $storedPath = storage_path('app/' . $fileName);
        if (file_exists($storedPath)) {
            rename($storedPath, $filePath);
        } else {
            throw new \Exception('File not found in temporary storage.');
        }

        return response()->json([
            'message' => 'Excel file has been saved successfully!',
            'file_path' => $filePath,
        ]);
    }

    //.....................................User With Overdue Tasks........................................

    public function exportUsersWithOverdueTasks()
    {
        $this->taskService->exportUsersWithOverdueTasks();

        return $this->success(null,'Excel file has been saved successfully!');
        
    }
}
