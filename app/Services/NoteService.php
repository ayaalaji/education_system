<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Mail\TaskEvaluationMail;
use Illuminate\Support\Facades\Mail;


class NoteService
{
/**
     * Store a note on a specific task.
     *
     * This method attaches a note and grade to a user associated with a task.
     *
     * @param array $data The data containing the note and grade.
     * @param int $taskId The ID of the task to which the note is being added.
     * @param int $userId The ID of the user to whom the note belongs.
     * @return AppModelsTask The task instance with the attached note.
     * @throws IlluminateHttpExceptionsHttpResponseException If adding the note fails.
     */
    public function storeNote(array $data, $taskId, $userId)
    {
        try {
            // Attach the note to a specific task
            $task = Task::findOrFail($taskId);
            $user = User::findOrFail($userId);

            // Update the existing pivot table entry with the note and grade
            $task->users()->updateExistingPivot($user->id, ['note' => $data['note'], 'grade' => $data['grade']]);
            //sending a email to student include the grade and note
            $studentName = $user->name;
            $taskNote = $data['note'];
            $taskgrade = $data['grade'];
            Mail::to($user->email)->send(new TaskEvaluationMail($studentName, $taskNote, $taskgrade));

            return $task;
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error adding note: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to add note'], 500));
        }
    }

    /**
     * Remove a note from a specific task for a user.
     *
     * This method deletes the note associated with a user on a task by setting it to null.
     *
     * @param int $taskId The ID of the task from which the note is being deleted.
     * @param int $userId The ID of the user whose note is being deleted.
     * @throws IlluminateHttpExceptionsHttpResponseException If deleting the note fails.
     */
    public function removeNote($taskId, $userId)
    {
        try {
            // Deleting a note for a specific user
            $task = Task::findOrFail($taskId);
            $user = User::findOrFail($userId);

            // Update the existing pivot table entry to remove the note
            $task->users()->updateExistingPivot($user->id, ['note' => null,'grade' => null]);
        } catch (Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Error deleting note: ' . $e->getMessage());
            // Throw an exception with a JSON response indicating failure
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete note'], 500));
        }
    }
}