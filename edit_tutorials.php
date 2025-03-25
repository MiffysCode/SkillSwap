<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/..includes/db.php';
require_once __DIR__ . '/..classes/Tutorial.php';

$db = (new Database())->getConnection();
$tutorialObj = new Tutorial($db);
$error = '';

if(!isset($_GET['tutorial_id'])) {
    header("Location: users.php");
    exit;
}

$tutorial_id = $_GET['tutorial_id'];
$tutorial = $tutorialObj->getTutorialById($tutorial_id);

// Ensure the logged-in user owns this tutorial
if($tutorial['user_id'] != $_SESSION['user_id']){
    header("Location: users.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = $_POST['title'];
    $description = $_POST['description'];
    if($tutorialObj->updateTutorial($tutorial_id, $title, $description)){
        header("Location: users.php");
        exit;
    } else {
        $error = "Failed to update tutorial.";
    }
}
?>

<?php include __DIR__ . '/..includes/header.php'; ?>

<main>
    <h1>Edit Tutorial</h1>
    <?php if($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="edit_tutorials.php?tutorial_id=<?php echo $tutorial_id; ?>" method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($tutorial['title']); ?>" required>
        <label>Description:</label>
        <textarea name="description" rows="5" required><?php echo htmlspecialchars($tutorial['description']); ?></textarea>
        <button type="submit">Update Tutorial</button>
    </form>
</main>

<?php include __DIR__ . '/..includes/footer.php'; ?>
