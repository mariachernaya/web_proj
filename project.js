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

function updateFormButtons(isLoggedIn) {
    const submitBtn = document.querySelector('.submit-btn');
    const editBtn = document.querySelector('.edit-btn');
    const logoutBtn = document.querySelector('.logout-btn');
    const loginBtn = document.querySelector('.login-btn');
    
    // Всегда показываем "Отправить" и "Войти" при выходе
    if (submitBtn) submitBtn.style.display = isLoggedIn ? 'none' : 'inline-block';
    if (editBtn) editBtn.style.display = isLoggedIn ? 'inline-block' : 'none';
    if (logoutBtn) logoutBtn.style.display = isLoggedIn ? 'inline-block' : 'none';
    if (loginBtn) loginBtn.style.display = isLoggedIn ? 'none' : 'inline-block';
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', () => {
    updateFormButtons(<?= isset($_SESSION['login']) ? 'true' : 'false' ?>);
});

document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    
    // Очищаем предыдущие ошибки
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
    document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));

    try {
        const formData = new FormData(form);
        
        // Добавляем языки программирования
        const langs = Array.from(form.querySelectorAll('select[name="language[]"] option:checked')).map(opt => opt.value);
        formData.delete('language[]');
        langs.forEach(lang => formData.append('language[]', lang));

        const response = await fetch('index.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        console.log("Ответ сервера:", data);

        // Обработка ошибок валидации
        if (data.errors) {
            Object.entries(data.errors).forEach(([field, hasError]) => {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                const input = form.querySelector(`[name="${field}"]`);
                
                if (errorElement && data.messages?.[field]) {
                    errorElement.textContent = data.messages[field];
                    errorElement.style.display = 'block';
                }
                
                if (input && hasError) {
                    input.classList.add('red');
                }
            });
            return; // Прекращаем выполнение при ошибках
        }

        // Обработка успешной отправки
        if (data.success) {
            // Показываем сообщение об успехе
            if (data.messages?.success) {
                const successElement = document.querySelector('.mess');
                if (successElement) {
                    successElement.textContent = data.messages.success;
                    successElement.style.display = 'block';
                }
            }
            
            // Обновляем кнопки
            updateFormButtons(data.log);
            
            // Показываем сгенерированные данные, если есть
            if (data.generated) {
                const loginElement = document.getElementById('generatedLogin');
                const passElement = document.getElementById('generatedPass');
                const credentialsElement = document.getElementById('credentials');
                
                if (loginElement) loginElement.textContent = data.generated.login;
                if (passElement) passElement.textContent = data.generated.pass;
                if (credentialsElement) credentialsElement.style.display = 'block';
            }
        }

    } catch (error) {
        //console.error('Ошибка:', error);
       // const errorElement = document.querySelector('.mess');
        // if (errorElement) {
        //     errorElement.textContent = 'Произошла ошибка при отправке формы';
        //     errorElement.style.display = 'block';
        // }
    }
});

