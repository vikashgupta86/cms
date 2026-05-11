
const socialTool = document.getElementById('socialTool');
const socialBtn = document.getElementById('socialBtn');

// handle the case where elements might not be found
const panelLinks = socialTool ? socialTool.querySelectorAll('.social-panel a') : [];
// const panelLinks = socialTool.querySelectorAll('.social-panel a') ?? [];

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

if (panelLinks && socialTool && socialBtn) {
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

}


function goBack() {
    // Navigates to the previous page in the browser history
    window.history.back();
}


function printSpecificContent(elementId) {
    const element = document.getElementById(elementId);
    
    if (!element) {
        console.error("Print Error: Element with ID '" + elementId + "' not found.");
        alert("Content area not found for printing.");
        return;
    }

    const content = element.innerHTML;
    const pri = document.createElement('iframe');
    pri.style.position = 'absolute';
    pri.style.top = '-1000px';
    document.body.appendChild(pri);
    
    const priDoc = pri.contentWindow.document;
    priDoc.open();
    priDoc.write(`<html><head><title>Print</title><style>body{font-family:sans-serif;padding:20px;}</style></head><body>${content}</body></html>`);
    priDoc.close();
    
    pri.contentWindow.focus();
    pri.contentWindow.print();
    
    setTimeout(() => { document.body.removeChild(pri); }, 1000);
}


document.addEventListener("DOMContentLoaded", function() {
    const cmsContainer = document.querySelector('.cms-content');
    if (cmsContainer) {
        $('.cms-content').addClass('table-responsive');
        const cmsTables = cmsContainer.querySelectorAll('table');
        cmsTables.forEach(function(table) {
            table.setAttribute('height', '0');
           // table.classList.remove('table', 'table-bordered', 'table-striped', 'table-hover', 'text-dark1');
            table.removeAttribute('style'); 
            table.classList.add('table', 'table-bordered', 'table-striped', 'table-hover', 'text-dark1');

          const header = table.querySelector('thead');
          if (header) {
              header.removeAttribute('style');
              header.style.backgroundColor = '#1f5692';
              header.style.color = '#fff!important';
              header.classList.add('text-white');



  // we found the thead element but it doesn't contain any th elements, so we need to check the first row of the table for th elements and covert the td to th and apply the header styles to them
              if (thElements.length === 0) {
                  const firstRow = table.querySelector('tr');
                  if (firstRow) {
                      const tdElements = firstRow.querySelectorAll('td');
                      tdElements.forEach(function(td) {
                          const th = document.createElement('th');
                          th.innerHTML = td.innerHTML;
                          th.style.backgroundColor = '#1f5692';
                          th.classList.add('text-white');
                          td.parentNode.replaceChild(th, td);
                      });
                  }
                  

              const thElements = table.querySelectorAll('th');
              thElements.forEach(function(th) {
                  th.style.backgroundColor = '#1f5692';
                  th.classList.add('text-white');
              });


          }


            const headerCells = table.querySelectorAll('tr:first-child th, tr:first-child td');
            headerCells.forEach(function(cell, index) {
                if (cell.textContent.toLowerCase().includes('date')) {
                    const rows = table.querySelectorAll('tr');
                    rows.forEach(function(row) {
                        const cells = row.querySelectorAll('td, th');
                        if (cells[index]) {
                            cells[index].style.whiteSpace = 'nowrap';
                        }
                    });
                }
              }); 

              // add style class on the links in the table
              const links = table.querySelectorAll('a');
              links.forEach(function(link) {
                  link.classList.add('text-success', 'fw-bold', 'text-decoration-none');
                  link.addEventListener('mouseover', function() {
                      link.classList.add('text-decoration-underline');
                  });
                  link.addEventListener('mouseout', function() {
                      link.classList.remove('text-decoration-underline');
                  });

                  const linkIcon = document.createElement('span');
                  linkIcon.classList.add('mx-1', 'bi', 'bi-box-arrow-up-right');
                  link.appendChild(linkIcon);                                       

              });


        });
    }
});
