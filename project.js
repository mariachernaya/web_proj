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
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    // Подготовка данных языка
    const langs = Array.from(form.querySelectorAll('select[name="language[]"] option:checked')).map(opt => opt.value);
    formData.delete('language[]');
    langs.forEach(lang => formData.append('language[]', lang));

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

        // Проверка типа содержимого
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new TypeError("Ожидался JSON-ответ, получен: " + contentType);
        }

        const data = await response.json();
        console.log("Получен ответ:", data); // Для отладки

        // Очистка предыдущих сообщений и ошибок
        document.querySelectorAll('.error').forEach(el => el.innerHTML = '');
        document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
        document.querySelector('.mess').innerHTML = '';
        document.querySelector('.mess_info').innerHTML = '';

        // Обработка выхода
        if (data.logout) {
            form.reset();
            document.querySelector('.edbut').style.display = 'none';
            document.querySelector('[name="logout_form"]').style.display = 'none';
            document.querySelector('.btnlike').style.display = 'inline-block';
            document.getElementById('credentials').style.display = 'none';
            return;
        }

        // Показ сообщений
        if (data.messages) {
            if (data.messages.success) {
                document.querySelector('.mess').innerHTML = data.messages.success;
            }
            if (data.messages.info) {
                document.querySelector('.mess_info').innerHTML = data.messages.info;
            }
        }

        // Показ ошибок
        if (data.errors) {
            Object.keys(data.errors).forEach(field => {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                if (errorElement) {
                    errorElement.innerHTML = data.messages[field] || '';
                }
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.toggle('red', data.errors[field]);
                }
            });
        }

        // Показ сгенерированных данных
        if (data.generated) {
            document.getElementById('generatedLogin').textContent = data.generated.login;
            document.getElementById('generatedPass').textContent = data.generated.pass;
            document.getElementById('credentials').style.display = 'block';
        }

        // Обновление состояния формы
        if (data.log) {
            document.querySelector('.edbut').style.display = 'inline-block';
            document.querySelector('[name="logout_form"]').style.display = 'inline-block';
            document.querySelector('.btnlike').style.display = 'none';
        } else {
            document.querySelector('.edbut').style.display = 'none';
            document.querySelector('[name="logout_form"]').style.display = 'none';
            document.querySelector('.btnlike').style.display = 'inline-block';
        }

    } catch (error) {
        
        //alert('Произошла ошибка при обработке запроса: ' + error.message);
    }
});
// После обработки данных
document.querySelectorAll('.mess').forEach(el => {
    el.style.display = 'block';
});
