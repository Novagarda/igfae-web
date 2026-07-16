'use strict'

export default class Igfae {
  constructor() {
    this.config = {
      letAnimateBlueLoading: false
    }
  }

  handler() {

    moverCaixasEmerxentes();
    moverCaixaFiltros();
    moverCaixaPersoa();
    abrirFiltros();

    // AJAX IGFAE PLUGIN
    const igfaeResultsSubmitBtn = document.querySelector("#igfae-search-form input[type='submit']");
    if (igfaeResultsSubmitBtn) {
      igfaeResultsSubmitBtn.addEventListener("click", function (event) {
        sendForm(document.querySelector('#igfae-search-form'), new FormData(document.querySelector('#igfae-search-form')));
        event.preventDefault();

        if (mobileCheck()) {
          const caixaFiltros = document.getElementById('caixa-filtros');
          caixaFiltros.classList.remove("ver");
        }
      });
    }

    const igfaeResultsDiv = document.querySelector("#igfae-search-results");
    if (igfaeResultsDiv) {
      igfaeResultsDiv.addEventListener("click", function (event) {

        if (event.target.closest("#igfae-pagination li.disabled > a")) {
          event.preventDefault();
        }

        if (event.target.closest("#igfae-pagination li:not(.disabled) > a")) {
          //event.preventDefault();
          var page = event.target.dataset.page;
          var formData = new FormData(document.querySelector("#igfae-search-form"));
          formData.append('igfae-page', page);
          sendForm(document.querySelector("#igfae-search-form"), formData);
        }

      });
    }

    function sendForm(form, data) {
      console.log(data);
      onFormBeforeSend(form, xhr);
      var xhr = new XMLHttpRequest();
      xhr.open(form.getAttribute("method"), form.getAttribute("action"), true);
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          onFormSuccess(form, xhr.response, xhr.statusText, xhr);
        } else {
          onFormError(form, xhr, xhr.statusText, xhr.statusText);
        }
        onFormComplete(form, xhr, xhr.statusText);
      };
      xhr.onerror = function () {
        onFormError(form, xhr, xhr.statusText, xhr.statusText);
        onFormComplete(form, xhr, xhr.statusText);
      };
      xhr.send(data);
    }

    function onFormBeforeSend(form, xhr, settings) {
      const loadingLayer = document.querySelector('#loading-full')
      loadingLayer.anim.play()
      document.querySelector('#igfae-search-results').classList.toggle('hidden');
    }

    function onFormSuccess(form, response) {
      document.querySelector('#igfae-search-results').innerHTML = response;
    }

    function onFormError(form, xhr, textStatus, errorThrown) {
      console.log('onFormError: ' + textStatus);
    }

    function onFormComplete(form, xhr, textStatus) {
      moverCaixasEmerxentes();

      setTimeout(() => {
        const loadingLayer = document.querySelector('#loading-full')
        loadingLayer.anim.reverse()
      }, 10);

      document.querySelector('#igfae-search-results').classList.toggle('hidden');
    }

    // MOVER CAIXAS EMERXENTES
    function moverCaixasEmerxentes() {

      const emerxentesContenedorOld = document.querySelector('.emerxentes-contenedor');
      if (emerxentesContenedorOld) {
        emerxentesContenedorOld.remove();
      }

      const emerxentes = document.querySelectorAll('.caixa-emerxente');
      if (emerxentes) {

        const emerxentesContenedor = document.createElement("div");
        emerxentesContenedor.classList.add("emerxentes-contenedor");
        document.body.appendChild(emerxentesContenedor);

        emerxentes.forEach(emerxente => {
          emerxentesContenedor.appendChild(emerxente);
        });
      }
    }

    // MOVER CAIXA DE FILTROS
    function moverCaixaFiltros() {

      const filtrosContenedorOld = document.querySelector('.filtros-contenedor');
      if (filtrosContenedorOld) {
        filtrosContenedorOld.remove();
      }

      const filtros = document.querySelector("#caixa-filtros");
      if (filtros) {
        const filtrosContenedor = document.createElement("div");
        filtrosContenedor.classList.add("filtros-contenedor");
        document.body.appendChild(filtrosContenedor);
        filtrosContenedor.appendChild(filtros);
      }
    }

    //MOVER CAIXA PERSOA
    function moverCaixaPersoa() {

      const persoaContenedorOld = document.querySelector('.persoa-contenedor');
      if (persoaContenedorOld) {
        persoaContenedorOld.remove();
      }

      const caixaPersoa = document.querySelector(".persoa-ficha");
      if (caixaPersoa) {
        const persoaWrapper = document.getElementById('persoa-wrapper');
        const persoaContenedor = document.createElement("div");
        const navTop = document.getElementById('navtop-mob');
        const altoNavTop = navTop.clientHeight;

        persoaContenedor.classList.add("persoa-contenedor");
        persoaContenedor.style.top = altoNavTop + "px";
        persoaWrapper.appendChild(persoaContenedor);
        persoaContenedor.appendChild(caixaPersoa);
        setTimeout(function () {
          caixaPersoa.classList.add("ver");
        }, 10);
      }
    }

    // ABRIR FILTROS
    function abrirFiltros() {

      const btnFiltros = document.getElementById('btn-filtros');
      if (btnFiltros) {
        const caixaFiltros = document.getElementById('caixa-filtros');
        const btnCerrarFiltros = document.getElementById('close-filtros');

        btnFiltros.addEventListener('click', (e) => {
          caixaFiltros.classList.add("ver");
        });

        btnCerrarFiltros.addEventListener('click', (e) => {
          caixaFiltros.classList.remove("ver");
        });
      }

      // Interacción botóns filtros
      // radios
      const botonsFiltros = document.querySelectorAll(".form-bloque .radios li label");
      if (botonsFiltros) {
        botonsFiltros.forEach(botonFiltro => {
          botonFiltro.addEventListener('click', (e) => {
            const radioBoton = botonFiltro.parentNode.querySelector('input');
            if (radioBoton.checked) {
              e.preventDefault();
              const allRadioLi = radioBoton.parentNode.parentNode.firstElementChild;
              const allRadioInput = allRadioLi.querySelector('input');
              allRadioInput.checked = true;
            }

          });
        });
      }
      // checkboxs
      const chekboxsFiltros = document.querySelectorAll(".form-bloque .checkboxs li label");

      if (chekboxsFiltros.length > 0) {
        const chekboxsAllFiltros = document.querySelector(".form-bloque .checkboxs li label.all");
        // boton 'all'
        chekboxsAllFiltros.addEventListener('click', (e) => {
          chekboxsFiltros.forEach(checkBtnLabel => {
            const checkBoton = checkBtnLabel.parentNode.querySelector('input');
            checkBoton.checked = false;
          });
        });
        // demais botons
        chekboxsFiltros.forEach(checkBtnLabel => {
          checkBtnLabel.addEventListener('click', (e) => {
            const checkBotonAll = chekboxsAllFiltros.parentNode.querySelector('input');
            checkBotonAll.checked = false;
          });
        });
      }
    }

    function mobileCheck() {
      let check = false;
      (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
      return check;
    }

    // Xestión links que disparan a animación de carga azul
    const linksLoading = document.querySelectorAll("a");

    if (linksLoading) {
      linksLoading.forEach(link => {
        link.addEventListener('click', (e) => {

          if(link.classList.contains('carga-loading')){
            this.config.letAnimateBlueLoading = true;
          }else{
            this.config.letAnimateBlueLoading = false;
          }

        });
      });
    }   
  } // FIN handler

  createLoadingAnimation() {
    const loadingLayer = document.querySelector('#loading-full')

    let tl = new gsap.timeline({ paused: true })
    tl.set('#loading-full', { display: 'block' })
    tl.to('#loading-full-inner', { duration: .2, autoAlpha: 1 })

    loadingLayer.anim = tl
  }

  // Función para limpar as restos en canto se chama á animación do swup dende main.js
  limparAoInicio() {
    escondeVenta('#caixa-filtros');
    escondeVenta('.caixa-emerxente.ver');
    escondeVenta('.persoa-ficha');

    function escondeVenta(elemento) {
      const ventaAberta = document.querySelector(elemento);
      if (ventaAberta) {
        ventaAberta.classList.remove("ver");
      }
    }

    if(this.config.letAnimateBlueLoading){
      const loadingLayer = document.querySelector('#loading-full');
      loadingLayer.classList.add('ver');
      loadingLayer.anim.play()
    }

    // Quitamos o buscador si está aberto
    const buscarContenedor = document.getElementById('buscar_container');
    if (buscarContenedor.classList.contains('ver')) {
      buscarContenedor.classList.remove('ver');
    }
  }

  limparAoFinal(){
    const loadingLayer = document.querySelector('#loading-full')    
    setTimeout(() => {
      loadingLayer.anim.reverse()
    }, 10);

  }

  init() {
    this.handler()
  }

  once(){
    this.createLoadingAnimation()
  }
}
