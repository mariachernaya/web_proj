// Функция для обновления состояния кнопок
function updateFormButtons(isLoggedIn) {
    document.querySelector('.submit-btn').style.display = isLoggedIn ? 'none' : 'inline-block';
    document.querySelector('.btnlike').style.display = isLoggedIn ? 'none' : 'inline-block';
    document.querySelector('.edbut').style.display = isLoggedIn ? 'inline-block' : 'none';
    document.getElementById('logoutBtn').style.display = isLoggedIn ? 'inline-block' : 'none';
}
// Обработчик кнопки выхода
document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'logout=1'
        });

        if (!response.ok) throw new Error('Ошибка выхода');
        
        const data = await response.json();
        
        if (data.logout) {
            // Очищаем форму
            document.getElementById('ajaxForm').reset();
            
            // Мгновенно переключаем кнопки
            document.querySelector('.submit-btn').style.display = 'inline-block';
            document.querySelector('.btnlike').style.display = 'inline-block';
            document.querySelector('.edbut').style.display = 'none';
            document.getElementById('logoutBtn').style.display = 'none';
            
            // Показываем сообщение
            const messElement = document.querySelector('.mess');
            if (messElement) {
                messElement.textContent = 'Вы успешно вышли из системы';
                messElement.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Ошибка при выходе:', error);
    }
});
document.getElementById('ajaxForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Определяем тип действия
    const action = formData.get('action');
    
    // Очистка сообщений
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
        
        // Обработка сообщений
        const messElement = document.querySelector('.mess');
        if (data.messages?.success && messElement) {
            messElement.textContent = data.messages.success;
            messElement.style.display = 'block';
        }
        
        if (data.messages?.info && document.querySelector('.mess_info')) {
            document.querySelector('.mess_info').innerHTML = data.messages.info;
            document.querySelector('.mess_info').style.display = 'block';
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
