'use strict'

import Swup from 'swup';
import SwupBodyClassPlugin from '@swup/body-class-plugin';
import SwupPreloadPlugin from '@swup/preload-plugin';

export default class PageLoader {
  constructor() {
    this.config = {

    }
  }

  transitions() {
    // Valid links for swup navigation
    const linkInclude = [
      `a[href^="${window.location.origin}"]`,
      `a[href^="/"]`,
      `a[href^="#"]`
    ]
    // Links to skip for swup navigation
    const linkExclude = [
      '[download]',
      '[target="_blank"]',
      '[data-no-swup]',
      `[href$=".pdf"]`,
      '#wpadminbar a',
      '.wpml-ls-link'
    ]

    const containers = [
      '#swup',
      ...(document.querySelector('#wpadminbar') ? ['#wpadminbar'] : []),
      '.header-bottom'
    ];

    const exclude = linkExclude.map(selector => `:not(${selector})`).join('')
    const linkSelector = linkInclude.map(include => `${include}${exclude}`).join(',')

    const options = {
      containers: containers,
      linkSelector: linkSelector,
      plugins: [
        new SwupPreloadPlugin(),
        new SwupBodyClassPlugin({
          prefix: ''
        })]
    };

    const swup = new Swup(options);
    console.log('starting swup');
  }

  init() {
    this.transitions()
  }

}
