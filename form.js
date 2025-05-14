  document.getElementById('showRegisterBtn').addEventListener('click', function() {
            showForm('registerForm');
        });
        
        document.getElementById('showLoginBtn').addEventListener('click', function() {
            showForm('loginForm');
        });
        
        document.getElementById('logoutBtn').addEventListener('click', function() {
            showForm('registerForm');
            document.getElementById('responseContainer').innerHTML = '';
        });
        function showForm(formId) {
            document.querySelectorAll('.form-section').forEach(form => {
                form.classList.remove('active-form');
            });
            document.getElementById(formId).classList.add('active-form');
        }

        // Обработка регистрации
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('http://u68790.kubsu-dev.ru/web_proj/sumbit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('responseContainer');
                responseDiv.innerHTML = '';
                
                if (data.success) {
                    let successHtml = `
                        <div class="alert-success">
                            <p>${data.message}</p>
                            <div class="credentials-box">
                                <h3>Ваши учетные данные:</h3>
                                <table>
                                    <tr><th>Логин:</th><td><code style="color: black">${data.credentials.login}</code></td></tr>
                                    <tr><th>Пароль:</th><td><code style="color: black">${data.credentials.password}</code></td></tr>
                                </table>
                                <p class="warning">Сохраните эти данные! Пароль нельзя восстановить!</p>
                                <button class="close-btn">Закрыть</button>
                            </div>
                        </div>
                    `;
                    responseDiv.innerHTML = successHtml;
                    this.reset();
                    
                    document.querySelector('.close-btn').addEventListener('click', function() {
                        responseDiv.style.display = 'none';
                    });
                } else if (data.errors) {
                    let errorsHtml = '<ul>';
                    for (const [field, error] of Object.entries(data.errors)) {
                        errorsHtml += `<li>${error}</li>`;
                    }
                    errorsHtml += '</ul>';
                    responseDiv.innerHTML = errorsHtml;
                    responseDiv.className = 'error';
                } else {
                    responseDiv.textContent = data.error || 'Произошла ошибка';
                    responseDiv.className = 'error';
                }
                
                responseDiv.style.display = 'block';
            })
            .catch(error => {
                const responseDiv = document.getElementById('responseContainer');
                responseDiv.textContent = 'Ошибка: ' + error.message;
                responseDiv.className = 'error';
                responseDiv.style.display = 'block';
            });
        });
        
        // Обработка входа
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('http://u68790.kubsu-dev.ru/web_proj/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('responseContainer');
                responseDiv.innerHTML = '';
                
                if (data.success) {
                    // Заполняем форму редактирования
                    document.getElementById('editName').value = data.user.name;
                    document.getElementById('editEmail').value = data.user.email;
                    document.getElementById('editMessage').value = data.user.message;
                    
                    // Показываем форму редактирования
                    showForm('editForm');
                    
                    // Сообщение об успешном входе
                    responseDiv.innerHTML = '<div class="success">Вы успешно вошли в систему</div>';
                    responseDiv.style.display = 'block';
                } else {
                    responseDiv.innerHTML = '<div class="error">' + data.error + '</div>';
                    responseDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
        
        // Обработка редактирования данных
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('http://u68790.kubsu-dev.ru/web_proj/update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('responseContainer');
                responseDiv.innerHTML = '';
                
                if (data.success) {
                    responseDiv.innerHTML = '<div class="success">Данные успешно обновлены</div>';
                } else {
                    responseDiv.innerHTML = '<div class="error">' + data.error + '</div>';
                }
                
                responseDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
