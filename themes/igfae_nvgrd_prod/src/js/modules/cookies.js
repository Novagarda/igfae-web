'use strict'

export default class Cookies {
  constructor() {
    this.config = {

    }

  }

  cookieNoticeLanguages () {
    gdprCookieNoticeLocales.es = {
        description: 'Utilizamos cookies para ofrecerte una mejor experiencia de navegación, personalizar contenido y anuncios, proporcionar funciones de redes sociales y analizar nuestro tráfico. Lee sobre cómo utilizamos las cookies y cómo puedes controlarlas haciendo clic en Configuración de cookies. Aceptas nuestras cookies si continúas utilizando este sitio web.',
        settings: 'Configuración de cookies',
        accept: 'Aceptar cookies',
        statement: 'Nuestra declaración de cookies',
        save: 'Guardar configuración',
        always_on: 'Siempre activo',
        cookie_essential_title: 'Cookies esenciales del sitio web',
        cookie_essential_desc: 'Las cookies necesarias ayudan a que un sitio web sea utilizable al habilitar funciones básicas como la navegación por las páginas y el acceso a áreas seguras del sitio web. El sitio web no puede funcionar correctamente sin estas cookies.',
        cookie_performance_title: 'Cookies de rendimiento',
        cookie_performance_desc: 'Estas cookies se utilizan para mejorar el rendimiento y la funcionalidad de nuestros sitios web, pero no son esenciales para su uso. Por ejemplo, almacenan tu idioma preferido o la región en la que te encuentras.',
        cookie_analytics_title: 'Cookies de análisis',
        cookie_analytics_desc: 'Utilizamos cookies de análisis para ayudarnos a medir cómo los usuarios interactúan con el contenido del sitio web, lo cual nos permite personalizar nuestros sitios web y aplicaciones para mejorar tu experiencia.',
        cookie_marketing_title: 'Cookies de marketing',
        cookie_marketing_desc: 'Estas cookies se utilizan para hacer que los mensajes publicitarios sean más relevantes para ti y tus intereses. La intención es mostrar anuncios que sean relevantes y atractivos para el usuario individual y, por lo tanto, más valiosos para los anunciantes y terceros.'
    }

    gdprCookieNoticeLocales.gl = {
        description: 'Utilizamos cookies para ofrecerche unha mellor experiencia de navegación, personalizar contido e anuncios, proporcionar funcións de redes sociais e analizar o noso tráfico. Le sobre como utilizamos as cookies e como podes controlalas facendo clic en Configuración de cookies. Aceptas as nosas cookies se continúas utilizando este sitio web.',
        settings: 'Configuración de cookies',
        accept: 'Aceptar cookies',
        statement: 'A nosa declaración de cookies',
        save: 'Gardar configuración',
        always_on: 'Sempre activo',
        cookie_essential_title: 'Cookies esenciais do sitio web',
        cookie_essential_desc: 'As cookies necesarias axudan a que un sitio web sexa utilizábel ao habilitar funcións básicas como a navegación polas páxinas e o acceso a áreas seguras do sitio web. O sitio web non pode funcionar correctamente sen estas cookies.',
        cookie_performance_title: 'Cookies de rendemento',
        cookie_performance_desc: 'Estas cookies utilizanse para mellorar o rendemento e a funcionalidade dos nosos sitios web, pero non son esenciais para o seu uso. Por exemplo, almacenan o teu idioma preferido ou a rexión na que te atopes.',
        cookie_analytics_title: 'Cookies de análise',
        cookie_analytics_desc: 'Utilizamos cookies de análise para axudarnos a medir como os usuarios interactúan co contido do sitio web, o cal nos permite personalizar os nosos sitios web e aplicacións para mellorar a túa experiencia.',
        cookie_marketing_title: 'Cookies de marketing',
        cookie_marketing_desc: 'Estas cookies utilizanse para facer que as mensaxes publicitarias sexan máis relevantes para ti e os teus intereses. A intención é mostrar anuncios que sexan relevantes e atractivos para o usuario individual e, por iso, máis valiosos para os anunciantes e terceiros.'
      }
      
      gdprCookieNoticeLocales.eu = {
        description: 'Cookieak erabiltzen ditugu nabigazio-esperientzia hobea eskaintzeko, edukiak eta iragarkiak pertsonalizatzeko, sare sozialeko ezaugarriak emateko eta gure trafikoa aztertzeko. Irakurri gure cookieak nola erabiltzen ditugun eta nola kontrolatu ditzakezun jakiteko, sakatu Cookie ezarpenak. Gure cookieak onartzen dituzu webgune hau jarraitzen baduzu.',
        settings: 'Cookie ezarpenak',
        accept: 'Onartu cookieak',
        statement: 'Gure cookie-aurkezpena',
        save: 'Ezarpenak gorde',
        always_on: 'Betik aktibo',
        cookie_essential_title: 'Webgune osoaren cookie esanguratsuak',
        cookie_essential_desc: 'Garrantzitsua diren cookieek webgune bat erabiltzeko gaitasuna ematen dute, adibidez, orrialde nabigazioa eta webgunearen segurtasun eremuetara sartzeko gaitasuna. Webguneak behar bezala ezin du funtzionatu cookie hauen gabe.',
        cookie_performance_title: 'Eraginkortasuneko cookieak',
        cookie_performance_desc: 'Cookie hauek webguneen eraginkortasuna eta funtzionalitatea hobetzeko erabiltzen dira, baina ez dira beharrezkoak. Adibidez, gorde egiten da zeure hizkuntza gogokoena edo zein eskualdean zaude.',
        cookie_analytics_title: 'Analisi cookieak',
        cookie_analytics_desc: 'Erabiltzaileek webgunearen edukiarekin nola interaktuan ikusiz laguntzen dizkigun analisi cookieak erabiltzen ditugu, hau gure webguneak eta aplikazioak zure esperientzia hobetzeko pertsonalizatzeko.',
        cookie_marketing_title: 'Marketing cookieak',
        cookie_marketing_desc: 'Cookie hauek mezu publizitarioak zure interesekin erlazionatuta eta interesgarriagoak bihurtzeko erabiltzen dira. Asmoa da erabiltzaile bakoitzarentzat interesgarri eta erakargarri diren publizitate-anunzioak erakustea, eta horrek argiago baloratzen ditu argitaratzaileentzako eta hirugarrenen publizitateentzako.'
      }
    
  }

  initCookieNotice () {
    if (typeof gdprCookieNotice !== 'undefined') {

        // Cargamos los idiomas
        this.cookieNoticeLanguages()

        gdprCookieNotice({
            locale: document.documentElement.lang, //This is the default value
            timeout: 2000, //Time until the cookie bar appears
            expiration: 30, //This is the default value, in days
            domain: '.tobiaslipp.com', //If you run the same cookie notice on all subdomains, define the main domain starting with a .
            implicit: false, //Accept cookies on scroll
            statement: '/legal-advice', //Link to your cookie statement page
            performance: ['JSESSIONID'], //Cookies in the performance category.
            analytics: ['ga'], //Cookies in the analytics category.
            marketing: ['SSID'] //Cookies in the marketing category.
          });
    }

  }

  init() {
    this.initCookieNotice()
  }


}
