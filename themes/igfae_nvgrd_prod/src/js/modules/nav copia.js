'use strict'

import Headroom from "headroom.js";

export default class Nav {
  constructor() {
    this.config = {
      navlink: '.navbar-link',
      currentClass: 'current-menu-item',
      visibleMenu: 0,
      presentMenu: 0
    }
    this.navbarToggler = document.querySelector('.navbar-toggler button')
    //    this.sidemenu = document.querySelector('.sidemenu')
  }

  headerHandler() {
    var myElement = document.querySelector("header");
    var headroom = new Headroom(myElement);
    headroom.init();
  }

  showSubnav() {

    // MENU Acordeon
    const menus = document.querySelectorAll('nav.nav');
    menus.forEach(menu => {
      menu.addEventListener("click", (event) => {
        const thisMenu = menu.getAttribute('id');
        if (thisMenu != this.config.visibleMenu) {
          menus.forEach(apagarMenu => {
            apagarMenu.classList.remove("visible");
          });
        }
        menu.classList.add("visible");
      });
      menu.addEventListener("mouseout", (event) => {
        this.config.visibleMenu = menu.getAttribute('id');
      });
    });

    // MENU PRINCIPAL
    const contenedorMenu = document.querySelector('.header-menu');
    const menuItems = document.querySelectorAll('.menu-item-has-children');
    const menuLinks = contenedorMenu.querySelectorAll('a');

    menuItems.forEach(menuItem => {
      var submenu = menuItem.querySelector('.subnav');
      const submenuHeight = submenu.clientHeight;
      submenu.setAttribute('style', '--alto-calculado:' + submenuHeight + 'px');
      if (!menuItem.classList.contains('current-menu-ancestor')) {
        submenu.classList.add('hide');
      } else {
        this.config.visibleMenu = menuItem.getAttribute('id');
        this.config.presentMenu = this.config.visibleMenu;
      }

      menuItem.addEventListener("click", (event) => {
        const thisMenu = menuItem.getAttribute('id');
        if (thisMenu != this.config.visibleMenu) {
          menuItems.forEach(apagarMenu => {
            var submenuApagarMenu = apagarMenu.querySelector('.subnav');
            apagarMenu.classList.remove("visible");
            submenu.classList.add("hide");
            if (apagarMenu.classList.contains('current-menu-ancestor')) {
              apagarMenu.classList.add('hide');
              submenuApagarMenu.classList.add('hide');
            }
          });
        }
        menuItem.classList.add("visible");
        menuItem.classList.remove("hide");
        if (menuItem.classList.contains('current-menu-ancestor')) {
          submenu.classList.add('hide');
        }
      });

      menuItem.addEventListener("mouseout", (event) => {
        this.config.visibleMenu = menuItem.getAttribute('id');
      });

    });

    menuLinks.forEach(menuLink => {

      menuLink.addEventListener("click", (event) => {

        menuLinks.forEach(resetMenuLink => {
          resetMenuLink.classList.remove('current-menu-item');
          resetMenuLink.parentNode.classList.remove('current-menu-item');
        });

        menuLink.classList.add('current-menu-item');
        this.config.presentMenu = this.config.visibleMenu;
      });

    });

    // reseteamos o menu á posición inicial
    contenedorMenu.addEventListener("mouseleave", (event) => {
      if (this.config.visibleMenu != this.config.presentMenu) {
        this.config.visibleMenu = this.config.presentMenu;
        menuItems.forEach(apagarMenu => {
          apagarMenu.classList.remove("visible");
          apagarMenu.classList.add("hide");
        });
        const menuPresentar = document.getElementById(this.config.presentMenu);
        if (menuPresentar) {
          menuPresentar.classList.add("visible");
          menuPresentar.classList.remove("hide");
        }
      }
    });

  }

  checkMenuContextualLinks() {

    const contextualLinks = document.querySelectorAll('.bread-link');

    contextualLinks.forEach(contextualLink => {

      contextualLink.addEventListener("click", (event) => {

        const currentLink = contextualLink.dataset.breadcurrent;
        const parentLink = contextualLink.dataset.breadparent;
        const currentElement = document.querySelector('.' + currentLink);
        const parentElement = document.querySelector('.' + parentLink);

        this.limparMenu();

        // abrimos o menu que corresponda
        parentElement.classList.add("visible");
        parentElement.classList.remove("hide");
        currentElement.classList.add("current-menu-item");
        this.config.visibleMenu = parentElement.getAttribute('id');
        this.config.presentMenu = this.config.visibleMenu;
      });

    });
  }

  // CLIC HOME
  checkHomeLink() {
    const homeLink = document.getElementById('home_link');
    homeLink.addEventListener("click", (event) => {
      this.limparMenu();
      this.config.visibleMenu = 0;
      this.config.presentMenu = 0;
    });
  }

  limparMenu() {
    const contenedorMenu = document.querySelector('.header-menu');
    const menuItems = document.querySelectorAll('.menu-item-has-children');
    const menuLinks = contenedorMenu.querySelectorAll('a');

    // Limpamos os elementos do menu
    menuItems.forEach(apagarMenu => {
      apagarMenu.classList.remove("visible");
      apagarMenu.classList.add("hide");
      var submenu = apagarMenu.querySelector('.subnav');
      submenu.classList.add("hide");
    });

    // Limpamos os enlaces do menu
    menuLinks.forEach(resetMenuLink => {
      resetMenuLink.classList.remove('current-menu-item');
      resetMenuLink.parentNode.classList.remove('current-menu-item');
    });
  }


  createSideMenuAnimation() {
    let tl = new gsap.timeline({ paused: true })
    tl.set(this.sidemenu, { autoAlpha: 1, display: 'block' })
    tl.to(this.navbarToggler, { rotation: 225, transformOrigin: "12px 12px" })
    tl.fromTo(this.sidemenu, { x: '-100%' }, { autoAlpha: 1, x: 0 })

    this.sidemenu.anim = tl
  }

  handlerSideMenu() {
    this.navbarToggler.addEventListener('click', () => {
      if (this.sidemenu.classList.contains('is-visible')) {
        this.hideSideMenu()
      } else {
        this.sidemenu.anim.play()
        this.sidemenu.classList.add('is-visible')
        document.body.classList.add('locked')
      }
    })

  }

  hideSideMenu() {
    this.sidemenu.anim.reverse()
    this.sidemenu.classList.remove('is-visible')
    document.body.classList.remove('locked')
  }

  setCurrentNavItem() {
    document.querySelectorAll(this.config.navlink).forEach((item, i) => {
      item.classList.remove(this.config.currentClass)

      if (window.location.href.includes(item.getAttribute('href'))) {
        item.classList.add(this.config.currentClass)
      }
    })
  }

  checkAnclasPosition() {
    // Selecciona el elemento sticky
    const stickyElement = document.querySelector('.nav-anclas');

    // Función que se ejecuta en el evento scroll
    function checkSticky() {
      // Calculamos ancho da ventá
      var anchoVenta = window.innerWidth;
      var topMargin = 0;
      if (anchoVenta < 769) {
        topMargin = 30;
      }
      // Verifica si el elemento está en la posición sticky (enganchado arriba)
      if (stickyElement.getBoundingClientRect().top === topMargin) {
        stickyElement.classList.add('sticky-activo');
      } else {
        stickyElement.classList.remove('sticky-activo');
      }
    }
    if (stickyElement) {
      window.addEventListener('scroll', checkSticky);
    }
  }

  // MENU BOCATA
  menuBocata() {
    const btnAbrirMenuBocata = document.getElementById('btn-bocata-abrir');
    const headerMenu = document.getElementById('header-principal');
    const navTopMob = document.getElementById('navtop-mob');

    btnAbrirMenuBocata.addEventListener('click', (e) => {
      headerMenu.classList.toggle("ver");
      navTopMob.classList.toggle("aberto");
    });

    const linksMainMenu = document.querySelectorAll(".navmain a");

    if (linksMainMenu) {
      linksMainMenu.forEach(linkMainMenu => {
        linkMainMenu.addEventListener('click', (e) => {
          headerMenu.classList.toggle("ver");
          navTopMob.classList.toggle("aberto");
        });
      });
    }
  }

  btnSearchInteraction() {
    const buscarContenedor = document.getElementById('buscar_container');
    const buscarBtn = document.getElementById('btn_buscar');
    const headerMenu = document.getElementById('header-principal');
    const navTopMob = document.getElementById('navtop-mob');

    buscarBtn.addEventListener('click', function (e) { //say this is an anchor
      e.preventDefault();
      buscarContenedor.classList.toggle('ver');
      headerMenu.classList.remove('ver');
      navTopMob.classList.remove("aberto");
    });

    const buscarClose = document.getElementById('buscar_close');
    buscarClose.addEventListener('click', () => {
      buscarContenedor.classList.toggle('ver');
    });
  }

  init() {
    //    this.headerHandler()
    this.showSubnav()
    this.setCurrentNavItem()
    this.checkAnclasPosition()
    this.checkMenuContextualLinks()
    this.menuBocata()
    this.btnSearchInteraction()
    this.checkHomeLink()

    /*
    if (this.sidemenu && this.navbarToggler) {
      console.log('creating sidemenu anim');
      this.createSideMenuAnimation()
      this.handlerSideMenu()
    }
    */

  }

  recarga() {
    this.checkMenuContextualLinks()
    this.setCurrentNavItem()
    this.checkAnclasPosition()
    //  this.hideSideMenu()
  }
}
