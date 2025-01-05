<!DOCTYPE html>
<html>

<head>
    <title>Assignment Deadline Reminder</title>
</head>

<body>
    <h1>Hi {{ $user->name }},</h1>
    <p>You have a task deadline approaching for <strong>{{ $task->title }}</strong> in the course <strong>{{ $course->title }}</strong>.</p>
    <p>
        <strong>Remaining Time:</strong>
        {{ $remainingDays }} day{{ $remainingDays > 1 ? 's' : '' }} to the deadline.
    </p>
    <p>
        <strong>Task Due Date:</strong>
        {{ \Carbon\Carbon::parse($task->due_date)->format('l, jS F Y h:i A') }}
    </p>
    <p>Please make sure to complete it on time!</p>
    <br>
    <p>Regards,</p>
    <p>Your Learning Platform</p>
</body>

</html>