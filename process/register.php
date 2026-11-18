<?php

// Start session
session_start();

// Database connection
require_once "../config/database.php";

/*
| Allow only POST requests
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

/*
 CSRF Token Validation
*/
if (
    !isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['token']
) {
    die("Invalid CSRF token.");
}

/*
 Get and Clean Form Data
*/
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$age = trim($_POST['age'] ?? '');
$course = trim($_POST['course'] ?? '');

/*
 Store Validation Errors
*/
$errors = [];

/*
 Check Empty Fields
*/
if (empty($full_name)) {
    $errors[] = "Full name is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
}

if (empty($age)) {
    $errors[] = "Age is required.";
}

if (empty($course)) {
    $errors[] = "Course is required.";
}

/*
 Full Name Validation
 Must be between 3 and 100 characters
*/
if (
    strlen($full_name) < 3 ||
    strlen($full_name) > 100
) {
    $errors[] = "Full name must be between 3 and 100 characters.";
}

/*
Name must only contain:
Letters, spaces, apostrophes and hyphens
*/
if (
    !preg_match("/^[a-zA-Z' -]+$/", $full_name)
) {
    $errors[] = "Full name contains invalid characters.";
}

/*
Email Validation
*/
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}

/*

 Check Duplicate Email
 Treat uppercase and lowercase as the same

*/
$stmt = $pdo->prepare(
    "SELECT id
     FROM students_registration
     WHERE LOWER(email) = LOWER(?)"
);

$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    $errors[] = "Email already registered.";
}

/*
 Age Validation
*/
if (
    !empty($age) &&
    (
        filter_var($age, FILTER_VALIDATE_INT) === false ||
        $age < 16 ||
        $age > 100
    )
) {
    $errors[] = "Age must be a whole number between 16 and 100.";
}
/*

Valid Courses

*/
$allowedCourses = [
    "Computer Science",
    "Information Technology",
    "Business Administration",
    "Education",
    "Nursing"
];

/*
 Validate Selected Course
*/
if (!empty($course) && !in_array($course, $allowedCourses)) {
    $errors[] = "Invalid course selected.";
}
/*

 If Errors Exist
 Save errors and old values then return to form

*/
if (!empty($errors)) {

    $_SESSION['errors'] = $errors;

    $_SESSION['old'] = [
        'full_name' => $full_name,
        'email' => $email,
        'age' => $age,
        'course' => $course
    ];

    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->prepare(
    "INSERT INTO students_registration
    (full_name, email, age, course)
    VALUES (?, ?, ?, ?)"
);

$stmt->execute([
    $full_name,
    $email,
    $age,
    $course
]);

$_SESSION['success'] = "Student registered successfully.";

header("Location: ../index.php");
exit();

?>