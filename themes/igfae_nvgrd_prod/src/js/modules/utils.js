'use strict'

export default class Utils {

  reloadOnResize () {
    window.onresize = function(){ location.reload(); }
  }

  removeClassArray (elem,selector) {
    const items = document.querySelectorAll(elem)
    items.forEach((item, i) => {
      console.log (item)
      item.classList.remove(selector)
    })
  }

  getClosest (elem,selector) {
    // Element.matches() polyfill
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.matchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector ||
            Element.prototype.oMatchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            function(s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                    i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {}
                return i > -1;
            };
    }

    // Get the closest matching element
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
      if ( elem.matches( selector ) ) return elem;
    }
    return null;
  }

  isInViewport (elem) {
    var bounding = elem.getBoundingClientRect();
    return (
        bounding.top >= 0 &&
        bounding.left >= 0 &&
        bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  };

  isElementInViewport (el) {

      // Special bonus for those using jQuery
      if (typeof jQuery === "function" && el instanceof jQuery) {
          el = el[0];
      }

      var rect = el.getBoundingClientRect();

      return (
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /* or $(window).height() */
          rect.right <= (window.innerWidth || document.documentElement.clientWidth) /* or $(window).width() */
      );
  }

  hasTouchScreen () {
    var hasTouchScreen = false;
if ("maxTouchPoints" in navigator) {
    hasTouchScreen = navigator.maxTouchPoints > 0;
} else if ("msMaxTouchPoints" in navigator) {
    hasTouchScreen = navigator.msMaxTouchPoints > 0;
} else {
    var mQ = window.matchMedia && matchMedia("(pointer:coarse)");
    if (mQ && mQ.media === "(pointer:coarse)") {
        hasTouchScreen = !!mQ.matches;
    } else if ('orientation' in window) {
        hasTouchScreen = true; // deprecated, but good fallback
    } else {
        // Only as a last resort, fall back to user agent sniffing
        var UA = navigator.userAgent;
        hasTouchScreen = (
            /\b(BlackBerry|webOS|iPhone|IEMobile)\b/i.test(UA) ||
            /\b(Android|Windows Phone|iPad|iPod)\b/i.test(UA)
        );
    }
}
  return hasTouchScreen
  }

  throttle (callback, wait, context = this) {
    let timeout = null
    let callbackArgs = null

    const later = () => {
      callback.apply(context, callbackArgs)
      timeout = null
    }

    return function () {
      if (!timeout) {
        callbackArgs = arguments
        timeout = setTimeout(later, wait)
      }
    }
  }

  debounce (callback, wait, context = this) {
    let timeout = null
    let callbackArgs = null

    const later = () => callback.apply(context, callbackArgs)

    return function () {
      callbackArgs = arguments
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  }

  randomNum (min,max) {
    return Math.floor(Math.random()*(max-min+1)+min);
  }

  getScrollDir (downCb, upCb) {
    let lastScrollTop = 0

    const getScrollDir = () => {
      let st = window.pageYOffset || document.documentElement.scrollTop
      if (st > lastScrollTop) {
        if (typeof downCb === 'function') {
          downCb()
        }
      } else {
        if (typeof upCb === 'function') {
          upCb()
        }
      }
      lastScrollTop = st
    }

    const throttledFn = this.throttle(() => {
      getScrollDir()
    }, 10)

    window.addEventListener('scroll', throttledFn)
  }

  customWindowEvent (name, data) {
    let event
    (function () {
      if (typeof window.CustomEvent === 'function') return false
      function CustomEvent (event, params) {
        params = params || { bubbles: false, cancelable: false, detail: undefined }
        var evt = document.createEvent('CustomEvent')
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail)
        return evt
      }
      CustomEvent.prototype = window.Event.prototype
      window.CustomEvent = CustomEvent
    })()
    if (window.CustomEvent) {
      let detail
      if (data) {
        detail = {detail: data}
      }
      event = new CustomEvent(name, detail)
    } else {
      event = document.createEvent('CustomEvent')
      event.initCustomEvent(name, true, true, data)
    }
    window.dispatchEvent(event)
  }

  // https://gist.github.com/kflorence/3086552
  scrollbarWidth () {
    const inner = document.createElement('p')
    inner.style.width = '100%'
    inner.style.height = '200px'
    const outer = document.createElement('div')
    outer.style.position = 'absolute'
    outer.style.top = '0px'
    outer.style.left = '0px'
    outer.style.visibility = 'hidden'
    outer.style.width = '200px'
    outer.style.height = '150px'
    outer.style.overflow = 'hidden'
    outer.appendChild(inner)
    document.body.appendChild(outer)
    const w1 = inner.offsetWidth
    outer.style.overflow = 'scroll'
    let w2 = inner.offsetWidth
    if (w1 === w2) {
      w2 = outer.clientWidth
    }
    document.body.removeChild(outer)
    return (w1 - w2)
  }
}
