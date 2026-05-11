/*
  * Author: sacxe
  * Email:  sacxe@proton.me
*/


(function () {
    const SETTINGS_KEY = "accessibilitySettings";
    const prefix = "sac-accessibility-";

    function getCookieDomain() {
        const hostname = location.hostname;
        if (hostname === 'localhost' || /^\d+\.\d+\.\d+\.\d+$/.test(hostname)) {
            return '';
        }
        const parts = hostname.split('.');
        if (parts.length > 1) {
            return '.' + parts.slice(1).join('.');
        }
    }

    const accessBtn = document.getElementById('accessButton');
    accessBtn.className = 'sa-widget-custom-trigger no-button';
    accessBtn.setAttribute('aria-label', 'Accessibility Options');
    accessBtn.setAttribute('aria-haspopup', 'dialog');
    accessBtn.setAttribute('aria-controls', 'sa-main');
    accessBtn.setAttribute('type', 'button');
    accessBtn.setAttribute('tabindex', '0');
    accessBtn.innerHTML = `<svg width="22" height="23" viewBox="0 0 22 23" fill="currentColor">
                                    <path d="M21.7598 8.37793H14.5859V22.9131H12.1943V16.2041H9.80371V22.9131H7.41211V8.37793H0.238281V6.1416H21.7598V8.37793ZM10.999 0.550781C12.3142 0.55082 13.3906 1.55719 13.3906 2.78711C13.3906 4.01699 12.3142 5.0234 10.999 5.02344C9.68385 5.02344 8.60746 4.01702 8.60742 2.78711C8.60742 1.55716 9.68382 0.550781 10.999 0.550781Z"></path>
                            </svg>`;


    const widgetHTML = `
            <link rel="stylesheet" href="http://localhost/bee/assets/lib/accessibility/style.css" />
            <div class="accessibility-loading">
                
            <!--<button tabindex="1"  aria-label="Accessibility Options" data-sa-trigger="true" aria-haspopup="dialog" aria-controls="sa-main" aria-expanded="false" id="sa-widget-custom-trigger" class="sa-widget-custom-trigger no-buttom" style1="width: 0; height: 0; position: fixed; left: -9999px;">
                    <label class="short-key">Ctrl+F2</label>    
                    <img alt="icon" loading="lazy" src="data:image/svg+xml,%0A%3Csvg width='32' height='32' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23clip0_1_1506)' fill='%23fff'%3E%3Cpath d='M16 7a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7z'/%3E%3Cpath d='M27 7.05l-.028.008-.026.008a7.112 7.112 0 0 0-.188.055c-1.163.341-6.808 1.933-10.785 1.933-3.696 0-8.83-1.375-10.472-1.842A4.61 4.61 0 0 0 5 7.05c-1.188-.313-2 .893-2 1.996 0 1.092.98 1.612 1.972 1.985v.017l5.95 1.86c.609.232.771.47.85.677.259.662.053 1.972-.02 2.43l-.363 2.812L9.378 29.84l-.017.092-.014.08c-.145 1.009.596 1.988 2 1.988 1.225 0 1.766-.846 2-1.996.234-1.15 1.75-9.848 2.625-9.848s2.677 9.848 2.677 9.848c.235 1.15.775 1.996 2 1.996 1.408 0 2.15-.984 2-1.996a3.591 3.591 0 0 0-.047-.254l-2.04-10.92-.361-2.813c-.262-1.638-.052-2.18.02-2.306a.07.07 0 0 0 .005-.01c.067-.125.375-.405 1.092-.674l5.58-1.95c.034-.01.068-.02.101-.033 1-.375 2-.894 2-1.996 0-1.102-.811-2.31-1.999-1.998z'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_1_1506'%3E%3Cpath fill='%23fff' d='M0 0h32v32H0z'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E" />
                    <span></span> 
                </button>-->

                <div class="sacxe sa-light-theme gradient-head sacxe-initial paid_widget" id="sa-main" role="dialog" aria-modal="true" aria-labelledby="sa-heading">
                    <div class="relative second-panel">
                        <h2 id="sa-heading">Accessibility options <span class="inner-short-key">Ctrl+F2</span></h2>
                        <button type="button" aria-label="Close main navigation panel" class="sacxe-close" tabindex="1"></button>
                    </div>
                    <div class="sacxe-body">
                        <div class="h-scroll mb-3">
                            <div class="sacxe-features">
                                <div class="sacxe-features__item reset-feature" id="featureItem">
                                    <button aria-label="Bigger Text" tabindex="2" id="btn-s9" class="sacxe-features__item__i">
                                        <span class="sacxe-features__item__icon">
                                            <span class="sac-icon icon-bigger-text" role="img" aria-label="Bigger text icon" aria-hidden="true" aria-pressed="false"></span>
                                        </span>
                                        <span class="sacxe-features__item__name">Bigger Text</span>
                                        <div class="sacxe-features__item__steps reset-steps" id="featureSteps">
                                <span class="step sacxe-features__step"></span>
                                <span class="step sacxe-features__step"></span>
                                <span class="step sacxe-features__step"></span>
                                <span class="step sacxe-features__step"></span>
                            </div>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon"  aria-live="polite" role="status"></span>
                        </button>
                    </div>
                    <div class="sacxe-features__item reset-feature" id="featureItem-st">
                        <button aria-label="Smaller Text" tabindex="3" id="btn-s17" class="sacxe-features__item__i" disabled>
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-smaller-text" role="img" aria-label="Smaller text icon" aria-hidden="true" aria-pressed="false"></span>
                            </span>
                            <span class="sacxe-features__item__name">Smaller Text</span>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIconSt"  aria-live="polite" role="status"></span>
                        </button>
                    </div>



                    <div class="sacxe-features__item reset-feature" id="featureItem-df">
                        <button aria-label="Dyslexia Friendly Font" aria-pressed="false" tabindex="4" id="btn-df" class="sacxe-features__item__i">
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-dyslexia-font" role="img" aria-label="Dyslexia friendly font icon" aria-hidden="true"></span>
                            </span>
                            <span class="sacxe-features__item__name">Dyslexia Friendly</span>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-df"  aria-live="polite" role="status"></span>
                        </button>
                    </div>
                    <div class="sacxe-features__item reset-feature" id="featureItem-adhd">                    
                        <button aria-label="ADHD Mode" aria-pressed="false" tabindex="5" id="btn-adhd" class="sacxe-features__item__i">
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-adhd-friendly" role="img" aria-label="Icon ADHD Friendly icon" aria-hidden="true"></span>
                            </span>
                            <span class="sacxe-features__item__name">ADHD Mode</span>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-adhd" aria-live="polite" role="status"></span>
                        </button>
                    </div>
                    <div class="sacxe-features__item reset-feature" id="featureItem-saturate">
                        <button aria-label="Saturate Colors" aria-pressed="false" tabindex="6" id="btn-saturate" class="sacxe-features__item__i">
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-saturate" role="img" aria-label="Saturate icon" aria-hidden="true" id="saturate-text-0"></span>
                                <span class="sac-icon icon-lowsaturate" role="img" aria-label="Low Saturate icon" aria-hidden="true" id="saturate-text-1" style="display: none;"></span>
                                <span class="sac-icon icon-highsaturate" role="img" aria-label="High Saturate colors icon" aria-hidden="true" id="saturate-text-2" style="display: none;"></span>
                                <span class="sac-icon icon-desaturate" role="img" aria-label="Desaturate icon" aria-hidden="true" id="saturate-text-3" style="display: none;"></span>
                            </span>
                            <div id="feature-container">
                                <span class="sacxe-features__item__name saturate-text" id="saturate-detail-text-0">Saturation</span>
                                <span class="sacxe-features__item__name saturate-text" id="saturate-detail-text-1" style="display: none;">Low Saturation</span>
                                <span class="sacxe-features__item__name saturate-text" id="saturate-detail-text-2" style="display: none;">High Saturation</span>
                                <span class="sacxe-features__item__name saturate-text" id="saturate-detail-text-3" style="display: none;">Desaturate</span>
                            </div>
                            <div class="sacxe-features__item__steps reset-steps" id="featureStepsSaturate">
                                <span class="step sacxe-features__step"></span>
                                <span class="step sacxe-features__step"></span>
                                <span class="step sacxe-features__step"></span>
                            </div>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-saturate"  aria-live="polite" role="status"></span>
                        </button>
                    </div>
                    <div class="sacxe-features__item reset-feature" id="featureItem-ht-dark">
	                    <button aria-label="Light Dark Theme" aria-pressed="false" tabindex="7" id="dark-btn" class="sacxe-features__item__i">
		                    <div class="sacxe-features__item__name">
			                    <div class="light_dark_icon">
				                    <input type="checkbox" class="light_mode sacxe-featugres__item__i" id="checkbox"  aria-label="Toggle light and dark mode" role="switch"/>
				                    <label for="checkbox" class="checkbox-label">
                                        <span class="visually-hidden">Toggle light and dark mode</span>
					                    <i class="fas fa-moon-stars">
						                    <span class="sac-icon icon-moon" role="img" aria-label="Dark mode icon" aria-hidden="true"></span>
					                    </i>
					                    <i class="fas fa-sun">
						                    <span class="sac-icon icon-sun" role="img" aria-label="Light mode icon" aria-hidden="true"></span>
					                    </i>
					                    <span class="ball"></span>
				                    </label>
			                    </div>
			                    <span class="sacxe-features__item__name">Light-Dark</span>
		                    </div>
	                        <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-ht-dark"  aria-live="polite" role="status"></span>
	                    </button>
                    </div>
                    <div class="sacxe-features__item reset-feature" id="featureItem-ic">
                        <button aria-label="Invert Colors" aria-pressed="false" tabindex="8" id="btn-invert" class="sacxe-features__item__i">
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-invert" role="img" aria-label="Invert colors icon" aria-hidden="true"></span>
                            </span>
                            <span class="sacxe-features__item__name">Invert Colors</span>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-ic"  aria-live="polite" role="status"></span>
                        </button>
                    </div>
                    
                
                    <div class="sacxe-features__item reset-feature" id="featureItem-Cursor">
                        <button aria-label="Cursor Bigger" aria-pressed="false" tabindex="9" id="btn-cursor" class="sacxe-features__item__i">
                            <span class="sacxe-features__item__icon">
                                <span class="sac-icon icon-cursor" role="img" aria-label="Cursor bigger icon" aria-hidden="true"></span>
                            </span>
                            <span class="sacxe-features__item__name">Cursor</span>
                            <span class="tick-active sacxe-features__item__enabled reset-tick" id="tickIcon-cursor"  aria-live="polite" role="status"></span>
                        </button>
                    </div>        
                </div>
            </div>
        </div>
        <div class="reset-panel">
            <div class="copyrights-accessibility">
                <button aria-label="Reset All Settings" tabindex="10" class="btn-reset-all" id="reset-all">
                    <div class="reset-icon"></div>
                    <div class="reset-btn-text">Reset All Settings</div>
                </button>
            </div>
        </div>
    </div>

    <div id="accessibility-overlay">
        <button  id="open-the-accessibility-menu" class="skip-link" >
            <span class="icon-open-accessibile">
                <img alt="icon" loading="lazy" src="data:image/svg+xml,%0A%3Csvg width='32' height='32' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23clip0_1_1506)' fill='%23fff'%3E%3Cpath d='M16 7a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7z'/%3E%3Cpath d='M27 7.05l-.028.008-.026.008a7.112 7.112 0 0 0-.188.055c-1.163.341-6.808 1.933-10.785 1.933-3.696 0-8.83-1.375-10.472-1.842A4.61 4.61 0 0 0 5 7.05c-1.188-.313-2 .893-2 1.996 0 1.092.98 1.612 1.972 1.985v.017l5.95 1.86c.609.232.771.47.85.677.259.662.053 1.972-.02 2.43l-.363 2.812L9.378 29.84l-.017.092-.014.08c-.145 1.009.596 1.988 2 1.988 1.225 0 1.766-.846 2-1.996.234-1.15 1.75-9.848 2.625-9.848s2.677 9.848 2.677 9.848c.235 1.15.775 1.996 2 1.996 1.408 0 2.15-.984 2-1.996a3.591 3.591 0 0 0-.047-.254l-2.04-10.92-.361-2.813c-.262-1.638-.052-2.18.02-2.306a.07.07 0 0 0 .005-.01c.067-.125.375-.405 1.092-.674l5.58-1.95c.034-.01.068-.02.101-.033 1-.375 2-.894 2-1.996 0-1.102-.811-2.31-1.999-1.998z'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_1_1506'%3E%3Cpath fill='%23fff' d='M0 0h32v32H0z'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E">
            </span>
            <strong>Open the accessibility option</strong>
            <span class="icon-enter"></span>
        </button>
    </div>
    </div>`;
    /*
    const container = document.getElementById('accessButton');
    if (!container) {
        throw new Error('#accessButton not found');
    }
    */
    document.body.insertAdjacentHTML('beforeend', widgetHTML);
    document.addEventListener("DOMContentLoaded", loadSettings);
    document.addEventListener("scroll", function () {
        detectRouteChange()
    })
    accessBtn.addEventListener('click', () => {
          document.getElementById('sa-main').style.right = '0';
    });

    document.getElementById('open-the-accessibility-menu').addEventListener('click', function () {
        const menu = document.getElementById('sa-main');
        const btn = document.getElementById('open-the-accessibility-menu');
        if (menu) {
            menu.style.right = '0';
        }
        if (btn) {
            btn.style.display = 'none';
        }
    });

    function closeMain() {
        document.getElementById('sa-main').style.right = '-530px'
    }
    document.addEventListener('DOMContentLoaded', function () {
        const closeButtons = document.querySelectorAll('.sacxe-close');
        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeMain)
        })
    });
    
    let fontSizeCount = 0;
    let saturateCount = 0;
    let lastPath = window.location.pathname;
    const fontSizeSpans = document.querySelectorAll('#featureSteps span');
    const saturateSpans = document.querySelectorAll('#featureStepsSaturate span');
    let tabPressCount = 0;
    let adhdActive = false;

    const readingMask = document.createElement('div');
    readingMask.className = 'reading-mask-horizontal';

    document.body.appendChild(readingMask);

    let cursorPositionY = 0;

    document.addEventListener('mousemove', (e) => {
        cursorPositionY = e.clientY;
        updateHighlightBarPosition();
        readingMask.style.setProperty('--y', `${e.clientY}px`);
    });

    window.addEventListener('wheel', () => {
        updateHighlightBarPosition();
    });

    function updateHighlightBarPosition() {
        if (adhdActive) {
            readingMask.style.top = '0';
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const accessibilityBtn = document.getElementById("open-the-accessibility-menu");
        const accessibilityMenu = document.getElementById("sa-main");
        const overlay = document.getElementById("accessibility-overlay");
        const closeBtn = document.getElementById("close-sa-main");

        if (!accessibilityBtn || !accessibilityMenu || !overlay) {
            // console.warn("Missing required elements.");
            return;
        }

        let hasSpoken = false;
        let menuOpened = false;
        let lastFocusedElement = null;
        let tabPressCount = 0;
        let menuClosedByEscape = false;

        document.addEventListener("keydown", function (e) {
            if (e.key !== "Tab" || menuOpened) return;
            if (menuClosedByEscape) return;
            tabPressCount++;
            if (tabPressCount === 1) {
                e.preventDefault();
                overlay.style.display = "flex";
                accessibilityBtn.style.visibility = "visible";
                accessibilityBtn.focus();
            } else if (tabPressCount === 2) {
                e.preventDefault();
                overlay.style.display = "none";
                accessibilityBtn.style.display = "none";
            }
        });

        function openMenu() {
            lastFocusedElement = document.activeElement;
            accessibilityMenu.style.right = "0";
            overlay.style.visibility = "hidden";
            accessibilityBtn.style.visibility = "hidden";
            menuOpened = true;
            menuClosedByEscape = false;
            const focusableElements = accessibilityMenu.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstFocusable = focusableElements.length > 0 ? focusableElements[0] : accessibilityMenu;
            if (firstFocusable === accessibilityMenu) {
                accessibilityMenu.setAttribute("tabindex", "-1");
            }
            firstFocusable.focus();
            tabPressCount = 0;
        }

        function closeMenu() {
            accessibilityMenu.style.right = "-530px";
            overlay.style.display = "none";
            accessibilityBtn.style.display = "none";
            tabPressCount = 0;
            menuOpened = false;
            menuClosedByEscape = true;
            if (lastFocusedElement) {
                lastFocusedElement.focus();
            }
        }
        accessibilityBtn.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                openMenu();
            }
        });
        accessibilityBtn.addEventListener("click", openMenu);
        if (closeBtn) {
            closeBtn.addEventListener("click", closeMenu);
        }
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                closeMenu();
            }
        });
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) {
                closeMenu();
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.ctrlKey && e.key === "F2") {
                e.preventDefault();
                if (menuOpened) {
                    closeMenu();
                } else {
                    openMenu();
                }
            }
        });
    });

    function applyTextSettings(direction = '+') {
        let elements = document.querySelectorAll('body > *:not(.sacxe)');
        elements.forEach(el => {
            let currentZoom = parseFloat(el.style.zoom) || 1;
            let newZoom;
            if (direction === '+') {
                newZoom = (currentZoom + 0.1).toFixed(2);
            } else {
                newZoom = Math.max(0.1, currentZoom - 0.1).toFixed(2); 
            }
            el.setAttribute('style', `zoom: ${newZoom} !important;`);
        });
    }

    function adjustFontSize(data) {
        if (data === '+1') {
            fontSizeCount = (fontSizeCount + 1) % 5;
            applyTextSettings('+');
        } else {
            fontSizeCount = (fontSizeCount - 1 + 5) % 5; 
            if (fontSizeCount > 0) {
                applyTextSettings('-');
            }
        }
        const button = document.getElementById('featureItem');
        const tickIcon = document.getElementById('tickIcon');
        const fontCheck = document.getElementById('featureSteps');
        const smallerTextBtn = document.getElementById('btn-s17');
        button.classList.add(prefix + 'feature-active');
        saveSettings();
        if (fontSizeCount > 0) {
            smallerTextBtn.disabled = false;
        } else {
            smallerTextBtn.disabled = true;
        }
        if (fontSizeCount === 0) {
            button.classList.toggle(prefix + 'feature-active');
            tickIcon.style.display = 'none';
            fontCheck.classList.remove(prefix + 'featureSteps-visible');
            let elements = document.querySelectorAll('body > *:not(.sacxe)');
            elements.forEach(el => el.style.zoom = 1);
            fontSizeSpans.forEach(span => span.classList.remove(prefix + 'active'))
        } else {
            tickIcon.style.display = 'inline-flex';
            fontCheck.classList.add(prefix + 'featureSteps-visible');
            fontSizeSpans.forEach(span => span.classList.remove(prefix + 'active'));
            fontSizeSpans.forEach((span, index) => {
                if (index <= fontSizeCount - 1) {
                    span.classList.add(prefix + 'active')
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const btnIncrease = document.getElementById('btn-s9');
        const btnDecrease = document.getElementById('btn-s17');
        if (btnIncrease) {
            btnIncrease.addEventListener('click', () => adjustFontSize('+1'));
        }
        if (btnDecrease) {
            btnDecrease.addEventListener('click', () => adjustFontSize('-1'));
        }
    });

    function toggleDyslexiaMode() {
        const button = document.getElementById('featureItem-df');
        const tickIcon = document.getElementById('tickIcon-df');
        button.classList.toggle(prefix + 'feature-active');
        tickIcon.style.display = tickIcon.style.display === 'inline-flex' ? 'none' : 'inline-flex';
        document.body.classList.toggle(prefix + 'dyslexia-mode');
        saveSettings()
    }
    document.addEventListener('DOMContentLoaded', function () {
        const dyslexiaBtn = document.getElementById('btn-df');
        if (dyslexiaBtn) {
            dyslexiaBtn.addEventListener('click', toggleDyslexiaMode)
        }
    });

    function changeCursor() {
        const button = document.getElementById('featureItem-Cursor');
        const tickIcon = document.getElementById('tickIcon-cursor');
        button.classList.toggle(prefix + 'feature-active');
        tickIcon.style.display = tickIcon.style.display === 'inline-flex' ? 'none' : 'inline-flex';
        document.body.classList.toggle(prefix + 'custom-cursor');
        saveSettings()
    }
    document.addEventListener('DOMContentLoaded', function () {
        const cursorBtn = document.getElementById('btn-cursor');
        if (cursorBtn) {
            cursorBtn.addEventListener('click', changeCursor)
        }
    });

    function toggleDarkMode() {
        const button = document.getElementById('featureItem-ht-dark');
        const tickIcon = document.getElementById('tickIcon-ht-dark');
        button.classList.toggle(prefix + 'feature-active');
        tickIcon.style.display = tickIcon.style.display === 'inline-flex' ? 'none' : 'inline-flex';
        document.body.classList.toggle(prefix + "dark-mode");
        saveSettings()
    }
    document.addEventListener('DOMContentLoaded', function () {
        const darkModeBtn = document.getElementById('dark-btn');
        if (darkModeBtn) {
            darkModeBtn.addEventListener('click', toggleDarkMode)
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const uwMain = document.getElementById('sa-main');
            if (uwMain) {
                uwMain.style.right = '-530px';
            }
        }
    });

    function invertColor() {
        const button = document.getElementById('featureItem-ic');
        const tickIcon = document.getElementById('tickIcon-ic');
        button.classList.toggle(prefix + 'feature-active');
        tickIcon.style.display = tickIcon.style.display === 'inline-flex' ? 'none' : 'inline-flex';
        document.documentElement.classList.toggle(prefix + "invert-colors");
        saveSettings()
    }
    document.addEventListener('DOMContentLoaded', function () {
        const invertBtn = document.getElementById('btn-invert');
        if (invertBtn) {
            invertBtn.addEventListener('click', invertColor)
        }
    });

    function saturateColor() {
        const button = document.getElementById('featureItem-saturate');
        const tickIcon = document.getElementById('tickIcon-saturate');
        const saturateCheck = document.getElementById('featureStepsSaturate');
        document.documentElement.classList.remove(prefix + 'saturate-low', prefix + 'saturate-high', prefix + 'saturate-desaturate');
        saturateCount = (saturateCount + 1) % 4;
        for (let i = 0; i <= 3; i++) {
            const textElement = document.getElementById(`saturate-text-${i}`);
            const textElement1 = document.getElementById(`saturate-detail-text-${i}`);
            if (textElement) {
                textElement.style.display = 'none';
            }
            if (textElement1) {
                textElement1.style.display = 'none';
            }
        }
        const currentTextElement = document.getElementById(`saturate-text-${saturateCount}`);
        if (currentTextElement) {
            currentTextElement.style.display = 'inline';
        }
        const currentTextElement1 = document.getElementById(`saturate-detail-text-${saturateCount}`);
        if (currentTextElement1) {
            currentTextElement1.style.display = 'inline';
        }
        if (saturateCount === 0) {
            button.classList.remove(prefix + 'feature-active');
            tickIcon.style.display = 'none';
            saturateCheck.classList.remove(prefix + 'featureSteps-visible');
            saturateSpans.forEach(span => span.classList.remove(prefix + 'active'));
        } else {
            button.classList.add(prefix + 'feature-active');
            tickIcon.style.display = 'inline-flex';
            saturateCheck.classList.add(prefix + 'featureSteps-visible');
            if (saturateCount === 1) {
                document.documentElement.classList.add(prefix + 'saturate-low');
            } else if (saturateCount === 2) {
                document.documentElement.classList.add(prefix + 'saturate-high');
            } else if (saturateCount === 3) {
                document.documentElement.classList.add(prefix + 'saturate-desaturate');
            }
            saturateSpans.forEach(span => span.classList.remove(prefix + 'active'));
            saturateSpans.forEach((span, index) => {
                if (index <= saturateCount - 1) {
                    span.classList.add(prefix + 'active');
                }
            });
        }
        saveSettings();
    }
    document.addEventListener('DOMContentLoaded', function () {
        const saturateBtn = document.getElementById('btn-saturate');
        if (saturateBtn) {
            saturateBtn.addEventListener('click', saturateColor)
        }
    });

    function toggleADHDFriendlyMode() {
        const button = document.getElementById('featureItem-adhd');
        const tickIcon = document.getElementById('tickIcon-adhd');
        if (adhdActive) {
            button.classList.remove(prefix + 'feature-active');
            tickIcon.style.display = 'none';
            readingMask.classList.add('mask-hidden');
            readingMask.classList.remove('mask-visible');
            document.body.classList.remove(prefix + 'adhd-saturate');
            adhdActive = false;
        } else {
            button.classList.add(prefix + 'feature-active');
            tickIcon.style.display = 'inline-flex';
            readingMask.classList.remove('mask-hidden');
            readingMask.classList.add('mask-visible');
            document.body.classList.add(prefix + 'adhd-saturate');
            adhdActive = true;
        }
        saveSettings();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const adhdBtn = document.getElementById('btn-adhd');
        if (adhdBtn) {
            adhdBtn.addEventListener('click', toggleADHDFriendlyMode)
        }
    });

    function resetSettings() {
        const cookieDomain = getCookieDomain();
        if (cookieDomain) {
            document.cookie = `${SETTINGS_KEY}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${cookieDomain}`;
        } else {
            document.cookie = `${SETTINGS_KEY}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
        }
        const allTextNodes = document.body.querySelectorAll('p:not(.sacxe *), h1:not(.sacxe *), h2:not(.sacxe *), h3:not(.sacxe *), h4:not(.sacxe *), h5:not(.sacxe *), h6:not(.sacxe *), span:not(.sacxe *), li:not(.sacxe *), a:not(.sacxe *)');
        allTextNodes.forEach(node => {
            node.innerHTML = node.innerHTML.replace(/<span style="font-weight: bold !important;?[^"']*["']>(.*?)<\/span>/g, '$1')
        });

        document.querySelectorAll('body *:not(.sacxe *):not(.sacxe)').forEach((el) => {
            el.style.fontSize = "";
            el.style.cursor = "";
            el.style.zoom = "1";
            el.classList.remove(prefix + "active");
        });

        adhdActive = false;
        document.body.classList.remove(prefix + "dark-mode", prefix + "custom-cursor", prefix + "dyslexia-mode", prefix + 'saturate-low', prefix + 'saturate-high', prefix + 'saturate-desaturate', prefix + 'adhd-saturate');
        document.documentElement.classList.remove(prefix + "invert-colors");
        fontSizeCount = 0;
        saturateCount = 0;
        saveSettings();
        loadSettings();
        const checkboxes = document.querySelectorAll('.sacxe input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = !1);
        document.querySelectorAll('.font-size-visible, .line-height-visible').forEach(el => el.classList.remove(prefix + 'span-visible'))
        readingMask.classList.add('mask-hidden');
        readingMask.classList.remove('mask-visible');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const resetBtn = document.getElementById('reset-all');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetSettings)
        }
    });

    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(";").shift());
    }

    function saveSettings() {
        const settings = {
            fontSizeCount: fontSizeCount - 1,
            saturateCount: saturateCount,
            dyslexiaMode: document.body.classList.contains(prefix + 'dyslexia-mode'),
            darkMode: document.body.classList.contains(prefix + 'dark-mode'),
            cursorChanged: document.body.classList.contains(prefix + 'custom-cursor'),
            invert: document.documentElement.classList.contains(prefix + 'invert-colors'),
            adhdFriendly: document.body.classList.contains(prefix + 'adhd-saturate'),
        };

        const jsonStr = JSON.stringify(settings);
        const expiryDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toUTCString(); // 30 days
        const cookieDomain = getCookieDomain();
        if (location.protocol === 'https:') {
            if (cookieDomain) {
                // For production domains with HTTPS
                document.cookie = `accessibilitySettings=${encodeURIComponent(jsonStr)}; expires=${expiryDate}; path=/; domain=${cookieDomain}; SameSite=Strict; Secure`;
            } else {
                // For localhost/IP with HTTPS
                document.cookie = `accessibilitySettings=${encodeURIComponent(jsonStr)}; expires=${expiryDate}; path=/; SameSite=Strict; Secure`;
            }
        } else {
            if (cookieDomain) {
                document.cookie = `accessibilitySettings=${encodeURIComponent(jsonStr)}; expires=${expiryDate}; path=/; domain=${cookieDomain}`;
            } else {
                document.cookie = `accessibilitySettings=${encodeURIComponent(jsonStr)}; expires=${expiryDate}; path=/`;
            }
            // console.warn(Secure cookie attribute skipped (not HTTPS)");
        }
        // console.log("Cookie saved for domain:", cookieDomain || 'current domain');
    }

    function updateWidgetToggles(settings) {
        const darkModeToggle = document.getElementById('tickIcon-ht-dark');
        if (darkModeToggle) {
            darkModeToggle.style.display = settings.darkMode ? 'inline-flex' : 'none'
        }
        const invertToggle = document.getElementById('tickIcon-ic');
        if (invertToggle) {
            invertToggle.style.display = settings.invert ? 'inline-flex' : 'none'
        }
        const dyslexiaToggle = document.getElementById('tickIcon-df');
        if (dyslexiaToggle) {
            dyslexiaToggle.style.display = settings.dyslexiaMode ? 'inline-flex' : 'none'
        }
        const adhdToggle = document.getElementById('tickIcon-adhd');
        if (adhdToggle) {
            adhdToggle.style.display = settings.adhdFriendly ? 'inline-flex' : 'none'
        }
        const cursorToggle = document.getElementById('tickIcon-cursor');
        if (cursorToggle) {
            cursorToggle.style.display = settings.cursorChanged ? 'inline-flex' : 'none'
        }
        const saturateToggle = document.getElementById('tickIcon-saturate');
        if (saturateToggle) {
            saturateToggle.style.display = settings.saturateCount > 0 ? 'inline-flex' : 'none'
        }
        adjustFontSize('+1');
    }

    function loadSettings() {
        let settings = getCookie(SETTINGS_KEY);
        if (!settings) {
            settings = getCookie(SETTINGS_KEY);
        }
        // console.log(settings);
        if (settings) {
            settings = JSON.parse(settings);
            fontSizeCount = settings.fontSizeCount || 0;
            saturateCount = settings.saturateCount || 0;
    
            if (settings.dyslexiaMode) {
                document.body.classList.add(prefix + 'dyslexia-mode');
                const button = document.getElementById('featureItem-df');
                button.classList.add(prefix + 'feature-active')
            } else {
                const button = document.getElementById('featureItem-df');
                button.classList.remove(prefix + 'feature-active')
            }
            if (settings.darkMode) {
                document.body.classList.add(prefix + 'dark-mode');
                const button = document.getElementById('featureItem-ht-dark');
                button.classList.add(prefix + 'feature-active')
            } else {
                const button = document.getElementById('featureItem-ht-dark');
                button.classList.remove(prefix + 'feature-active')
            }
            if (settings.invert) {
                document.documentElement.classList.toggle(prefix + "invert-colors");
                const button = document.getElementById('featureItem-ic');
                button.classList.add(prefix + 'feature-active')
            } else {
                const button = document.getElementById('featureItem-ic');
                button.classList.remove(prefix + 'feature-active')
            }
            if (settings.cursorChanged) {
                document.body.classList.add(prefix + 'custom-cursor');
                const button = document.getElementById('featureItem-Cursor');
                button.classList.add(prefix + 'feature-active')
            } else {
                const button = document.getElementById('featureItem-Cursor');
                button.classList.remove(prefix + 'feature-active')
            }
            if (settings.adhdFriendly) {
                toggleADHDFriendlyMode();
                readingMask.classList.remove('mask-hidden');
                const button = document.getElementById('featureItem-adhd');
                button.classList.add(prefix + 'feature-active');
            }
            else {
                readingMask.classList.add('mask-hidden');
                const button = document.getElementById('featureItem-adhd');
                button.classList.remove(prefix + 'feature-active');
            }
            applyTextSettings()

            if (saturateCount > 0) {
                const button = document.getElementById('featureItem-saturate');
                button.classList.add(prefix + 'feature-active');
                if (saturateCount === 1) {
                    document.documentElement.classList.add(prefix + 'saturate-low');
                } else if (saturateCount === 2) {
                    document.documentElement.classList.add(prefix + 'saturate-high');
                } else if (saturateCount === 3) {
                    document.documentElement.classList.add(prefix + 'saturate-desaturate');
                }

                for (let i = 0; i <= 3; i++) {
                    const textElement = document.getElementById(`saturate-text-${i}`);
                    const detailTextElement = document.getElementById(`saturate-detail-text-${i}`);
                    if (textElement) {
                        textElement.style.display = i === saturateCount ? 'inline' : 'none';
                    }
                    if (detailTextElement) {
                        detailTextElement.style.display = i === saturateCount ? 'inline' : 'none';
                    }
                }

                const saturateCheck = document.getElementById('featureStepsSaturate');
                saturateCheck.classList.add(prefix + 'featureSteps-visible');
                saturateSpans.forEach((span, index) => {
                    if (index <= saturateCount - 1) {
                        span.classList.add(prefix + 'active');
                    }
                });
            }
            else {
                const button = document.getElementById('featureItem-saturate');
                button.classList.remove(prefix + 'feature-active');
                document.documentElement.classList.remove(prefix + 'saturate-low');
                document.documentElement.classList.remove(prefix + 'saturate-high');
                document.documentElement.classList.remove(prefix + 'saturate-desaturate');
                const saturateCheck = document.getElementById('featureStepsSaturate');
                saturateCheck.classList.remove(prefix + 'featureSteps-visible');
                saturateSpans.forEach((span, index) => {
                    if (index <= saturateCount - 1) {
                        span.classList.remove(prefix + 'active');
                    }
                });

            }

            updateWidgetToggles(settings)
        }
    }

    const modal = document.getElementById("sa-main");

    // Toggle modal on Ctrl + F2
    document.addEventListener("keydown", function (e) {
        if (e.ctrlKey && e.key === "F2") {
            e.preventDefault();
            const currentRight = window.getComputedStyle(modal).right;
            if (currentRight === "0px") {
                modal.style.right = "-530px";
            } else {
                modal.style.right = "0";
            }
        }
    });

    function detectRouteChange() {
        setInterval(() => {
            let currentPath = window.location.pathname;
            if (currentPath !== lastPath) {
                // console.log('Route changed from', lastPath, 'to', currentPath);
                lastPath = currentPath;
                const settingsStr = getCookie(SETTINGS_KEY);
                if (settingsStr) {
                    const settings = JSON.parse(settingsStr);
                    if (settings.adhdFriendly && !adhdActive) {
                        toggleADHDFriendlyMode()
                    }         
                }
            }
        }, 1000);
    }
})()

window.addEventListener("load", function () {
    document.body.classList.remove("accessibility-loading");
});

/*
document.getElementById('accessibility-button').onclick = function() {
    document.getElementById("sa-widget-custom-trigger").click();
}
*/

