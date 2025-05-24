if (window.location.hash === '#form-anchor') {
    const anchor = document.getElementById('form-anchor');
    if (anchor) anchor.scrollIntoView();
}

document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Подготовка данных языка
    const langSelect = form.querySelector('select[name="language[]"]');
    if (langSelect) {
        const langs = Array.from(langSelect.selectedOptions).map(opt => opt.value);
        formData.delete('language[]');
        langs.forEach(lang => formData.append('language[]', lang));
    }

    const isLogout = e.submitter && e.submitter.name === 'logout_form';
    if (isLogout) {
        formData.append('logout_form', '1');
    }

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Проверка ответа
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("Failed to parse JSON:", text);
            throw new Error("Invalid JSON response");
        }

        console.log("Server response:", data);

        // Очистка предыдущих сообщений и ошибок
        document.querySelectorAll('.error').forEach(el => el.textContent = '');
        document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
        document.querySelectorAll('.mess').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        // Обработка выхода
        if (data.logout) {
    updateFormButtons(false); 
    document.getElementById('credentials')?.style.display = 'none';
    
    // Показ сообщения
    const messElement = document.querySelector('.mess');
    if (messElement) {
        messElement.textContent = data.messages?.success || '';
        messElement.style.display = 'block';
    }
    return;
}

        // Показ сообщений
if (data.messages) {
    const messElement = document.querySelector('.mess');
    const messInfoElement = document.querySelector('.mess_info');
    
    // Основное сообщение
    if (data.messages.success && messElement) {
        messElement.textContent = data.messages.success;
        messElement.style.display = 'block';
    }
    
    // Дополнительная информация
    if (data.messages.info && messInfoElement) {
        messInfoElement.innerHTML = data.messages.info; 
        messInfoElement.style.display = 'block';
    }
}
        // Показ ошибок
        if (data.errors) {
            Object.entries(data.errors).forEach(([field, hasError]) => {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                const input = form.querySelector(`[name="${field}"]`);
                
                if (errorElement && data.messages && data.messages[field]) {
                    errorElement.textContent = data.messages[field];
                }
                
                if (input) {
                    if (hasError) {
                        input.classList.add('red');
                    } else {
                        input.classList.remove('red');
                    }
                }
            });
        }

        // Показ сгенерированных данных
        if (data.generated) {
            const loginElement = document.getElementById('generatedLogin');
            const passElement = document.getElementById('generatedPass');
            const credentialsElement = document.getElementById('credentials');
            
            if (loginElement) loginElement.textContent = data.generated.login;
            if (passElement) passElement.textContent = data.generated.pass;
            if (credentialsElement) credentialsElement.style.display = 'block';
        }

        // Обновление состояния формы
        updateFormButtons(data.log);

    } catch (error) {
        console.error('Error:', error);
        const messElement = document.querySelector('.mess');
        if (messElement) {
            messElement.textContent = 'Изменены';
            messElement.style.display = 'block';
        }
    }
});

// Функция для обновления состояния кнопок
function updateFormButtons(isLoggedIn) {
    const edbut = document.querySelector('.edbut'); // "Изменить"
    const logoutBtn = document.querySelector('[name="logout_form"]'); // "Выйти"
    const btnlike = document.querySelector('.btnlike'); // "Войти"

    // Для авторизованных
    if (edbut) edbut.style.display = isLoggedIn ? 'inline-block' : 'none';
    if (logoutBtn) logoutBtn.style.display = isLoggedIn ? 'inline-block' : 'none';
    
    // Для неавторизованных
    if (btnlike) btnlike.style.display = isLoggedIn ? 'none' : 'inline-block'; 
}
