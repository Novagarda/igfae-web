'use strict'

export default class Filter {
  constructor() {
    this.config = {

    }

  }

  handleFilter() {
    console.log('filter init');
    const filterButtons = document.querySelectorAll('button.filter-item')

    filterButtons.forEach((item, i) => {
      item.addEventListener('click',this.filterCat)
    });

  }

  filterCat (e) {
    let currentFilter = this.getAttribute('data-filter')
    let filter = document.querySelector('.navbar-filter')

    if (this.classList.contains('active') || currentFilter == 'all') {
      removeActiveFilter()
      showAllItems()
      filter.classList.remove('filtered')
    } else {
      setActiveFilter(this,currentFilter)
      filterItems(currentFilter)
      filter.classList.add('filtered')
    }

    function setActiveFilter (current,slug) {
      const filterButtons = document.querySelectorAll('button.filter-item')

      filterButtons.forEach((item, i) => {
        if (item != current) {
          item.classList.remove('active')
        } else {
          item.classList.add('active')
        }
      });
    }

    function filterItems(slug) {
      const items = document.querySelectorAll('.grid-item')

      items.forEach((item, i) => {
        if (item.classList.contains(slug)) {
          item.style.opacity = 1
        } else {
          item.style.opacity = .2
        }
      })
    }

    function showAllItems () {
      const items = document.querySelectorAll('.grid-item')
      items.forEach((item, i) => {
        item.style.opacity = 1
      })
    }

    function removeActiveFilter () {
      const filters = document.querySelectorAll('button.filter-item')
      const filter = document.querySelector('.navbar-filter')

      filters.forEach((item, i) => {
        item.classList.remove('active')
      });
      filter.classList.remove('filtered')
    }

  }

  resetFilter () {
    const filters = document.querySelectorAll('button.filter-item')
    const filter = document.querySelector('.navbar-filter')

    filters.forEach((item, i) => {
      item.classList.remove('active')
    });
    filter.classList.remove('filtered')
  }

  init() {
    const filter = document.querySelector('.navbar-filter')

    if (filter) {
      this.handleFilter()
    }

    document.addEventListener('swup:willReplaceContent', (event) => {

      const filter = document.querySelector('.navbar-filter')
      if (filter) {
        this.resetFilter()
        this.handleFilter()
      }



    });
  }
}
