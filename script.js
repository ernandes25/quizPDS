document.addEventListener('DOMContentLoaded', function () {
    const loginModal = document.getElementById('login-modal');
    const loginForm = document.getElementById('login-form');
    const cadastroForm = document.getElementById('cadastro-form');
    const contactForm = document.getElementById('contact-form');
    const buttonInit = document.getElementById('button-init');
    const logoutButtonHeader = document.getElementById('logout-button-header');
    const logoutButtonMain = document.getElementById('logout-button-main');
    const additionalFields = document.getElementById('additional-fields');
    const adminEmailForm = document.getElementById('admin-email-form');
    const adminLoginForm = document.getElementById('admin-login-form');
    const loadUsersButton = document.getElementById('load-users');
    const usersTable = document.getElementById('users-table');
    const logoutButton = document.getElementById('logout');
    const userGreeting = document.getElementById('user-greeting');
    const loadingMessageCadastro = document.getElementById('loading-message-cadastro');
    const loadingMessageContact = document.getElementById('loading-message-contact');
    

    let isSubmittingAdminEmailForm = false;
    let isSubmittingContactForm = false;

    function showLoadingMessage(messageElement) {
        if (messageElement) {
            messageElement.style.display = 'block';
        }
    }

    function hideLoadingMessage(messageElement) {
        if (messageElement) {
            messageElement.style.display = 'none';
        }
    }

    if (contactForm) {
        contactForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (isSubmittingContactForm) return;
            isSubmittingContactForm = true;

            const formData = new FormData(contactForm);
            formData.append('action', 'contato');

            showLoadingMessage(loadingMessageContact);

            fetch('process_form.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    hideLoadingMessage(loadingMessageContact);
                    isSubmittingContactForm = false;
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        alert('Mensagem enviada com sucesso!');
                        window.location.href = 'contact_success.html';
                    } else {
                        alert('Erro ao enviar mensagem: ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoadingMessage(loadingMessageContact);
                    isSubmittingContactForm = false;
                    alert('Ocorreu um erro ao enviar sua mensagem. Por favor, tente novamente mais tarde.');
                });
        });
    }

    function checkLogin() {
        const user = localStorage.getItem('user');
        const admin = localStorage.getItem('admin');
        if (user) {
            if (logoutButton) {
                logoutButton.style.display = 'block';
            }
            if (logoutButtonHeader) {
                logoutButtonHeader.style.display = 'block';
            }
            if (buttonInit) {
                buttonInit.innerText = 'CONTINUAR PDS';
                buttonInit.style.display = 'block';
            }
            if (userGreeting) {
                userGreeting.innerText = `Bem vindo, ${user}`;
            }
        } else {
            if (logoutButton) {
                logoutButton.style.display = 'none';
            }
            if (logoutButtonHeader) {
                logoutButtonHeader.style.display = 'none';
            }
            if (buttonInit) {
                buttonInit.innerText = 'INICIAR PDS';
                buttonInit.style.display = 'block';
            }
            if (userGreeting) {
                userGreeting.innerText = '';
            }
        }
        if (admin) {
            if (logoutButtonMain) {
                logoutButtonMain.style.display = 'block';
            }
            if (userGreeting) {
                userGreeting.innerHTML = 'Você está logado como<br>ADMINISTRADOR.';
            }
        } else {
            if (logoutButtonMain) {
                logoutButtonMain.style.display = 'none';
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
        checkLogin();
        window.location.href = 'index.html';
    }

    function adminLogout() {
        localStorage.removeItem('admin');
        checkLogin();
        window.location.href = 'index.html';
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            formData.append('action', 'login');

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
                    if (data.status === 'success') {
                        localStorage.setItem('user', formData.get('usuario'));
                        window.location.href = data.redirect || 'quiz.html';
                    } else if (data.status === 'user_not_found') {
                        if (confirm('Usuário não encontrado. Deseja efetuar seu cadastro agora?')) {
                            window.location.href = 'cadastro.html';
                        }
                    } else {
                        alert(data.message);
                        if (data.message.includes('dados de cadastro incompletos')) {
                            additionalFields.style.display = 'block';
                            loginModal.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    if (cadastroForm) {
        cadastroForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(cadastroForm);
            const senha = formData.get('nova_senha');
            const email = formData.get('email');

            if (!/^\d{4}$/.test(senha)) {
                alert('A senha deve conter exatamente 4 caracteres numéricos.');
                return;
            }

            if (!/\S+@\S+\.\S+/.test(email)) {
                alert('Por favor, insira um endereço de email válido.');
                return;
            }

            formData.append('action', 'cadastro');
            formData.append('novo_usuario', email);

            showLoadingMessage(loadingMessageCadastro);

            fetch('process_form.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    hideLoadingMessage(loadingMessageCadastro);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        localStorage.setItem('user', formData.get('novo_usuario'));
                        alert('Cadastro realizado com sucesso! Redirecionando para o Quiz PDS...');
                        setTimeout(() => {
                            window.location.href = 'quiz.html';
                        }, 3000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    hideLoadingMessage(loadingMessageCadastro);
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    if (adminEmailForm) {
        adminEmailForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (isSubmittingAdminEmailForm) return;
            isSubmittingAdminEmailForm = true;

            const formData = new FormData(adminEmailForm);
            formData.append('action', 'cadastrar_email_admin');

            showLoadingMessage(loadingMessageContact);

            fetch('process_form.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        hideLoadingMessage(loadingMessageContact);
                        isSubmittingAdminEmailForm = false;
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoadingMessage(loadingMessageContact);
                    isSubmittingAdminEmailForm = false;
                    if (data.status === 'success') {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    hideLoadingMessage(loadingMessageContact);
                    isSubmittingAdminEmailForm = false;
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(adminLoginForm);
            formData.append('action', 'admin_login');

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
                    if (data.status === 'success') {
                        localStorage.setItem('admin', formData.get('email'));
                        window.open(data.redirect || 'admin_dashboard.html', '_blank');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

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

    function saveQuizResult(score) {
        const user = localStorage.getItem('user');
        if (!user) {
            alert('Usuário não encontrado. Faça login novamente.');
            window.location.href = 'login.html';
            return;
        }

        const data = { action: 'save_quiz_result', user: user, score: score };

        fetch('process_form.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Erro ao salvar resultado do quiz: ' + data.message);
                }
            })
            .catch(error => {
                alert('Erro ao salvar resultado do quiz. Tente novamente mais tarde.');
            });
    }

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

                    saveQuizResult(score);

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

    if (loadUsersButton) {
        loadUsersButton.addEventListener('click', function () {
            fetch('process_form.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'get_users' })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const usersTableBody = usersTable.querySelector('tbody');
                        usersTableBody.innerHTML = '';
                        data.usuarios.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${user.nome}</td>
                                <td>${user.email}</td>
                                <td>${user.telefone}</td>
                                <td>${user.quiz_result !== null ? user.quiz_result : 'N/A'}</td>
                                <td>${user.data_hora_quiz !== null ? user.data_hora_quiz : 'N/A'}</td>
                            `;
                            usersTableBody.appendChild(row);
                        });
                        usersTable.style.display = 'table';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.');
                });
        });
    }

    if (logoutButtonHeader) {
        logoutButtonHeader.addEventListener('click', function () {
            logout();
        });
    }

    if (logoutButtonMain) {
        logoutButtonMain.addEventListener('click', function () {
            adminLogout();
        });
    }

    checkLogin();
    window.startPDS = startPDS;
    window.logout = logout;
});
