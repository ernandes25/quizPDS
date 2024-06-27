
document.addEventListener('DOMContentLoaded', function () {
    const loginModal = document.getElementById('login-modal');
    const loginForm = document.getElementById('login-form');
    const buttonInit = document.getElementById('button-init');
    const logoutButton = document.getElementById('logout');
    const additionalFields = document.getElementById('additional-fields');
    const adminEmailForm = document.getElementById('admin-email-form');
    const adminLoginForm = document.getElementById('admin-login-form');
    const loadUsersButton = document.getElementById('load-users');
    const usersTable = document.getElementById('users-table');

    function checkLogin() {
        const user = localStorage.getItem('user');
        if (user) {
            if (logoutButton) {
                logoutButton.style.display = 'block';
            }
            if (buttonInit) {
                buttonInit.innerText = 'Continuar PDS';
            }
        } else {
            if (logoutButton) {
                logoutButton.style.display = 'none';
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
            formData.append('action', 'login'); // Adiciona a ação de login ao formData

            fetch('process_form.php', {
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
                        // Ocultar o modal de login
                        loginModal.style.display = 'none';
                        // Redirecionar para a página do quiz
                        window.location.href = data.redirect || 'quiz.html';
                    } else if (data.status === 'user_not_found') {
                        // Redirecionar para a página de cadastro
                        alert(data.message);
                        window.location.href = data.redirect;
                    } else {
                        console.error('Erro:', data);
                        alert(data.message);
                        // Mostrar campos adicionais se necessário
                        if (data.message.includes('dados de cadastro incompletos')) {
                            additionalFields.style.display = 'block';
                            loginModal.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    // Para o cadastro de novo usuário
    const cadastroForm = document.getElementById('cadastro-form');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(cadastroForm);
            formData.append('action', 'cadastro'); // Adiciona a ação de cadastro ao formData

            fetch('process_form.php', {
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
                        localStorage.setItem('user', formData.get('novo_usuario'));
                        alert('Cadastro realizado com sucesso! Redirecionando para o Quiz PDS...');
                        // Redirecionar para a página do quiz
                        setTimeout(() => {
                            window.location.href = 'quiz.html';
                        }, 3000); // Redireciona após 3 segundos
                    } else {
                        console.error('Erro:', data);
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    // Para cadastrar o email do administrador
    if (adminEmailForm) {
        adminEmailForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(adminEmailForm);
            formData.append('action', 'cadastrar_email_admin'); // Adiciona a ação de cadastro de email do administrador

            fetch('process_form.php', {
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
                        alert('Email do administrador cadastrado com sucesso!');
                    } else {
                        console.error('Erro:', data);
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    // Para o login do administrador
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(adminLoginForm);
            formData.append('action', 'admin_login'); // Adiciona a ação de login do administrador ao formData

            fetch('process_form.php', {
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
                        alert(data.message);
                        // Redirecionar para a página do dashboard do administrador
                        window.location.href = data.redirect || 'admin_dashboard.html';
                    } else {
                        console.error('Erro:', data);
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    // Para carregar os dados dos usuários
    if (loadUsersButton) {
        loadUsersButton.addEventListener('click', function () {
            console.log('Botão carregar dados dos usuários clicado.'); // Adicionado para depuração
            fetch('process_form.php', {
                method: 'POST',
                body: new URLSearchParams({ action: 'get_users' })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data); // Adicionado para depuração
                    if (data.status === 'success') {
                        const usersTableBody = usersTable.querySelector('tbody');
                        usersTableBody.innerHTML = ''; // Limpar tabela antes de adicionar novos dados
                        data.usuarios.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${user.nome}</td>
                                <td>${user.email}</td>
                                <td>${user.telefone}</td>
                            `;
                            usersTableBody.appendChild(row);
                        });
                        usersTable.style.display = 'table';
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