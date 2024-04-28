
const canvas = document.getElementById("gameCanvas");
const gc = canvas.getContext("2d");
const width = canvas.width;
const height = canvas.height;

let playerOneYPos = height / 2;
let playerTwoYPos = height / 2;
let ballXPos = width / 2;
let ballYPos = height / 2;
let ballXSpeed = 2;
let ballYSpeed = 2;
let scoreP1 = 0;
let scoreP2 = 0;
let gameStarted = false;

canvas.addEventListener("mousemove", (e) => {
    playerOneYPos = e.clientY;
});

canvas.addEventListener("click", () => {
    gameStarted = true;
});

function draw() {
    gc.clearRect(0, 0, width, height);

    gc.fillStyle = "green";
    gc.fillRect(0, 0, width, height);

    gc.fillStyle = "white";
    gc.font = "25px Arial";
    gc.textAlign = "center";
    gc.fillText(scoreP1 + "\t\t\t\t\t\t\t\t" + scoreP2, width / 2, 100);

    gc.fillRect(width / 2 - 5, 0, 10, height);
    gc.strokeOval(width / 2 - 75, height / 2 - 75, 150, 150);

    if (gameStarted) {
        ballXPos += ballXSpeed;
        ballYPos += ballYSpeed;

        if (ballXPos < width - width / 4) {
            playerTwoYPos = ballYPos - 150 / 2;
        } else {
            playerTwoYPos = ballYPos > playerTwoYPos + 150 / 2 ? playerTwoYPos + 1 : playerTwoYPos - 1;
        }

        gc.beginPath();
        gc.arc(ballXPos, ballYPos, 20, 0, Math.PI * 2);
        gc.fillStyle = "white";
        gc.fill();
        gc.closePath();
    } else {
        gc.fillStyle = "white";
        gc.fillText("Click", width / 2, height / 2);

        ballXPos = width / 2;
        ballYPos = height / 2;

        ballXSpeed = Math.random() < 0.5 ? 2 : -2;
        ballYSpeed = Math.random() < 0.5 ? 2 : -2;
    }

    if (ballYPos > height || ballYPos < 0) ballYSpeed *= -1;

    if (ballXPos < 0) {
        scoreP2++;
        gameStarted = false;
    }

    if (ballXPos > width) {
        scoreP1++;
        gameStarted = false;
    }

    if (((ballXPos + 20 > width - 20) && ballYPos >= playerTwoYPos && ballYPos <= playerTwoYPos + 150) ||
        ((ballXPos < 20 + 20) && ballYPos >= playerOneYPos && ballYPos <= playerOneYPos + 150)) {
        ballYSpeed *= -1;
        ballXSpeed *= -1;
    }

    gc.fillRect(0, playerOneYPos, 20, 150);
    gc.fillRect(width - 20, playerTwoYPos, 20, 150);

    requestAnimationFrame(draw);
}

draw();