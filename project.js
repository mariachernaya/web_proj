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
        const data = await response.json();
  
        if (data.success) {
            if (data.generated){
            // Заполняем данные
            document.getElementById('generatedLogin').textContent = data.generated.login;
            document.getElementById('generatedPass').textContent = data.generated.pass;
               document.getElementById('credentials').style.display = 'block';
            // Очищаем форму 
            if (!data.log) e.target.reset();
        }
    } catch (error) {
        
    }
   // Обработка выхода
        if (data.logout) {
            form.reset();
            document.querySelectorAll('.error').forEach(el => el.innerHTML = '');
            document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
            document.querySelector('.edbut').style.display = 'none';
            document.querySelector('[name="logout_form"]').style.display = 'none';
            document.querySelector('.btnlike').style.display = 'inline-block';
            return;
        }
	    
        document.querySelector('.mess').innerHTML = data.messages.success || '';
        document.querySelector('.mess_info').innerHTML = data.messages.info || '';
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
	    
    

	      if (data.success) {
            Object.keys(data.values).forEach(key => {
                const elements = form.elements[key];
                if (!elements) return;
                
                if (elements instanceof RadioNodeList) {
                    elements.forEach(element => {
                        element.checked = (element.value === data.values[key]);
                    });
                } else if (key === 'language') {
                    // Обработка множественного выбора
                    Array.from(elements.options).forEach(option => {
                        option.selected = data.languages.includes(option.value);
                    });
                } else {
                    elements.value = data.values[key] || '';
                }
            });
        }
            const langSelect = form.querySelector('select[name="language[]"]');
            Array.from(langSelect.options).forEach(option => {
                option.selected = data.languages.includes(option.value);
            });
          
          if (data.log === false) {
          form.reset();
          document.querySelectorAll('.error').forEach(el => el.innerHTML = '');
          document.querySelectorAll('.input').forEach(el => el.classList.remove('red'));
            }
        

        if (data.log) {
            form.querySelector('.edbut').style.display = 'inline-block';
            form.querySelector('[name="logout_form"]').style.display = 'inline-block';
            form.querySelector('.btnlike').style.display = 'none';
        } else {
            form.querySelector('.edbut').style.display = 'none';
            form.querySelector('[name="logout_form"]').style.display = 'none';
            form.querySelector('.btnlike').style.display = 'inline-block';
        }
      if (formData.get('logout_form') !== null) {
    document.querySelectorAll('input, select, textarea').forEach(element => {
        if (element.type !== 'submit' && element.type !== 'button') {
            element.value = '';
            element.checked = false;
            element.selected = false;
        }
    });
}

	
    } catch (error) {
        
    }
});


