<?php

session_start();

require_once "config/database.php";

/*

 Generate CSRF Token

*/
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

/*
 Success Message
*/
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

/*
 Error Messages
*/
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

/*

 Old Form Values

*/
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

/*
 Fetch Students
*/
$stmt = $pdo->query(
    "SELECT * FROM students_registration
     ORDER BY registration_date DESC"
);

$students = $stmt->fetchAll();

$totalStudents = count($students);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <h1>Student Registration System</h1>

    <!-- Success Message -->
    <?php if (!empty($success)): ?>
        <div class="success">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Error Messages -->
    <?php foreach ($errors as $error): ?>
        <div class="error">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endforeach; ?>

    <!-- Registration Form -->
    <form action="process/register.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= $_SESSION['token']; ?>"
        >

        <label>Full Name</label>
        <input
            type="text"
            name="full_name"
            value="<?= htmlspecialchars($old['full_name'] ?? ''); ?>"
        >

        <label>Email</label>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($old['email'] ?? ''); ?>"
        >

        <label>Age</label>
        <input
            type="number"
            name="age"
            value="<?= htmlspecialchars($old['age'] ?? ''); ?>"
        >

        <label>Course</label>

        <select name="course">
            <option value="">Select Course</option>

            <option value="Computer Science"
                <?= (($old['course'] ?? '') == 'Computer Science') ? 'selected' : ''; ?>>
                Computer Science
            </option>

            <option value="Information Technology"
                <?= (($old['course'] ?? '') == 'Information Technology') ? 'selected' : ''; ?>>
                Information Technology
            </option>

            <option value="Business Administration"
                <?= (($old['course'] ?? '') == 'Business Administration') ? 'selected' : ''; ?>>
                Business Administration
            </option>

            <option value="Education"
                <?= (($old['course'] ?? '') == 'Education') ? 'selected' : ''; ?>>
                Education
            </option>

            <option value="Nursing"
                <?= (($old['course'] ?? '') == 'Nursing') ? 'selected' : ''; ?>>
                Nursing
            </option>
        </select>

        <button type="submit">
            Register Student
        </button>

    </form>

    <h2>Total Students Registered: <?= $totalStudents; ?></h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Age</th>
            <th>Course</th>
            <th>Registration Date</th>
        </tr>

        <?php foreach ($students as $student): ?>

            <tr>

                <td><?= htmlspecialchars($student['id']); ?></td>

                <td><?= htmlspecialchars($student['full_name']); ?></td>

                <td><?= htmlspecialchars($student['email']); ?></td>

                <td><?= htmlspecialchars($student['age']); ?></td>

                <td><?= htmlspecialchars($student['course']); ?></td>

                <td><?= htmlspecialchars($student['registration_date']); ?></td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>
</html>