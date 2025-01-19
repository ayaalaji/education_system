<!DOCTYPE html>
<html>
<head>
    <title>New Course Material Uploaded</title>
</head>
<body>
    <h1>New Material Added to {{ $course_title }}</h1>
    <p> We are excited to inform you that new material has been uploaded for your course: <strong>{{ $course_title }}</strong>.</p>

    <h3>Details:</h3>
    <ul>
        @if (!empty($file_path))
            <li><strong>File Path:</strong> <a href="{{ $file_path }}">{{ $file_path }}</a></li>
        @endif

        @if (!empty($vedio_path))
            <li><strong>Video Path:</strong> <a href="{{ $vedio_path }}">{{ $vedio_path }}</a></li>
        @endif
    </ul>

    @if (empty($file_path) && empty($vedio_path))
        <p>No file or video links were provided for this material.</p>
    @endif

    <p>Please check your course portal for more information.</p>

    <p>Thank you for being part of our learning community!</p>

    <footer>
        <p style="font-size: 12px; color: gray;">
            If you have any questions, feel free to contact our support team.
        </p>
    </footer>
</body>
</html>
