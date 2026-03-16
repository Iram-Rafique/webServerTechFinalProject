
let countdown = typeof otpCountdown !== "undefined" ? otpCountdown : 60;

const timerDisplay = document.getElementById("timer");
const resendBtn = document.getElementById("resendBtn");

if (timerDisplay && resendBtn) {

    resendBtn.style.display = "none";

    const timer = setInterval(() => {

        let minutes = Math.floor(countdown / 60);
        let seconds = countdown % 60;

        timerDisplay.innerHTML =
            `Resend OTP in ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

        countdown--;

        if (countdown <= 0) {
            clearInterval(timer);
            timerDisplay.innerHTML = "You can resend OTP now.";
            resendBtn.style.display = "block";
        }

    }, 1000);
}
window.addEventListener("load", () => {
    setTimeout(() => {
        document.querySelectorAll("input[type='text'], input[type='email'], input[type='password']").forEach(input => {
            input.value = "";
        });
    }, 100);
});