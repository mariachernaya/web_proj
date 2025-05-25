// Функция для обновления состояния кнопок
function updateFormButtons(isLoggedIn) {
    const submitBtn = document.querySelector('.button[type="submit"]:not([name])');
    const edbut = document.querySelector('.edbut');
    const logoutBtn = document.querySelector('[name="logout_form"]');
    const btnlike = document.querySelector('.btnlike');

    if (isLoggedIn) {
        // Показываем кнопки для авторизованных
        if (edbut) edbut.style.display = 'inline-block';
        if (logoutBtn) logoutBtn.style.display = 'inline-block';
        // Скрываем кнопки для неавторизованных
        if (submitBtn) submitBtn.style.display = 'none';
        if (btnlike) btnlike.style.display = 'none';
    } else {
        // Показываем кнопки для неавторизованных
        if (submitBtn) submitBtn.style.display = 'inline-block';
        if (btnlike) btnlike.style.display = 'inline-block';
        // Скрываем кнопки для авторизованных
        if (edbut) edbut.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'none';
    }
}

if (window.location.hash === '#form-anchor') {
    const anchor = document.getElementById('form-anchor');
    if (anchor) anchor.scrollIntoView();
}
document.getElementById('ajaxForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Очистка предыдущих сообщений и ошибок
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
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        console.log("Server response:", data);

        // Обработка сообщений
        const messElement = document.querySelector('.mess');
        const messInfoElement = document.querySelector('.mess_info');
        
        if (data.messages) {
            if (data.messages.success && messElement) {
                messElement.textContent = data.messages.success;
                messElement.style.display = 'block';
            }
            
            if (data.messages.info && messInfoElement) {
                messInfoElement.innerHTML = data.messages.info;
                messInfoElement.style.display = 'block';
            }
        }

        // Обработка выхода из системы
        if (data.logout) {
            // Очищаем форму
            form.reset();
            
            // Обновляем кнопки
            updateFormButtons(false);
            
            // Очищаем выбранные языки
            const langSelect = form.querySelector('select[name="language[]"]');
            if (langSelect) {
                Array.from(langSelect.options).forEach(option => {
                    option.selected = false;
                });
            }
            
            // Показываем сообщение
            if (messElement) {
                messElement.textContent = data.messages?.success || 'Вы вышли из системы';
                messElement.style.display = 'block';
            }
            return;
        }

        // Обновление данных в форме после успешного изменения
        if (data.success && data.log) {
            updateFormButtons(true);
        }

        // Обработка ошибок валидации
        if (data.errors) {
            Object.entries(data.errors).forEach(([field, hasError]) => {
                const fieldName = field === 'language[]' ? 'language' : field;
                const errorElement = document.querySelector(`.error[data-field="${fieldName}"]`);
                const input = form.querySelector(`[name="${field}"]`) || 
                             form.querySelector(`[name="${fieldName}"]`);
                
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
