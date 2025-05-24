window.onload = function () {
  let start = false;
  function slicker() {
    let vw = window.innerWidth;
    let vh = window.innerHeight;
    console.log(vh, vw);
    if (start) {
      $(".autoplay").slick("unslick");
    }

    
    if (vw >= 1000) {
      $(".autoplay").slick({
        arrows: false,
        dots: true,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
      });
      setTimeout(function () {
        $(".autoplay2").slick({
          arrows: false,
          dots: true,
          infinite: true,
          slidesToShow: 5,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 2000,
        });
      }, 800);
    } else if (vw >= 600) {
      $(".autoplay").slick({
        arrows: false,
        dots: true,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
      });
      setTimeout(function () {
        $(".autoplay2").slick({
          arrows: false,
          dots: true,
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 2000,
        });
      }, 800);
    } else if (vw <= 480) {
      $(".autoplay").slick({
        arrows: false,
        dots: true,
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
      });
      setTimeout(function () {
        $(".autoplay2").slick({
          arrows: false,
          dots: true,
          infinite: true,
          slidesToShow: 2,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 2000,
        });
      }, 800);
    }
  }
  slicker();
  start = true;

  window.addEventListener("resize", function () {
    slicker();
  });
};

$(".mob_menu").on("click", function () {
  $("body").toggleClass("menu_active");
});

$(".a").css("height", $(".aa > div:eq(0)").height());
function aa(p) {
  console.log(p);
  $(".aa > div").css("opacity", "0");
  setTimeout(function () {
    $(".aa > div").css("display", "block");
  }, 0);
  $(".aa > div:eq(" + p + ")").css("display", "block");
  setTimeout(function () {
    $(".aa > div:eq(" + p + ")").css("opacity", "1");
  }, 0);

  setTimeout(function () {
    $(".a").animate(
      {
        height: $(".aa > div:eq(" + p + ")").height(),
      },
      300,
      "linear"
    );
  }, 100);

  $(".ednum").html((p + 1).toString().padStart(2, "0"));
}

(p = 0), (pl = $(".aa > div").length - 1);
$(".b2").on("click", function () {
  if (p == 0) p = pl;
  else p--;
  aa(p);
});
$(".b1").on("click", function () {
  if (p == pl) p = 0;
  else p++;
  aa(p);
});


/*Footer*/
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
  return;
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
