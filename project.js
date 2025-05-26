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
            body: 'action=logout&logout=1' 
        });

        if (!response.ok) throw new Error('Ошибка выхода');
        
        const data = await response.json();
        
        if (data.logout) {
            document.getElementById('ajaxForm').reset();
            
            document.querySelector('.submit-btn').style.display = 'inline-block';
            document.querySelector('.btnlike').style.display = 'inline-block';
            document.querySelector('.edbut').style.display = 'none';
            document.getElementById('logoutBtn').style.display = 'none';
            
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

    const submitButton = document.querySelector('button[type="submit"]:focus');
    if (submitButton && submitButton.name === 'action') {
        formData.append(submitButton.name, submitButton.value);
    }
    const action = formData.get('action');
    
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
        
        const messElement = document.querySelector('.mess');
        if (data.messages?.success && messElement) {
            messElement.textContent = data.messages.success;
            messElement.style.display = 'block';
        }
        
        if (data.messages?.info && document.querySelector('.mess_info')) {
            document.querySelector('.mess_info').innerHTML = data.messages.info;
            document.querySelector('.mess_info').style.display = 'block';
        }
        if (data.errors) {
            for (const field in data.errors) {
                const errorElement = document.querySelector(`.error[data-field="${field}"]`);
                if (errorElement && data.messages[field]) {
                    errorElement.textContent = data.messages[field];
                }
            }
        }
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
