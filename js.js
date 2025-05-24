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

if (window.location.hash === '#form-anchor') {
    const anchor = document.getElementById('form-anchor');
    if (anchor) anchor.scrollIntoView();
}


document.getElementById('ajaxForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Очистка предыдущих сообщений
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
    document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
    document.querySelectorAll('.mess').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        console.log("Server response:", data);

        // Обработка сообщений
        if (data.messages) {
            if (data.messages.success) {
                const messElement = document.querySelector('.mess');
                if (messElement) {
                    messElement.textContent = data.messages.success;
                    messElement.style.display = 'block';
                }
            }
            if (data.messages.info) {
                const messInfoElement = document.querySelector('.mess_info');
                if (messInfoElement) {
                    messInfoElement.innerHTML = data.messages.info;
                    messInfoElement.style.display = 'block';
                }
            }
        }

        // Обработка ошибок
        if (data.errors) {
            Object.entries(data.errors).forEach(([field, hasError]) => {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                const input = form.querySelector(`[name="${field}"]`);
                
                if (errorElement && data.messages && data.messages[field]) {
                    errorElement.textContent = data.messages[field];
                }
                
                if (input && hasError) {
                    input.classList.add('red');
                }
            });
        }

        // Обновление состояния формы
        updateFormButtons(data.log);

    } catch (error) {
        console.error('Error:', error);
        const messElement = document.querySelector('.mess');
        if (messElement) {
            messElement.textContent = 'Ошибка при отправке формы';
            messElement.style.display = 'block';
        }
    }
});
