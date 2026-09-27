const codeInputs = document.querySelectorAll(".code-input");
const verificationForm = document.getElementById("verificationForm");
const requestCode = document.getElementById("requestCode");
const savedTheme = localStorage.getItem("theme");

if (savedTheme === "dark") {
    document.body.classList.add("dark");
}

codeInputs.forEach((input, index) => {
    input.addEventListener("input", () => {
        input.value = input.value.replace(/\D/g, "");
        if (input.value && index < codeInputs.length - 1) {
            codeInputs[index + 1].focus();
        }
    });

    input.addEventListener("keydown", (event) => {
        if (
            event.key === "Backspace" &&
            !input.value &&
            index > 0
        ) {
            codeInputs[index - 1].focus();
        }
        if (
            event.key === "ArrowLeft" &&
            index > 0
        ) {
            codeInputs[index - 1].focus();
        }
        if (
            event.key === "ArrowRight" &&
            index < codeInputs.length - 1
        ) {
            codeInputs[index + 1].focus();
        }
    });
});

codeInputs[0].addEventListener("paste", (event) => {
    event.preventDefault();
    const pastedCode =
        event.clipboardData
            .getData("text")
            .replace(/\D/g, "")
            .slice(0, 4);
    pastedCode.split("").forEach((number, index) => {
        if (codeInputs[index]) {
            codeInputs[index].value = number;
        }
    });
    if (pastedCode.length === 4) {
        codeInputs[3].focus();
    }
});

verificationForm.addEventListener("submit", (event) => {
    let code = "";
    codeInputs.forEach(input => {
        code += input.value;
    });

    if (code.length !== 4) {
        event.preventDefault();
        alert("Please enter the 4-digit code.");
    }
});

requestCode.addEventListener("click", () => {
    window.location.href = "/authentication/resend";
});