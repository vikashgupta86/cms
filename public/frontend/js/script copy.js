
const socialTool = document.getElementById('socialTool');
const socialBtn = document.getElementById('socialBtn');
const panelLinks = socialTool.querySelectorAll('.social-panel a');

let isLocked = false;

function openDropdown(lock = false) {
  socialTool.classList.add('open');
  socialBtn.setAttribute('aria-expanded', 'true');
  if (lock) isLocked = true;
}

function closeDropdown(force = false) {
  if (!isLocked || force) {
    socialTool.classList.remove('open');
    socialBtn.setAttribute('aria-expanded', 'false');
    isLocked = false;
  }
}

socialTool.addEventListener('mouseenter', () => {
  if (!isLocked) openDropdown();
});
socialTool.addEventListener('mouseleave', () => {
  if (!isLocked) closeDropdown();
});
socialBtn.addEventListener('click', e => {
  e.preventDefault();
  e.stopPropagation();
  openDropdown(true);
});
socialBtn.addEventListener('focus', () => {
  if (!isLocked) openDropdown();
});
socialBtn.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    e.preventDefault();
    openDropdown(true);
    panelLinks[0]?.focus();
  }
  if (e.key === 'Escape') {
    e.preventDefault();
    closeDropdown(true);
  }
});
socialBtn.addEventListener('keyup', e => {
  if (e.key === ' ') {
    e.preventDefault();
    openDropdown(true);
    panelLinks[0]?.focus();
  }
});
panelLinks.forEach(link => {
  link.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      e.preventDefault();
      closeDropdown(true);
      socialBtn.focus();
    }
  });
});
socialTool.addEventListener('focusout', () => {
  requestAnimationFrame(() => {
    if (!socialTool.contains(document.activeElement) && !isLocked) {
      closeDropdown();
    }
  });
});
document.addEventListener('click', e => {
  if (!socialTool.contains(e.target)) {
    closeDropdown(true);
  }
});
</script>



