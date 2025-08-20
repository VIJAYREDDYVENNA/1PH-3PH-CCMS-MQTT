(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) 
    {  
      const logo = document.getElementById('istl-logo'); 
      if (logo) { 
        if (storedTheme === 'dark') {
          logo.src = '../assets/logos/istl_dark.png';
        } else if (storedTheme === 'light') {
          logo.src = '../assets/logos/istl_light.png'; 
        }
      }

      return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  const setTheme = theme => {
    if (theme === 'auto') {
      document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));      
      const logo = document.getElementById('istl-logo');
      if (logo) 
      { 
        const storedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        if (storedTheme === 'dark') {
          logo.src = '../assets/logos/istl_dark.png'; // Set the src attribute for dark theme
        } else if (storedTheme === 'light') {
          logo.src = '../assets/logos/istl_light.png'; // Set the src attribute for light theme
        }
      }
      
    } else {

      const logo = document.getElementById('istl-logo');
      if (logo) { 
        if (theme === 'dark') {
          logo.src = '../assets/logos/istl_dark.png'; // Set the src attribute for dark theme
        } else if (theme === 'light') {
          logo.src = '../assets/logos/istl_light.png'; // Set the src attribute for light theme
        }
      }
      document.documentElement.setAttribute('data-bs-theme', theme);
    }
  }

  const showActiveTheme = (theme, focus = false) => {
    const themeSwitcher = document.querySelector('#bd-theme');

    if (!themeSwitcher) {
      return;
    }

    const themeSwitcherText = document.querySelector('#bd-theme-text');
    const activeThemeIcon = document.querySelector('.theme-icon-active use');
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
    const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href');

    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
      element.classList.remove('active');
      element.setAttribute('aria-pressed', 'false');
    });

    btnToActive.classList.add('active');
    btnToActive.setAttribute('aria-pressed', 'true');
    activeThemeIcon.setAttribute('href', svgOfActiveBtn);
    const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`;
    themeSwitcher.setAttribute('aria-label', themeSwitcherLabel);

    if (focus) {
      themeSwitcher.focus();
    }
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme();
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
      setTheme(getPreferredTheme());
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    setTheme(getPreferredTheme());
    showActiveTheme(getPreferredTheme());

    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
      toggle.addEventListener('click', () => {
        const theme = toggle.getAttribute('data-bs-theme-value');
        setStoredTheme(theme);
        setTheme(theme);
        showActiveTheme(theme, true);
      });
    });
  });
})();
