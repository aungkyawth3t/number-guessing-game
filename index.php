<?php
    session_start();
    $message = "";
    $hint = "";

    function resetGame() {
        unset($_SESSION['secret_number'], $_SESSION['guess_count'], $_SESSION['last_guess']);
    }

    if(isset($_GET['action']) && $_GET['action'] == 'reset') {
        resetGame();
        header("Location: index.php");
        exit;
    }

    if(!isset($_SESSION['secret_number'])) {
        $_SESSION['secret_number'] = rand(1, 10);
        $_SESSION['guess_count'] = 0;
        $_SESSION['last_guess'] = null;
        $message = "New game! I'm thinking of a number from 1 to 10.";
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guess'])) {
        $guess = (int) $_POST['guess'];
        $_SESSION['guess_count']++;
        $secret_number = $_SESSION['secret_number'];

        if($guess == $secret_number) {
            $message = "Congratulations! You guessed it in {$_SESSION['guess_count']} tries!";
            unset($_SESSION['secret_number']);
            unset($_SESSION['last_guess']);
        }
        elseif($guess < $secret_number) {
            $message = "Too low!";
        }
        elseif($guess > $secret_number) {
            $message = "Too high!";
        }

        // hint
        if($guess !== $secret_number) {
            $newDistance = abs($secret_number - $guess);
            if(isset($_SESSION['last_guess'])) {
                $oldDistance = abs($secret_number - $_SESSION['last_guess']);
            } else {
                $oldDistance = null;
            }
            if ($oldDistance === null) {
                $hint = "Let's see how close you are!";
            } elseif ($newDistance < $oldDistance) {
                $hint = "You're getting warmer!";
            } elseif ($newDistance > $oldDistance) {
                $hint = "You're getting colder!";
            } else {
                $hint = "Same distance as before!";
            }
        }
        $_SESSION['last_guess'] = $guess;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuessTheNumber</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Headline:wght@200..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="game-card">
        <h1>Guess the Number!</h1>
        
        <p> <?= $message ?> </p>
        <?php if($message == "Too high!"): ?>
            <h2 class="feedback-message too-high"> <?= $message ?> </h2>
        <?php elseif($message == "Too Low!"): ?>
            <h2 class="feedback-message too-low"> <?= $message ?> </h2>
        <?php endif ?>

        <p class="hint-message"> <?= $hint ?> </p>
        <p class="guess-count">Guesses: <strong> <?= $_SESSION['guess_count'] ?? 0 ?> </strong></p>
        <?php if(isset($_SESSION['secret_number'])): ?>
            <form class="game-form" action="index.php" method="POST">
                <label for="guess"> Enter your guess (1 - 100) </label>
                <input type="number" id="guess" name="guess" min="1" max="100" placeholder="?" required>
                <button type="submit" class="game-button" name="btn-guess">
                    GUESS
                </button>
            </form>
        <?php else: ?>
            <a href="?action=reset" class="game-button play-again">
                Play Again?
            </a>
        <?php endif ?>

        <div class="reset-link">
            <a href="?action=reset">
                Start a New Game
            </a>
        </div>
    </div>
</body>
</html>