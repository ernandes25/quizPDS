document.addEventListener('DOMContentLoaded', function () {
    const loginModal = document.getElementById('login-modal');
    const loginForm = document.getElementById('login-form');
    const buttonInit = document.getElementById('button-init');
    const logoutButton = document.getElementById('logout');

    function checkLogin() {
        const user = localStorage.getItem('user');
        if (user) {

            if (logoutButton) {
                logoutButton.style.display = 'block';

            } if (buttonInit) {
                logoutInit.innerText = 'Continuar pds';
            }
        } else {
            if (logoutButton) {
                logoutButton.style.display = 'none;'
            }
        }
    }


    function startPDS() {
        const user = localStorage.getItem('user');
        if (user) {
            window.location.href = 'quiz.html';
        } else {
            loginModal.style.display = 'block';
        }
    }

    function logout() {
        localStorage.removeItem('user');
        if (logoutButton) {
            logoutButton.style.display = 'none';
        }
        if (buttonInit) {
            buttonInit.innerText = 'Iniciar PDS';
        }
        window.location.href = 'index.html';
    }
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(loginForm);

            fetch('cad.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // Adicionado para depuração
                    if (data.status === 'success') {
                        localStorage.setItem('user', formData.get('usuario'));
                        alert(data.message);
                        window.location.href = 'quiz.html';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    checkLogin();
    window.startPDS = startPDS;
    window.logout = logout;
});




function growProgressBar(percentage_width) {
    var bar = document.getElementById("progress_bar");
    bar.style.width = percentage_width;
}

function calculateScore() {
    let score = 0;
    let score11to15 = 0;

    const questions = document.querySelectorAll('.question');
    const totalQuestions = questions.length;

    for (let i = 1; i <= totalQuestions; i++) {
        const selectedOption = document.querySelector(`input[name="q${i}"]:checked`);
        if (selectedOption) {
            const value = parseInt(selectedOption.value, 10);
            if (i <= 10) {
                score += value;
            } else {
                score11to15 += value;
            }
        }
    }

    return score + (score11to15 * 2);
}

document.addEventListener('DOMContentLoaded', function () {
    const questions = document.querySelectorAll('.question');
    const totalQuestions = questions.length;

    questions.forEach((questionDiv, index) => {
        const nextButton = questionDiv.querySelector('button[type="button"]');
        const radios = questionDiv.querySelectorAll('input[type="radio"]');

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                nextButton.disabled = false;
            });
        });

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                questionDiv.classList.remove('active');

                if (index < questions.length - 1) {
                    questions[index + 1].classList.add('active');
                    const progressPercentage = ((index + 1) / totalQuestions) * 100;
                    growProgressBar(progressPercentage + '%');
                } else {
                    growProgressBar('100%');

                    const score = calculateScore();
                    document.getElementById('score').innerText = score;
                    document.getElementById('result').classList.add('active');
                    document.getElementById('result').style.display = 'block';

                    let score_info;
                    if (score <= 90) {
                        score_info = "É improvável que você tenha Pouco Desejo Sexual (PDS)";
                    } else if (score >= 91 && score <= 120) {
                        score_info = "Aumenta a possibilidade do Pouco Desejo Sexual(PDS) estar presente, mas isso não pode ser claramente determinado a partir de suas respostas.";
                    } else if (score >= 121 && score <= 140) {
                        score_info = "Resultado sugere muito a presença do Pouco Desejo Sexual(PDS), mas de forma alguma a provam.";
                    } else {
                        score_info = "Você tem Pouco Desejo Sexual (PDS), embora não possa ser feito um diagnóstico definitivo com base apenas em um teste online.";
                        document.getElementById('result').classList.add('restar-dps');
                    }

                    document.getElementById('result_score_info').innerText = score_info;
                }
            });
        }
    });
});
