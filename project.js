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
    document.getElementById('form-anchor').scrollIntoView();
}
document.querySelector('form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    
    try {
        const formData = new FormData(form);
        
        // Подготовка данных языка
        const langSelect = form.querySelector('select[name="language[]"]');
        if (langSelect) {
            const langs = Array.from(langSelect.selectedOptions).map(opt => opt.value);
            formData.delete('language[]');
            langs.forEach(lang => formData.append('language[]', lang));
        }

        const isLogout = e.submitter?.name === 'logout_form';
        if (isLogout) {
            formData.append('logout_form', '1');
        }

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

        console.log("Server response:", data); // Отладочная информация

        // Очистка предыдущих сообщений
        document.querySelectorAll('.error').forEach(el => el.textContent = '');
        document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));

        // Обработка выхода
        if (data.logout) {
            form.reset();
            toggleFormButtons(false);
            document.getElementById('credentials')?.style.display = 'none';
            return;
        }

        // Показ сообщений
        updateElement('.mess', data.messages?.success);
        updateElement('.mess_info', data.messages?.info);

        // Показ ошибок
        if (data.errors) {
            Object.entries(data.errors).forEach(([field, hasError]) => {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                const input = form.querySelector(`[name="${field}"]`);
                
                if (errorElement && data.messages?.[field]) {
                    errorElement.textContent = data.messages[field];
                }
                
                if (input) {
                    input.classList.toggle('red', hasError);
                }
            });
        }

        // Показ сгенерированных данных
        if (data.generated) {
            updateElement('#generatedLogin', data.generated.login);
            updateElement('#generatedPass', data.generated.pass);
            document.getElementById('credentials')?.style.display = 'block';
        }

        // Обновление состояния кнопок
        toggleFormButtons(data.log);

    } catch (error) {
        //console.error('Error:', error);
        //alert('Произошла ошибка: ' + error.message);
    }
});

// Вспомогательные функции
function toggleFormButtons(isLoggedIn) {
    const display = isLoggedIn ? 'inline-block' : 'none';
    document.querySelector('.edbut')?.style.display = display;
    document.querySelector('[name="logout_form"]')?.style.display = display;
    document.querySelector('.btnlike')?.style.display = isLoggedIn ? 'none' : 'inline-block';
}

function updateElement(selector, content) {
    const element = document.querySelector(selector);
    if (element && content !== undefined) {
        element.innerHTML = content;
        element.style.display = content ? 'block' : 'none';
    }
}
// После обработки данных
document.querySelectorAll('.mess').forEach(el => {
    el.style.display = 'block';
});

