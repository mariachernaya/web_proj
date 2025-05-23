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
