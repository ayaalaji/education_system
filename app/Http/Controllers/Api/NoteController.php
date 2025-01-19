<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;
use App\Events\TaskSubmittedEvent;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Services\NoteService;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->middleware('security');
        $this->noteService = $noteService;
    }
        /**
     * Add a note to a task.
     *
     * This method validates the incoming request data and stores the note
     * associated with the specified task and user.
     *
     * @param AppHttpRequestsStoreNoteRequest $request The request containing the note data.
     * @param int $taskId The ID of the task to which the note is being added.
     * @param int $userId The ID of the user adding the note.
     * @return IlluminateHttpJsonResponse A JSON response indicating success.
     */
    public function addNote(StoreNoteRequest $request, $taskId, $userId)
    {
        // Validate the incoming request data
        $validatedData = $request->validated();

        // Store the validated note data using the note service
        $this->noteService->storeNote($validatedData, $taskId, $userId);

        // Return a success response indicating that the note was added successfully
        return $this->success(null, 'note added success', 201);
    }

    /**
     * Delete a note from a task.
     *
     * This method removes the note associated with the specified task and user.
     *
     * @param int $taskId The ID of the task from which the note is being deleted.
     * @param int $userId The ID of the user who owns the note.
     * @return IlluminateHttpJsonResponse A JSON response indicating success.
     */
    public function deleteNote($taskId, $userId)
    {
        // Remove the note associated with the specified task and user
        $this->noteService->removeNote($taskId, $userId);

        // Return a success response indicating that the note was deleted successfully
        return $this->success(null, 'note deleted success', 204);
    }
}
